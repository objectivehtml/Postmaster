<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Postmaster Library
 * 
 * @package		Postmaster
 * @subpackage	Libraries
 * @author		Justin Kimbrell
 * @copyright	Copyright (c) 2012, Objective HTML
 * @link 		http://www.objectivehtml.com/postmaster
 * @version		1.1.2
 * @build		20121014
 */

require_once APPPATH.'libraries/Template.php';
require_once PATH_THIRD.'/postmaster/config/postmaster_constants.php';

class Postmaster_lib {
	
	public $service_suffix = '_postmaster_service';
	public $model;
	
	public function __construct()
	{
		$this->EE =& get_instance();
			
		$this->EE->load->model('postmaster_model');
		$this->EE->load->driver('channel_data');
		$this->EE->load->helper('postmaster_helper');
		$this->EE->lang->loadfile('postmaster');
		
		$this->model = $this->EE->postmaster_model;
	}
	
	public function trigger_hook($hook, $args)
	{
		$this->EE->load->library('postmaster_hook', array(
			'base_path' => PATH_THIRD.'postmaster/hooks/'
		));
		
		if(!empty($hook))
		{
			return $this->EE->postmaster_hook->trigger($hook, $args);
		}			
	}
	
	public function get_themes()
	{
		$this->EE->load->helper('directory');

		$directory = $this->EE->theme_loader->theme_path().'postmaster/css/themes/';

		$themes = array();

		foreach(directory_map($directory) as $theme)
		{
			$name = str_replace('.css', '', $theme);

			$this->EE->theme_loader->css('themes/'.$name);

			$themes[] = (object) array(
				'value' => $name,
				'name'  => ucfirst($name)
			);
		}

		return $themes;
	}
	
	public function get_send_date($parsed_object)
	{
		$parsed_object = $this->convert_array($parsed_object);
		$send_date     = $parsed_object->post_date_specific;
		$send_date     = !empty($send_date) ? $this->EE->localize->set_localized_time(strtotime($send_date)) : $this->EE->localize->now;

		if(!empty($parsed_object->post_date_relative))
		{
			$send_date = strtotime($parsed_object->post_date_relative, $send_date);
		}
		
		return $send_date;
	}

	public function load_service($name)
	{
		require_once PATH_THIRD . 'postmaster/libraries/Postmaster_service.php';
		require_once PATH_THIRD . 'postmaster/services/'.ucfirst(strtolower($name)).'.php';

		$class = $name.$this->service_suffix;

		return new $class;
	}
	
	public function parse($parcel, $member_id = FALSE, $parse_vars = array(), $prefix = 'parcel', $delimeter = ':')
	{
		$parcel_copy    = clone $parcel;
		$channel_id     = isset($parcel->entry->channel_id) ? $parcel->entry->channel_id : 0;
		$channels       = $this->EE->postmaster_model->get_channels();
		$channel_fields = $this->EE->postmaster_model->get_channel_fields($channel_id);
		
		if(isset($parcel->entry))
		{
			if(isset($parcel->entry->entry_id))
			{
				$entry = $this->EE->channel_data->get_channel_entry($parcel_copy->entry->entry_id, '*')->row_array();
				$entry_vars = $this->EE->channel_data->utility->add_prefix($prefix, $entry, $delimeter);
			}
			else
			{
				$entry_vars = $parcel->entry;
			}
			
			foreach($entry_vars as $var => $value)
			{
				$field_name  = preg_replace('/^'.$prefix.$delimeter.'|:.*/us', '',  $var);	
							
				if(!isset($channel_fields[$field_name]) && !preg_match("/^field_[a-z]{2}_\d$/", $field_name))
				{
					$parse_vars[$var] = $value;
				}
			}
			
		}
		
		$parse_vars = array_merge($parse_vars, $this->EE->postmaster_model->get_member($member_id, 'member'));
		
		$entry  = $parcel_copy->entry;
		unset($parcel_copy->entry);
		
		return $this->convert_array($this->EE->channel_data->tmpl->parse_array($parcel_copy, $parse_vars, $entry_vars, $channels, $channel_fields, $prefix.$delimeter));
	}
	
	public function append($obj, $prop, $value)
	{
		$return_obj = is_object($obj) ? TRUE : FALSE;
		$obj 		= (array) $obj;
		$obj[$prop] = $value;
		
		if($return_obj)
		{
			$obj = $this->convert_array($obj);
		}
		
		return $obj;
	}
	
	private function convert_array($obj = array())
	{
		if(is_array($obj))
		{
			$obj = (object) $obj;
		}
		
		return $obj;
	}

	public function send($parsed_object, $parcel, $ignore_date = FALSE)
	{
		$parsed_object = $this->convert_array($parsed_object);
		$parcel        = $this->convert_array($parcel);
		
		$service   = $this->load_service($parcel->service);
		$send_date = $this->get_send_date($parsed_object);

		if($this->validate_emails($parsed_object->to_email))
		{
			if($ignore_date || $send_date <= $this->EE->localize->now)
			{
				$response = $service->send($parsed_object, $parcel, TRUE);
				$this->model->save_response($response);

				if(!empty($parsed_object->send_every))
				{
					$gmt_date = $this->EE->localize->set_localized_time(strtotime($parsed_object->send_every, $this->EE->localize->now));
					$this->model->add_to_queue($parsed_object, $parcel, $gmt_date);
				}
				
				return $response;
			}
			else
			{
				$this->model->add_to_queue($parsed_object, $parcel);
			}
		}
		else
		{
			$this->model->unsubscribe($parsed_object->to_email);
		}
		
		return FALSE;
	}
	
	public function send_from_queue($row)
	{
		$parcel           = $this->model->get_parcel($row->parcel_id);
		$parcel->entry    = $this->model->get_entry($row->entry_id);
		$parcel->settings = json_decode($parcel->settings);

		$parsed_object = $this->parse($parcel);

		$this->send($parsed_object, $parcel, TRUE);
		$this->model->remove_from_queue($row->id);
	}

	public function validate_channel_entry($entry_id, $meta, $data)
	{
		$this->EE->TMPL = new EE_Template();

		$parcels = $this->model->get_parcels();

		foreach($parcels as $index => $parcel)
		{
			$entry_data = isset($data['revision_post']) ? $data['revision_post'] : $data;

			if($parcel->channel_id == $data['channel_id'])
			{
				$entry_data['category'] = isset($entry_data['category']) ? $entry_data['category'] : array();

				if($this->validate_trigger($parcel->trigger))
				{
					if($this->validate_categories($entry_data['category'], $parcel->categories))
					{
						if($this->validate_member($meta['author_id'], $parcel->member_groups))
						{
							if($this->validate_status($meta['status'], $parcel->statuses))
							{		
								$entry  = $this->EE->channel_data->get_channel_entry($entry_id)->row();
								$parcel = $this->append(&$parcel, 'entry', $entry);
							
								
								$member_id = FALSE;
								
								if(isset($parcel->entry->author_id))
								{
									$member_id = $parcel->entry->author_id;
								}
								
								$parsed_object = $this->parse($parcel, $member_id);
						
								$parsed_object->settings = $parcels[$index]->settings;

								if($this->validate_conditionals($parsed_object->extra_conditionals))
								{
									$this->send($parsed_object, $parcel);
								}
							}
						}
					}
				}
			}
		}
	}

	public function validate_conditionals($extra_conditionals)
	{
		$extra_conditionals = trim(strtoupper($extra_conditionals));

		if(empty($extra_conditionals) || $extra_conditionals == 'TRUE')
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	public function validate_categories($subject, $valid_categories)
	{
		$valid = 0;

		foreach($valid_categories as $category)
		{
			if(count($subject) > 0 && in_array($category->cat_id, $subject))
			{
				$valid++;
			}
		}
		
		return count($valid_categories) == $valid ? TRUE : FALSE;
	}

	public function validate_email($emails = '')
	{
		if(!is_array($emails))
		{
			$emails = array($emails);
		}

		foreach($emails as $email)
		{
			$this->EE->db->or_where('email', $email);
		}

		$this->EE->db->from('postmaster_blacklist');

		if($this->EE->db->count_all_results() == 0)
		{
			return TRUE;
		}

		return FALSE;
	}

	public function validate_emails($emails)
	{
		$emails = explode(',', $emails);

		foreach($emails as $index => $email)
		{
			$emails[$index] = trim($email);
		}

		return $this->validate_email($emails);
	}

	public function validate_member($subject, $valid_members)
	{
		$valid  = FALSE;
		$member = $this->EE->channel_data->get_member($subject)->row();

		foreach($valid_members as $member)
		{
			if($member->group_id == $member->group_id)
			{
				$valid = TRUE;
			}
		}

		return $valid;
	}
	
	public function validate_status($subject, $statuses)
	{
		$valid = FALSE;

		foreach($statuses as $status)
		{
			if($subject == $status)
			{
				$valid = TRUE;
			}
		}

		return $valid;
	}

	public function validate_trigger($triggers)
	{
		if(is_string($triggers))
		{
			$triggers = explode('|', $triggers);
		}

		$entry_trigger = $this->EE->session->cache('postmaster', 'entry_trigger');

		$valid  	   = FALSE;

		if(in_array($entry_trigger, $triggers))
		{
			$valid = TRUE;
		}

		return $valid;
	}

	public function cp_url($method = 'index', $useAmp = FALSE)
	{
		if(!defined('BASE'))
		{
			define('BASE', '');
		}

		$amp  = !$useAmp ? AMP : '&';

		$file = substr(BASE, 0, strpos(BASE, '?'));
		$file = str_replace($file, '', $_SERVER['PHP_SELF']) . BASE;

		$url  = $file .$amp. '&C=addons_modules' .$amp . 'M=show_module_cp' . $amp . 'module=postmaster' . $amp . 'method=' . $method;

		return str_replace(AMP, $amp, $url);
	}	
	
	public function current_url($append = '', $value = '')
	{
		$http = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
		
		$port = $_SERVER['SERVER_PORT'] == '80' || $_SERVER['SERVER_PORT'] == '443' ? NULL : ':' . $_SERVER['SERVER_PORT'];
		
		if(!isset($_SERVER['SCRIPT_URI']))
		{				
			 $_SERVER['SCRIPT_URI'] = $http . $_SERVER['HTTP_HOST']. $_SERVER['REQUEST_URI'];
		}
		
		$base_url = $http . $_SERVER['HTTP_HOST'] . '/' . config_item('site_index');
		
		if(!empty($append))
		{
			$base_url .= '?'.$append.'='.$value;
		}
		
		return $base_url;
	}
}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Postmaster Library
 * 
 * @package		Postmaster
 * @subpackage	Libraries
 * @author		Justin Kimbrell
 * @copyright	Copyright (c) 2012, Objective HTML
 * @link 		http://www.objectivehtml.com/postmaster
 * @version		1.1.1
 * @build		20120801
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
		if(isset($parcel->entry))
		{
			$entry = array();
			
			if(isset($parcel->entry->entry_id))
			{
				$entry = $this->EE->channel_data->get_channel_entry($parcel->entry->entry_id, '*')->row_array();
			}
			
			$entry_vars = array_merge((array) $parcel->entry, $entry);
			$parse_vars = array_merge($parse_vars, $entry_vars);
		}
	
		$parse_vars = array_merge($parse_vars, $this->EE->postmaster_model->get_member($member_id, 'member'));
	
		$channel_id     = isset($parcel->entry->channel_id) ? $parcel->entry->channel_id : 0;
		$channels       = $this->EE->postmaster_model->get_channels();
		$channel_fields = $this->EE->postmaster_model->get_channel_fields($channel_id);
		
		unset($parcel->entry);
		
		$parcel = $this->EE->channel_data->tmpl->parse_array($parcel, $parse_vars, $entry_vars, $channels, $channel_fields, $prefix.$delimeter);
		
		/*
		foreach($parcel as $field => $value)
		{
			if(is_string($value))
			{
				$parcel->$field = $this->EE->channel_data->tmpl->parse_string($value, $parse_vars, $parcel->entry, $channels, $channel_fields);
			}
		}*/
		
		return (object) $parcel;
		
		/*
		
		if(!isset($this->EE->TMPL))
		{
			require_once APPPATH.'/libraries/Template.php';
		}
		
		$this->EE->load->library('typography');
		$this->EE->load->library('api');
		$this->EE->api->instantiate('channel_fields');

		$fields = $this->EE->api_channel_fields->fetch_custom_channel_fields();

		//var_dump($fields);exit();

		$entry  = (array) $parcel->entry;
		
		
		$member = $this->EE->channel_data->get_member(isset($entry['author_id']) ? $entry['author_id'] : 0)->row_array();
		$member = $this->EE->channel_data->utility->add_prefix('member', $member);
		
		foreach(array('password', 'salt', 'crypt_key', 'unique_id') as $var)
		{
			unset($member[$var]);
		}

		$fields = array(
			'to_name',
			'to_email',
			'from_name',
			'from_email',
			'cc',
			'bcc',
			'subject',
			'message',
			'post_date_specific',
			'post_date_relative',
			'send_every',
			'extra_conditionals'
		);

		$parse_object = (object) array();

		$this->EE->load->library('api');
		$this->EE->api->instantiate('channel_fields');

		//$installed_fieldtypes = $this->EE->api_channel_fields->fetch_installed_fieldtypes();
		if(isset($parcel->entry->channel_id))
		{
			$channel_fields 	  = $this->EE->channel_data->get_channel_fields($parcel->entry->channel_id)->result();
	
			foreach($channel_fields as $index => $field)
			{
				$channel_fields[$field->field_name] = $field;
				unset($channel_fields[$index]);
			}
		}
		
		foreach($fields as $field)
		{
			if(!empty($parcel->$field))
			{
				$this->EE->TMPL = new EE_Template();
				$this->EE->TMPL->template = $parcel->$field;					
				$this->EE->TMPL->template = $this->EE->TMPL->parse_globals($this->EE->TMPL->template);
					
				$vars = $this->EE->functions->assign_variables($this->EE->TMPL->template);

				foreach($vars['var_single'] as $single_var)
				{
					$params = $this->EE->functions->assign_parameters($single_var);

					$single_var_array = explode(' ', $single_var);
					
					$field_name = str_replace('parcel:', '', $single_var_array[0]);
				
					$entry = FALSE;

					if(isset($channel_fields[$field_name]))
					{
						$field_type = $channel_fields[$field_name]->field_type;
						$field_id   = $channel_fields[$field_name]->field_id;
						$data       = $parcel->entry->$field_name;

						if($this->EE->api_channel_fields->setup_handler($field_id))
						{
							$this->EE->db->select('*');
							$this->EE->db->join('channel_titles', 'channel_titles.entry_id = channel_data.entry_id');
							$this->EE->db->join('channels', 'channel_data.channel_id = channels.channel_id');
							$row = $this->EE->db->get_where('channel_data', array('channel_data.entry_id' => $parcel->entry->entry_id))->row_array();
					
							$this->EE->api_channel_fields->apply('_init', array(array('row' => $row)));

							// Preprocess
							$data = $this->EE->api_channel_fields->apply('pre_process', array($row['field_id_'.$field_id]));

							$entry = $this->EE->api_channel_fields->apply('replace_tag', array($data, $params, FALSE));

							$this->EE->TMPL->template = $this->EE->TMPL->swap_var_single($single_var, $entry, $this->EE->TMPL->template );
						}
					}
				}

				$pair_vars = array();

				foreach($vars['var_pair'] as $pair_var => $params)
				{
					$pair_var_array = explode(' ', $pair_var);
					
					$field_name = str_replace('parcel:', '', $pair_var_array[0]);
					$offset = 0;

					while (($end = strpos($this->EE->TMPL->template, LD.'/parcel:'.$field_name.RD, $offset)) !== FALSE)
					{
						if (preg_match("/".LD."parcel:{$field_name}(.*?)".RD."(.*?)".LD.'\/parcel:'.$field_name.RD."/s", $this->EE->TMPL->template, $matches, 0, $offset))
						{
							$chunk  = $matches[0];
							$params = $matches[1];
							$inner  = $matches[2];

							// We might've sandwiched a single tag - no good, check again (:sigh:)
							if ((strpos($chunk, LD.$field_name, 1) !== FALSE) && preg_match_all("/".LD."parcel:{$field_name}(.*?)".RD."/s", $chunk, $match))
							{
								// Let's start at the end
								$idx = count($match[0]) - 1;
								$tag = $match[0][$idx];
								
								// Reassign the parameter
								$params = $match[1][$idx];

								// Cut the chunk at the last opening tag (PHP5 could do this with strrpos :-( )
								while (strpos($chunk, $tag, 1) !== FALSE)
								{
									$chunk = substr($chunk, 1);
									$chunk = strstr($chunk, LD.$field_name);
									$inner = substr($chunk, strlen($tag), -strlen(LD.'/'.$field_name.RD));
								}
							}
							
							$pair_vars[$field_name] = array($inner, $this->EE->functions->assign_parameters($params), $chunk);
						}
						
						$offset = $end + 1;
					}

					foreach($pair_vars as $field_name => $pair_var)
					{																
						if(isset($channel_fields[$field_name]))
						{
							$field_type = $channel_fields[$field_name]->field_type;
							$field_id   = $channel_fields[$field_name]->field_id;

							$data       = $parcel->entry->$field_name;

							if($this->EE->api_channel_fields->setup_handler($field_id))
							{
								$entry = $this->EE->api_channel_fields->apply('replace_tag', array($data, $pair_var[1], $pair_var[0]));

								$this->EE->TMPL->template = str_replace($pair_var[2], $entry, $this->EE->TMPL->template);
							}
						}
					}

					$entry = FALSE;
				}

				$entry  = $this->EE->channel_data->utility->add_prefix('parcel', $parcel->entry);

				$entry  = array_merge($member, $entry);
				
				$entry['parcel:author'] = isset($member['member:screen_name']) ? $member['member:screen_name'] : isset($member['member:username']) ? $member['member:username'] : '';
				
				$this->EE->TMPL->template = $this->EE->TMPL->parse_variables_row($this->EE->TMPL->template, $entry);
				$this->EE->TMPL->parse($this->EE->TMPL->template);
				
				$parcel->$field 	  = $this->EE->TMPL->template;
				$parse_object->$field = $parcel->$field;	
			}
			else
			{
				$parse_object->$field = $parcel->$field;
			}
		}
	
		return $parse_object;
		*/
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
				$response = $service->send($parsed_object, $parcel);
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
								$parcel->entry = $this->EE->channel_data->get_channel_entry($entry_id)->row();
								
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
		
		$base_url = $http . $_SERVER['HTTP_HOST'];
		
		if(!empty($append))
		{
			$base_url .= '?'.$append.'='.$value;
		}
		
		return $base_url;
	}
}
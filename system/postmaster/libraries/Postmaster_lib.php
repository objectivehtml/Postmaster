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
	
	/**
	 * Postmaster Email Service Suffix
	 * 
	 * @var string
	 */
	 
	public $service_suffix = '_postmaster_service';
	
	
	/**
	 * Postmaster Model
	 * 
	 * @var string
	 */
	 
	public $model;
	
	
	/**
	 * Load all the dependent scripts
	 *
	 * @access	public
	 * @return	null
	 */
	
	public function __construct()
	{
		$this->EE =& get_instance();
			
		$this->EE->load->config('postmaster_config');
		$this->EE->load->model('postmaster_model');
		$this->EE->load->driver('channel_data');
		$this->EE->load->helper('postmaster_helper');
		$this->EE->lang->loadfile('postmaster');
		
		$this->model = $this->EE->postmaster_model;
	}
	
	
	/**
	 * Append property to an object
	 *
	 * @access	public
	 * @param	object    	The object to append to the new prop
	 * @param	string    	The name of the property to append
	 * @param	string    	The value of the appended property
	 * @return	object
	 */
	
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
	
	
	/**
	 * Convert an array to an object
	 *
	 * @access	public
	 * @param	array     	The array to convert
	 * @return	object
	 */
	
	private function convert_array($obj = array())
	{
		if(is_array($obj))
		{
			$obj = (object) $obj;
		}
		
		return $obj;
	}

	
	/**
	 * Create and return a failed email service response
	 *
	 * @access	public
	 * @param	array     	An associative array used to set properties
	 * @return	object
	 */
	 
	public function failed_response($props = array())
	{
		$default_props = array(
			'status' => FALSE
		);
		
		$props = array_merge($default_props, $props);
		
		require_once(PATH_THIRD.'postmaster/libraries/Base_service.php');
		
		return new Postmaster_Service_Response($props);
	}
	
	
	/**
	 * Get the available themes
	 *
	 * @access	public
	 * @return	array
	 */
	 
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
	
	
	/**
	 * Get the send_date from a parsed object
	 *
	 * @access	public
	 * @param	object     	The parsed object
	 * @return	int;
	 */
	 
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
	
	
	/**
	 * Load the specified email service
	 *
	 * @access	public
	 * @param	string     	The name of the service to load
	 * @return	object
	 */
	 
	public function load_service($name)
	{
		require_once PATH_THIRD . 'postmaster/libraries/Base_service.php';
		require_once PATH_THIRD . 'postmaster/services/'.ucfirst(strtolower($name)).'.php';

		$class = $name.$this->service_suffix;

		return new $class;
	}
	
	
	/**
	 * Parse a parcel
	 *
	 * @access	public
	 * @param	object 		The parcel object to parse
	 * @param	mixed 		Specify a member id to override the default
	 * @param	array 		An associative array of additional vars to parse
	 * @param	string 		A prefix
	 * @param	string 		A prefix delimeter
	 * @return	object
	 */
	 
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
	
	
	/**
	 * Send a parsed object with the specified email service
	 *
	 * @access	public
	 * @param	object	Parsed object
	 * @param	object	The parcel object
	 * @param	object	Optionally override to ignore the date validation
	 * @return	mixed
	 */
	 
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
				
				if($response->status == 'success')
				{
					$this->log_action('An email was successfully sent to "'.(!empty($parsed_object->to_email) ? $parsed_object->to_email : 'N/A').'".');
				}
				else
				{					
					$this->log_action('An email was failed to send to "'.(!empty($parsed_object->to_email) ? $parsed_object->to_email : 'N/A').'".');
				}
				
				$this->model->save_response($response);

				if(!empty($parsed_object->send_every))
				{
					$gmt_date = $this->EE->localize->set_localized_time(strtotime($parsed_object->send_every, $this->EE->localize->now));
					
					$this->log_action('The email to "'.$parsed_object->to_email.'" is set to be sent every "'.$parsed_object->send_every.'". The next time it will be sent will be '.date('Y-m-d H:i', $send_date).'.');
				
					$this->model->add_to_queue($parsed_object, $parcel, $gmt_date);
				}
				
				return $response;
			}
			else
			{
				$this->log_action('The email to "'.$parsed_object->to_email.'" has been added to the queue and is set to be sent at '.date('Y-m-d H:i', $send_date).'.');
				$this->model->add_to_queue($parsed_object, $parcel);
			}
		}
		else
		{
			$this->log_action('"'.$parsed_object->to_email.'" is not a valid email. It has been removed from the queue.');		
			$this->model->unsubscribe($parsed_object->to_email);
		}
		
		return FALSE;
	}
	
	
	/**
	 * Send email from queue
	 *
	 * @access	public
	 * @param	object	The row object from the saved record in the queue 
	 * @return	NULL
	 */
	
	public function send_from_queue($row)
	{
		$parcel           = $this->model->get_parcel($row->parcel_id);
		$parcel->entry    = $this->model->get_entry($row->entry_id);
		$parcel->settings = json_decode($parcel->settings);

		$parsed_object = $this->parse($parcel);

		$this->model->remove_from_queue($row->id);		
		$this->send($parsed_object, $parcel, TRUE);
	}


	/**
	 * Convenience method to trigger the specific hook.
	 *
	 * @access	public
	 * @param	string 	The name of the hook to call
	 * @param 	array	An array of arguments used to call the hook
	 * @return	
	 */
	 
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
	
	
	/**
	 * Validate a channel entry from a given entry_id and send email
	 *
	 * @access	public
	 * @param	int		A valid entry_id
	 * @param	array	An array of entry meta data
	 * @param	array	An array of entry data
	 * @return	null
	 */
	public function validate_channel_entry($entry_id, $meta, $data)
	{
		$this->EE->TMPL = new EE_Template();
		
		$parcels = $this->model->get_parcels(array(
			'where' => array(
				'site_id' => $meta['site_id']	
			)
		));
		
		$this->log_action('Entry '.$entry_id.' validation has started. There '.(count($parcels) == 1 ? 'is' : 'are').'  '.count($parcels).' parcel'.(count($parcels) > 1 ? 's' : NULL).' to validate.');

		foreach($parcels as $index => $parcel)
		{
			$entry_data = isset($data['revision_post']) ? $data['revision_post'] : $data;

			//$this->log_action('Entry '.$entry_id.' validation has started. There are '.count($parcels).' parcels to validate.');

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
								$parcel = $this->append($parcel, 'entry', $entry);							
								
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
			else
			{				
				$this->log_action('The "'.$parcel->title.'" parcel does not have a valid channel_id, which is "'.$data['channel_id'].'"');
			}
		}
	}

	
	/**
	 * Validate a parsed conditional string 
	 *
	 * @access	public
	 * @param	string 	A parsed string
	 * @return	bool
	 */
	 
	public function validate_conditionals($extra_conditionals)
	{
		$extra_conditionals = trim(strtoupper($extra_conditionals));

		if(empty($extra_conditionals) || $extra_conditionals == 'TRUE')
		{
			//$this->log_action('The parcel has valid extra conditions.');				
			return TRUE;
		}
		else
		{
			$this->log_action('The parcel does not have valid extra conditions.');
			return FALSE;
		}
	}

	/**
	 * Validate a subject array against categories 
	 *
	 * @access	public
	 * @param	string 	A parsed string
	 * @return	bool
	 */
	 
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
		
		if(count($valid_categories) == $valid)
		{
			$valid = TRUE;			
			//$this->log_action('The parcel has a valid category.');			
		}
		else
		{		
			$valid = FALSE;
			$this->log_action('The parcel does not have a valid category.');
		}

		return $valid;
	}
	
	/**
	 * Validate an email address against the blacklist
	 *
	 * @access	public
	 * @param	string 	A valid email address
	 * @return	bool
	 */
	 
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

	/**
	 * Validate multiple email addresses against the blacklist
	 *
	 * @access	public
	 * @param	mixed 	A comma delimited string or an array of emails
	 * @return	bool
	 */
	 
	public function validate_emails($emails)
	{
		if(is_string($emails))
		{
			$emails = explode(',', $emails);
		}
		
		foreach($emails as $index => $email)
		{
			$emails[$index] = trim($email);
		}

		return $this->validate_email($emails);
	}


	/**
	 * Validate an member against the valid groups
	 *
	 * @access	public
	 * @param	int		A valid member_id
	 * @param	array	An array of valid member groups
	 * @return	bool
	 */
	 
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
		
		if($valid)
		{
			//$this->log_action('The parcel has a valid author of "'.$subject.'".');			
		}
		else
		{
			$this->log_action('The parcel does not have a valid author, which has a member_id of '.$subject.'.');
		}

		return $valid;
	}
	
	
	/**
	 * Validate a status against the valid statuses
	 *
	 * @access	public
	 * @param	int		A status
	 * @param	array	An array of valid statuses
	 * @return	bool
	 */
	 
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

		// If no status is defined and entry has no status, return TRUE
		if(count($statuses) == 0 && empty($subject))
		{
			$valid = TRUE;
		}
		
		if($valid)
		{
			//$this->log_action('The parcel has a valid status of "'.$subject.'".');
		}
		else
		{
			$this->log_action('The parcel does not have a valid status, which is "'.$subject.'".');
		}
		
		return $valid;
	}


	/**
	 * Validates a specified trigger
	 *
	 * @access	public
	 * @param	mixed	And array or delimeted string of triggers
	 * @return	bool
	 */
	 
	public function validate_trigger($triggers)
	{
		if(is_string($triggers))
		{
			$triggers = explode('|', $triggers);
		}

		$entry_trigger = $this->EE->session->cache('postmaster', 'entry_trigger');

		if(in_array($entry_trigger, $triggers))
		{
			$valid = TRUE;
			//$this->log_action('The parcel has a valid entry trigger.');		
		}
		else
		{
			$valid = FALSE;
			$this->log_action('The parcel does not have a valid entry trigger.');	
		}
		
		return $valid;
	}	
	
		
	/**
	 * Log Action
	 *
	 * @access	public
	 * @param	string  The string to log
	 * @return	mixed
	 */
	
	public function log_action($str)
	{
		if(config_item('postmaster_debug'))
		{
			$this->EE->load->library('logger');
			$this->EE->logger->log_action($str);
		}
	}
	
	/**
	 * Return a CP url
	 *
	 * @access	public
	 * @param	string 	A valid method name
	 * @param	bool 	Encode amperands?
	 * @return	string
	 */
	 
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
	
	
	/**
	 * Returns the current_url
	 *
	 * @access	public
	 * @param	string 	Append a variable to the URL
	 * @param	string 	Append a value to the URL
	 * @return	string
	 */
	 
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
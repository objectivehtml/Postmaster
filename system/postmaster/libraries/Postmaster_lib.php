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

		$send_date     = !empty($send_date) ? $this->strtotime($send_date) : $this->now();
		
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
		/* Fix 9/18/13 - Typography hack to prevent encoding URL's in emails */
		
		$current_method = $this->EE->input->get('M');
		
		if(REQ == 'CP' && $current_method)
		{
			$_GET['M'] = 'send_email';
		}

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
		
		$parse_vars[$prefix.$delimeter.'safecracker'] 		  = isset($this->EE->safecracker) ? TRUE : FALSE;
		$parse_vars[$prefix.$delimeter.'channel_form'] 		  = isset($this->EE->channel_form) ? TRUE : FALSE;
		$parse_vars[$prefix.$delimeter.'logged_in_group_id']  = $this->EE->session->userdata('group_id');
		$parse_vars[$prefix.$delimeter.'logged_in_member_id'] = $this->EE->session->userdata('member_id');
		$parse_vars[$prefix.$delimeter.'current_time'] 		  = $this->EE->localize->now;

		if(isset($parse_vars[$prefix.$delimeter.'edit_date']))
		{
			$edit_date = $parse_vars[$prefix.$delimeter.'edit_date'];

			$parse_vars[$prefix.$delimeter.'edit_date'] = mktime(substr($edit_date, 8, 2), substr($edit_date, 10, 2),  substr($edit_date, 12, 2), substr($edit_date, 4, 2), substr($edit_date, 6, 2), substr($edit_date, 0, 4));
			$entry_vars[$prefix.$delimeter.'edit_date'] = $parse_vars[$prefix.$delimeter.'edit_date'];
		}
		else
		{
			$parse_vars[$prefix.$delimeter.'edit_date'] = FALSE;
		}

		$parse_vars = array_merge($parse_vars, $this->EE->postmaster_model->get_member($member_id, 'member'));

		if(isset($parcel_copy->entry))
		{
			$entry = $parcel_copy->entry;
			unset($parcel_copy->entry);
		}
		
		if(!isset($entry_vars))
		{
			$entry_vars = array();
		}
		
		$return = $this->convert_array($this->EE->channel_data->tmpl->parse_array($parcel_copy, $parse_vars, $entry_vars, $channels, $channel_fields, $prefix.$delimeter));
		
		/* Fix 9/18/13 - Typography hack to prevent encoding URL's in emails */
		
		if(REQ == 'CP' && $current_method)
		{
			$_GET['M'] = $current_method;
		}

		return $return;		
	}

	public function plain_text($message)
	{
		// Strip style tags
		$message = preg_replace("/<style.*<\/style>/us", "", $message);

		// Strip HTML
		$message = strip_tags($message);

		// Strip consecutive 
		$message = preg_replace("/(\\n)\\1+/u", "$1$1", $message);

		// Trim the string
		$message = trim($message);
		
		return $message;
	}

	public function route_task($task_id, $hook, $args)
	{
		$this->EE->load->model('postmaster_routes_model');

		$routes = $this->EE->postmaster_routes_model->get_routes_by_task($hook, $task_id);	

		return $this->_route($routes, $args, 'task');
	}	
	
	public function route_hook($hook_id, $hook, $args)
	{
		$this->EE->load->model('postmaster_routes_model');
		
		var_dump($hook);exit();

		$routes = $this->EE->postmaster_routes_model->get_routes_by_hook($hook, $hook_id);	
		
		return $this->_route($routes, $args, 'hook');
	}

	private function _route($routes, $args, $type)
	{
		$return = array();

		foreach($routes->result_array() as $route)
		{
			// $path = PATH_THIRD . 'postmaster/' . $route['file'];

			// if(file_exists($path))
			// {	
				if($route['type'] == 'hook' || empty($route['type']))
				{
					return $this->EE->postmaster_hook->trigger($route['hook'], $args);
				}
				else
				{
					return $this->EE->postmaster_task->trigger($route, $args);
				}
			// }
		}

		return $return;
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
		
		$service = $this->load_service($parcel->service);
		
		$service->set_settings($parcel->settings);
		
		$date = $this->get_send_date($parsed_object);

		if($this->validate_emails($parsed_object->to_email))
		{
			if($ignore_date || $date <= $this->now())
			{
				$service->pre_process();

				$response = $service->send($parsed_object, $parcel);
				
				$service->set_response($response);				
				$service->post_process();
				
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
					$date = strtotime($parsed_object->send_every, $this->EE->localize->now);					
					$this->log_action('The email to "'.$parsed_object->to_email.'" is set to be sent every "'.$parsed_object->send_every.'". The next time it will be sent will be '.$date.'.');
					
					if(isset($parcel->entry))
					{
						$this->model->add_parcel_to_queue($parsed_object, $parcel, $date);
					}
					else
					{
						$this->model->add_hook_to_queue($parsed_object, $parcel, $date);
					}
				}
				
				return $response;
			}
			else
			{
				$this->log_action('The email to "'.$parsed_object->to_email.'" has been added to the queue and is set to be sent at '.date('Y-m-d H:i', $date).'.');

				if(isset($parcel->entry))
				{
					$this->model->add_parcel_to_queue($parsed_object, $parcel, $date);
				}
				else
				{
					$this->model->add_hook_to_queue($parsed_object, $parcel, $date);
				}
			}
		}
		else
		{
			$this->log_action('"'.$parsed_object->to_email.'" is not a valid email. It has been removed from the queue.');		
			$this->model->unsubscribe($parsed_object->to_email);
		}
		
		return FALSE;
	}
	
	public function strtotime($str)
	{
		if(preg_match('/^\d*$/', $str))
		{
			return $str;
		}
		
		return strtotime($str);
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
		if(!empty($row->parcel_id))
		{
			$parcel = $this->model->get_parcel($row->parcel_id);
			$parcel->entry = $this->model->get_entry($row->entry_id);
		}
		
		if(!empty($row->hook_id))
		{
			$parcel = $this->model->get_hook($row->hook_id)->row();
		}
		
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
		
		$this->EE->load->model('postmaster_routes_model');
		
		$routes = $this->EE->postmaster_routes_model->get_routes_by_hook($hook);	

		return $this->_route($routes, $args, 'hook');
	}


	/**
	 * Convenience method to trigger the specific task hook.
	 *
	 * @access	public
	 * @param	string 	The name of the hook to call
	 * @param 	array	An array of arguments used to call the hook
	 * @return	
	 */
	 
	public function trigger_task_hook($hook, $args)
	{
		$this->EE->load->library('postmaster_task', array(
			'base_path' => PATH_THIRD.'postmaster/tasks/'
		));
		
		$this->EE->load->model('postmaster_routes_model');
		
		$routes = $this->EE->postmaster_routes_model->get_routes_by_task($hook);	

		return $this->_route($routes, $args, 'task');		
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
			
			if($this->should_send_email($parcel->send_once, $entry_id, $parcel->id))
			{
				if($this->validate_enabled($parcel->enabled))
				{
					if($parcel->channel_id == $meta['channel_id'])
					{
						$entry_data['category'] = isset($entry_data['category']) ? $entry_data['category'] : array();
		
						if($this->validate_trigger($parcel->trigger))
						{
							if($this->validate_categories($entry_data['category'], $parcel->categories, $parcel->match_explicitly == '1' ? true : false))
							{		
								if($this->validate_member($meta['author_id'], $parcel->member_groups))
								{		
									if($this->validate_status($meta['status'], $parcel->statuses))
									{	
										$entry  = $this->EE->channel_data->get_channel_entry($entry_id)->row();
										$parcel = $this->append($parcel, 'entry', $entry);							
										
										$this->append($parcel, 'safecracker', (isset($this->EE->safecracker) ? TRUE : FALSE));
										
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
		}
	}

	public function should_send_email($send_once, $entry_id, $parcel_id)
	{
		$send_once = (int) $send_once;

		if(!$send_once)
		{
			return TRUE;
		}

		return $this->EE->postmaster_model->has_sent($entry_id, $parcel_id) ? FALSE : TRUE;
	}
	
	/**
	 * Validate a parsed conditional string 
	 *
	 * @access	public
	 * @param	string 	A parsed string
	 * @return	bool
	 */
	 
	public function validate_conditionals($extra_conditionals, $type = 'parcel')
	{
		$extra_conditionals = trim(strtoupper($extra_conditionals));

		if(empty($extra_conditionals) || $extra_conditionals == 'TRUE')
		{
			//$this->log_action('The parcel has valid extra conditions.');				
			return TRUE;
		}
		else
		{
			$this->log_action('The '.$type.' does not have valid extra conditions.');
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
	 
	public function validate_categories($subject, $valid_categories, $match_explicitly = true, $type = 'parcel')
	{
		$valid = 0;

		if(is_string($match_explicitly))
		{
			$match_explicitly = $match_explicitly == '1' ? true : false;
		}

		if(!count($valid_categories))
		{
			return TRUE;
		}

		foreach($valid_categories as $category)
		{
			if(count($subject) > 0 && in_array($category->cat_id, $subject))
			{
				$valid++;
			}
		}

		if($match_explicitly && count($valid_categories) == $valid || !$match_explicitly && $valid > 0)
		{
			$valid = TRUE;			
			//$this->log_action('The parcel has a valid category.');			
		}
		else
		{		
			$valid = FALSE;
			$this->log_action('The '.$type.' does not have a valid category.');
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
	 * Validate a parsed conditional string 
	 *
	 * @access	public
	 * @param	string 	A parsed string
	 * @return	bool
	 */
	 
	public function validate_enabled($enabled, $type = 'parcel')
	{
		$enabled = (int) trim($enabled);

		if($enabled != 0)
		{				
			return TRUE;
		}
		else
		{
			$this->log_action('The email did not send because the '.$type.' is not enabled.');
			return FALSE;
		}
	}
	
	/**
	 * Validate an member against the valid groups
	 *
	 * @access	public
	 * @param	int		A valid member_id
	 * @param	array	An array of valid member groups
	 * @return	bool
	 */
	 
	public function validate_member($subject, $valid_members, $type = 'parcel')
	{
		$valid  = FALSE;
		$member = $this->EE->channel_data->get_member($subject)->row();
		
		if(!count($valid_members))
		{
			return true;
		}

		foreach($valid_members as $valid_member)
		{
			if($valid_member->group_id == $member->group_id)
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
			$this->log_action('The '.$type.' does not have a valid author, which has a member_id of '.$subject.'.');
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
	 
	public function validate_status($subject, $statuses, $type = 'parcel')
	{
		$valid = FALSE;

		if(!count($statuses))
		{
			return TRUE;
		}

		foreach($statuses as $status)
		{
			if($subject == $status)
			{
				$valid = TRUE;
			}
		}

		// If no status is defined and entry has no status, return TRUE
		// Added 1.3.3 - Ignore statuses for Zoo Visitor.
		
		if(count($statuses) == 0 && empty($subject) || isset($_POST['zoo_visitor_action']))
		{
			$valid = TRUE;
		}
		
		if($valid)
		{
			//$this->log_action('The parcel has a valid status of "'.$subject.'".');
		}
		else
		{
			$this->log_action('The '.$type.' does not have a valid status, which is "'.$subject.'".');
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
	 * Get the current hook that is in progress
	 *
	 * @access	public
	 * @return	string
	 */
	
	public function get_hook_in_progress()
	{
		$hook = $this->EE->extensions->in_progress;

		if(empty($hook))
		{
			$hook = $this->EE->session->cache('postmaster', 'in_progress');
		}

		if(!$hook)
		{
			$hook = '';
		}

		return $hook;
	}

	
	/**
	 * Get the current time with member locale
	 *
	 * @access	public
	 * @return	string
	 */
	public function now($timestamp = NULL)
	{
		if(method_exists($this->EE->localize, 'set_localized_time'))
		{
			return $this->EE->localize->set_server_time($timestamp);
		}
		else
		{
			return $this->EE->localize->format_date('', $timestamp);
		}
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
		if(version_compare(APP_VER, '2.8.0', '>='))
		{
			return cp_url('addons_modules/show_module_cp', array(
				'module' => 'postmaster',
				'method' => $method
			));
		}
		else
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
		if($config_url = config_item('postmaster_base_url'))
		{
			$base_url = $config_url;
		}
		else
		{
			$this->EE->load->helper('addon_helper');
			
			$base_url = base_url();

			$url_has_www  = preg_match('/^www\./', $_SERVER['HTTP_HOST']);
			$base_has_www = preg_match('/^(http:\/\/www|www)\./', $base_url);

			if(!$base_has_www && $url_has_www)
			{
				$base_url = preg_replace('/^http:\/\//', 'http://www.', $base_url);
			}
		}
		
		if(!empty($append))
		{
			$base_url .= '?'.$append.'='.$value;
		}

		return $base_url;
	}
}

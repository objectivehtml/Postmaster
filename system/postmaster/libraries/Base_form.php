<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Base Form Class
 *
 * A class that easily allows developers to create EE form tags.
 * 
 * @subpackage	Libraries
 * @author		Justin Kimbrell
 * @copyright	Copyright (c) 2012, Justin Kimbrell
 * @link 		http://www.objectivehtml.com/libraries/base_form
 * @version		1.4.3
 * @build		20121102
 */

if(!class_exists('Base_form'))
{
	class Base_form {
		
		public $action            = '';
		public $additional_params = array('novalidate', 'onsubmit');
		public $ajax_response     = FALSE;
		public $class             = '';
		public $groups            = array();
		public $hidden_fields     = array();
		public $encode_fields	  = TRUE;
		public $error_handling    = 'inline';
		public $errors            = array();
		public $field_errors      = array();
		public $id                = '';
		public $method            = 'post';	
		public $name              = '';	
		public $prefix            = '';
		public $rules             = array();
		public $return            = FALSE;
		public $required          = '';
		public $secure_action     = FALSE;
		public $secure_return     = FALSE;
		public $tagdata           = '';
		public $key               = 'QZ@n/YsAgDi5Lz)o|RscOX\'rLY,pttpNPQ-83FP[`J_q~&Pxj7h{sA2`e5\v:#x';
		public $validation_field  = 'base_form_submit';
		
		public function __construct()
		{
			$this->EE =& get_instance();
			
			// Load user defined key from config if one exists, if not use default.
			// Obviously it's much more secure to use your own key!
			
			$this->EE->load->library('encrypt');
			$this->EE->load->library('form_validation');
			
			$saved_key = config_item('encryption_key');
			
			if(!empty($saved_key))
			{
				$this->key = $saved_key;
			}
			
			$this->return 	= $this->current_url();
			$this->tagdata 	= $this->EE->TMPL->tagdata;
		}
		
		public function clear($clear_errors = TRUE)
		{
			$this->action            = '';
			$this->additional_params = array('novalidate', 'onsubmit');
			$this->ajax_response     = FALSE;
			$this->class             = '';
			$this->groups            = array();
			$this->hidden_fields     = array();
			$this->rules             = array();
			$this->error_handling    = 'inline';
			
			if($clear_errors)
			{
				$this->errors            = array();
				$this->field_errors      = array();
			}
			
			$this->id                = '';
			$this->post              = '';
			$this->prefix			 = '';
			$this->method            = 'post';
			$this->name              = '';
			$this->return            = '';
			$this->required          = '';
			$this->secure_action     = FALSE;
			$this->secure_return     = FALSE;
			$this->tagdata           = $this->EE->TMPL->tagdata;
			$this->validation_field  = 'base_form_submit';
		}
		
		
		public function open($hidden_fields = array(), $fields = FALSE, $entry = FALSE)
		{	
			$this->return 			= $this->param('return', $this->current_url());
			$this->return_var		= $this->param('return_var');
			$this->return_segment	= $this->param('return_segment');

			if($this->return_var)
			{
				$this->return   = $this->EE->input->get_post('return');
			}
			
			if($this->return_segment)
			{
				$segments     = $this->EE->uri->segment_array();
				$segments     = array_slice($segments, (int) $this->return_segment);
				$this->return = '/'.implode('/', $segments);
			}			

			$this->ajax_response	= $this->param('ajax_response', $this->param('ajax', $this->ajax_response, TRUE), TRUE);
		
			$this->secure_action 	= $this->param('secure_action', $this->secure_action, TRUE);
			$this->secure_return 	= $this->param('secure_return', $this->secure_return, TRUE);
			$this->action			= empty($this->action) ? $this->param('action', $this->current_url()) : $this->action;
			$this->action			= $this->secure_url($this->action, $this->secure_action);		
			
			$this->class			= $this->param('class', $this->class);
			$this->groups			= $this->EE->channel_data->get_member_groups()->result_array();
			
			$this->error_handling 	= $this->param('error_handling', $this->error_handling);
			$this->hidden_fields	= array_merge($this->hidden_fields, $hidden_fields);
			$this->id				= $this->param('id', $this->id);
			$this->name				= $this->param('name', $this->name);
			$this->prefix			= $this->param('prefix', $this->prefix);
			
			$this->required 		= $this->param('required', $this->required);
			$this->required			= $this->required ? explode('|', $this->required) : FALSE;
			$this->rules 			= $this->param('rules', $this->rules);
			
			// Loops through parameters and looks for any defined rules
			if($this->EE->TMPL->tag_data[0]['params'])
			{
				foreach($this->EE->TMPL->tag_data[0]['params'] as $param => $rule)
				{
					if(preg_match("/^(rules:)/", $param, $matches))
					{
						$this->set_rule($param, $rule);
					}
				}
			}
			
			// Merges the default hidden_fields			
			$hidden_fields  = array_merge($this->hidden_fields, array(
				'XID'	   => '{XID_HASH}',
				'site_url' => $this->param('site_url') ? $this->param('site_url') : $this->EE->config->item('site_url'),
				'required' 		=> $this->required,
				'secure_return' => $this->secure_return,
				'ajax_response'	=> (boolean) $this->ajax_response ? 'y' : 'n',
				'base_form_submit' => TRUE,
				'return'		=> $this->return
			));
			
			// Loops through the member groups looking for dynamic redirects
			foreach($this->groups as $group)
			{
				$group_redirect = $this->param('group_'.$group['group_id'].'_return');
				
				if($group_redirect)
				{
					$hidden_fields['group_'.$group['group_id'].'_return'] = $group_redirect;
				}
			}
		
			// Add the rules to the hidden fields if they exist
			if(count($this->rules) > 0)
			{
				foreach($this->rules as $param => $rule)
				{	
					$hidden_fields['rule['.$param.']'] = $rule;
				}	
			}
			
			// Default form parameters			
			$params = array(
				'method' => $this->method,
				'class'	 => $this->class,
				'id'	 => $this->id,
				'name'	 => $this->name
			);
				
			// Append the additional_parameters	
			foreach($this->additional_params as $param)
			{
				if($this->param($param))
				{
					$params[$param] = $this->param($param);
				}
			}
			
			// Validate the form
			$this->validate();			
			
			// Create the error array
			$errors = array(
				array(
					'errors'			  => array(array()),
					'total_errors'		  => count($this->field_errors) + count($this->errors),
					'count:errors'		  => count($this->field_errors) + count($this->errors),
					'field_errors' 		  => array(array()),
					'total_field_errors'  => 0,
					'count:field_errors'  => 0,
					'global_errors'		  => array(array()),
					'total_global_errors' => 0,
					'count:global_errors' => 0
				)
			);
			
			// Add the POST vars to the template with a 'post:' prefix.
			$post = $this->add_prefix('post', $_POST);
			
			// If channel fields and an entry exists, parse the fields with the data
			if($fields && is_array($entry))
			{
				$this->tagdata = $this->parse_fields($fields, $entry);				
			}
			// Parse the template variables
			$this->tagdata = $this->parse(array($post));
					
			$errors = array();
			
			// If the field error count is greater than zero, then add errors
			if(count($this->field_errors) > 0)
			{
				$x = 0;
				
				foreach($this->field_errors as $field => $error)
				{
					$errors[0]['field_errors'][$x] 		= array('error' => $error);
					$errors[0]['field_error:'.$field]   = $error;
					$x++;
				}
				
				$errors[0]['total_field_errors'] = count($this->field_errors);
				$errors[0]['count:field_errors'] = $errors[0]['total_field_errors'];
			}
			
			// If the global error count is greater than zero, then add errors
			if(count($this->errors) > 0)
			{
				$x = 0;
				
				foreach($this->errors as $error)
				{
					$errors[0]['global_errors'][$x]	= array('error' => $error);
					$x++;
				}
				
				$errors[0]['total_global_errors'] = count($this->errors);
				$errors[0]['count:global_errors'] = count($errors[0]['total_global_errors']);
			}
			
			// Parse the tagdata again for errors
			$this->tagdata = $this->parse($errors);
				
			$this->EE->load->helper('form');
			$this->EE->load->helper('url');
			
			// Make sure the form POSTs back to the same exact URL
			if(!preg_match("/(http|https|ftp|ftps)\:\/\/?/", $this->action, $mathes))
			{
				$this->action = rtrim($this->current_url(FALSE), '/') . '/' . ltrim($this->action, '/');
			}
			
			if($this->error_handling != 'inline' && count(array_merge($this->field_errors, $this->errors)) > 0)
			{
				$this->EE->output->show_user_error('general', array_merge($this->field_errors, $this->errors));
			}
			
			// Return the form
			return form_open($this->action, $params, $this->encode($hidden_fields)) . $this->tagdata . '</form>';
		}
		
		public function get($field_name, $default = FALSE, $decode = TRUE)
		{
			$var = $this->EE->input->get($field_name);
			
			if($var && $decode)
			{
				$var = $this->decode($var);
			}
			
			if($var === FALSE)
			{
				$var = $default;
			}
			
			return $var;
		}
		
		public function post($field_name, $default = FALSE, $decode = TRUE)
		{
			$var = $this->EE->input->post($field_name);
			
			if($var && $decode)
			{
				$var = $this->decode($var);
			}
			
			if($var === FALSE)
			{
				$var = $default;
			}
			
			return $var;
		}
		
		public function encode($fields = array())
		{
			if($this->encode_fields)
			{
				if(is_array($fields))
				{
					foreach($fields as $index => $value)
					{
						if(is_array($value))
						{
							$fields[$index] = $this->encode($value);
						}
						else
						{
							$fields[$index] = $this->EE->encrypt->encode($value, $this->key);
						}
					}
				}
				else
				{
					$fields = $this->EE->encrypt->encode($fields, $this->key);
				}
			}
			
			return $fields;
		}
		
		public function decode($fields = array())
		{
			if($this->encode_fields)
			{
				if(is_array($fields))
				{
					foreach($fields as $index => $value)
					{
						if(is_array($value))
						{
							$fields[$index] = $this->decode($value);
						}
						else
						{
							$fields[$index] = $this->EE->encrypt->decode($value, $this->key);
						}
					}
				}
				else
				{
					$fields = $this->EE->encrypt->decode($fields, $this->key);
				}
			}
			
			return $fields;
		}
		
		public function set_rule($field_name, $rule)
		{
			$field_name = str_replace('rules:', '', $field_name);
			
			if(isset($this->rules[$field_name]))
			{
				$rule = rtrim($rule, '|') . '|' . $this->rules[$field_name];
			}
			
			$this->rules[$field_name] = $rule;
		}
		
		public function set_error($message)
		{
			$this->errors['Error '.(count($this->errors) + 1)] = $message;
		}
		
		public function set_message($rule, $message)
		{
			$this->EE->form_validation->set_message($rule, $message);		
		}
		
		public function set_field_error($field, $message)
		{
			$this->field_errors[$this->decode($field)] = $message;
		}
		
		public function parse_fields($field_data, $entry_data, $prefix = '')
		{
			if(!isset($entry_data[0]))
			{
				$entry_array = array($entry_data);
			}
			else
			{
				$entry_array = $entry_data;
			}
			
			foreach($entry_array as $entry_data)
			{
				$vars = array();
				
				foreach($field_data as $index => $row)
				{
					$field_name 		= $row['field_name'];
					$field_label 		= $row['field_label'];
					$field_instructions	= $row['field_instructions'];
					$field_type			= $row['field_type'];
					
					$vars[0][$field_name] = isset($entry_data[$prefix.$field_name]) ? $entry_data[$prefix.$field_name] : NULL;			
					$vars[0]['label:'.$field_name] = $field_label;
					$vars[0]['instructions:'.$field_name] = $field_instructions;
					$vars[0]['type:'.$field_name] = $field_type;
					
					if(!empty($row['field_list_items']))
					{
						foreach(explode("\n", $row['field_list_items']) as $option_index => $option)
						{
							
							$values = explode('|', $vars[0][$field_name]);
							
							$checked = 'checked="checked"';
							$selected = 'selected="selected"';
							
							$vars[0]['options:'.$field_name][] = array(
								'option_value'	=> $option,
								'option_name' 	=> $option,
								'selected'		=> in_array($option, $values) ? $selected : NULL,
								'checked'		=> in_array($option, $values) ? $checked : NULL
							);										
						}	
					}
				}
			}
			
			return $this->parse($vars);
		}
				
		public function validate($required_fields = array(), $additional_rules = array())
		{
			if(isset($_POST[$this->validation_field]))
			{
				$vars = array();

				$this->EE->form_validation->set_error_delimiters('', '');
				
				$validate_fields = isset($_POST['required']) ? $this->decode($_POST['required']) : $this->required;
				$validate_fields = !is_array($validate_fields) ? explode('|', $validate_fields) : $validate_fields;
				
				$required_fields = array_merge($required_fields, $validate_fields);
							
				foreach($required_fields as $field)
				{
					if(!empty($field))
					{
						$this->EE->form_validation->set_rules($field, ucwords(str_replace(array('-', '_'), ' ', $field)), 'callback_required_field_check');
					}
				}
				
				$rules = $this->decode(array_merge((isset($_POST['rule']) ? $_POST['rule'] : array()), $this->rules));
				
				foreach($rules as $field => $rule)
				{
					$label = ucwords(str_replace(array('_'), ' ', $field));
					
					$required_fields = array_merge(array($field), $required_fields);
					
					$this->EE->form_validation->set_rules($field, $label, $rule);
				}
				
				if ($this->EE->form_validation->run() == FALSE)
				{
					$error_count = 0;	
					
					foreach($required_fields as $field)
					{	
						$error = form_error($field);
							
						if($error !== FALSE && !empty($error))
						{	
							$this->set_field_error($field, $error);
						}
					}
				}
			}
		}
		
		public function required_field_check()
		{
			echo 'test';exit();
		}
		
		public function redirect($group_id = FALSE)
		{
			$url = $this->return;
			
			if(isset($_POST['return']))
			{
				$url = $this->decode($this->EE->input->post('return', TRUE));
			}
				
			if(isset($_POST['secure_return']))
			{
				$this->secure_return = (int) $this->decode($this->EE->input->post('secure_return', TRUE)) == 1 ? TRUE : FALSE;
			}
				
			if($group_id)
			{
				$group_redirect = $this->decode($this->EE->input->post('group_'.$group_id.'_return'));
				
				if($group_redirect)
				{
					$url = $group_redirect;
				}
			}
						
			$url = $this->secure_url($url, $this->secure_return);
							
			return $this->EE->functions->redirect($url);
		}
		
		public function secure_url($url, $secure = FALSE)
		{		
			if($secure === TRUE)
			{
				$url = str_replace('http://', 'https://', $url);
			}
			
			return $url;
		}
		
		public function current_url($uri_segments = TRUE)
		{
			$segments = $this->EE->uri->segment_array();
			
			$base_url = $this->base_url();
			
			$uri	  = '';
			
			$port = $_SERVER['SERVER_PORT'] == '80' || $_SERVER['SERVER_PORT'] == '443' ? NULL : ':' . $_SERVER['SERVER_PORT'];
			
			if($uri_segments)
			{
				$uri = '/' . implode('/', $segments);
			}
			
			$get = '';
			
			if(count($_GET) > 0)
			{
				$get = '?'.http_build_query($_GET);
			}
			
			return $base_url . $port . $uri . $get;
		}
		
		public function base_url()
		{
			$http = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
			
			if(!isset($_SERVER['SCRIPT_URI']))
			{				
				 $_SERVER['SCRIPT_URI'] = $http . $_SERVER['HTTP_HOST']. $_SERVER['REQUEST_URI'];
			}
			
			return $http . $_SERVER['HTTP_HOST'];
		}

		public function parse($vars, $tagdata = FALSE)
		{
			if($tagdata === FALSE)
			{
				$tagdata = $this->tagdata;
			}
				
			return $this->EE->TMPL->parse_variables($tagdata, $vars);
		}
		
		public function param($param, $default = FALSE, $boolean = FALSE, $required = FALSE)
		{
			$name	= $param;
			$param 	= $this->EE->TMPL->fetch_param($param);
			
			if($required && !$param) show_error('You must define a "'.$name.'" parameter in the '.__CLASS__.' tag.');
				
			if($param === FALSE && $default !== FALSE)
			{
				$param = $default;
			}
			else
			{				
				if($boolean)
				{
					$param = strtolower($param);
					$param = ($param == 'true' || $param == 'yes') ? TRUE : FALSE;
				}			
			}
			
			return $param;			
		}
		
		/**
		 * Channel Data Utility Method 
		 *
		 * Add a prefix to an result array or a single row.
		 * Must pass an array.
		 *
		 * @access	public
		 * @param	string	The prefix
		 * @param	array	The data to prefix
		 * @param	string	The delimiting value
		 * @return	array
		 */
		 public function add_prefix($prefix, $data, $delimeter = ':')
		 {
		 	$new_data = array();
		 	
		 	foreach($data as $data_index => $data_value)
		 	{
		 		if(is_array($data_value))
		 		{
		 			$new_row = array();
		 			
		 			foreach($data_value as $inner_index => $inner_value)
		 			{
		 				$new_row[$prefix . $delimeter . $inner_index] = $inner_value;
		 			}
		 			
		 			$new_data[$data_index] = $new_row;
		 		}
		 		else
		 		{
		 			$new_data[$prefix . $delimeter . $data_index] = $data_value;
		 		}
		 	}
		 	
		 	return $new_data;	
		 }	
	}
}
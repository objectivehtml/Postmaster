<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once PATH_THIRD.'postmaster/libraries/Postmaster_core.php';
require_once PATH_THIRD.'postmaster/libraries/Postmaster_curl.php';
require_once PATH_THIRD.'postmaster/libraries/Uuid.php';

abstract class Postmaster_base_api extends Postmaster_core {

	/**
	 * Email Service
	 * 
	 * @var string
	 */
	 		 
	protected $service;
	
	
	/**
	 * Fields to parse
	 * 
	 * @var string
	 */
	 		 
	protected static $parse_fields = array(
		'to_name',
		'to_email',
		'from_name',
		'from_email',
		'cc',
		'bcc',
		'subject',
		'message',
		'html_message', // New in v1.4
		'plain_message', // New in v1.4
		'post_date_specific',
		'post_date_relative',
		'send_every',
		'extra_conditionals'
	);
	
	
	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	array	An associative array to set class properties
	 * @return	void
	 */
	 
	public function __construct($params = array())
	{
		parent::__construct($params);
				
		$this->EE =& get_instance();
		
		$this->name         = strtolower(str_replace($this->class_suffix, '', get_class($this)));
		$this->filename     = ucfirst($this->name).'.php';
		$this->channel_data = $this->EE->channel_data;
	}
	
		
	/**
	 * Get the default settings
	 *
	 * @access	public
	 * @return	object
	 */
	 
	public function plain_text($message)
	{
		return $this->EE->postmaster_lib->plain_text($message);
	}
	
			
	/**
	 * Get the default settings
	 *
	 * @access	public
	 * @return	object
	 */
	 
	public function default_settings()
	{
		return (object) $this->default_settings;
	}
	
	
	/**
	 * Return a failed response
	 *
	 * @access	public
	 * @param	array	An array of properties to set  
	 * @return	object
	 */
	
	public function failed_response($params = array())
	{
		return $this->EE->postmaster_lib->failed_response($params);
	}
	 
	 
	/**
	 * Get the individual service settings array from the global settings
	 *
	 * @access	public
	 * @param	array	The global settings array  
	 * @return	array
	 */
	 
	public function get_settings($settings = FALSE)
	{
		if(!$settings)
		{
			$settings = $this->settings;
		}
		
		$default_settings = $this->default_settings();
		
		return isset($settings->{$this->name}) ? (object) array_merge((array) $default_settings, (array) $settings->{$this->name}) : $default_settings;
	}
	
	
	/**
	 * Display the settings table
	 *
	 * @access	
	 * @param 	Array	An array of settings
	 * @return	
	 */
	 
	public function display_settings($settings)
	{
		return $this->build_table($settings);
	}
	
	
	/**
	 * Build the settings table with InterfaceBuilder
	 *
	 * @access	public
	 * @param	array	The global settings array 
	 * @return	string
	 */
	 
	public function build_table($settings, $fields = FALSE)
	{	
		if($fields)
		{
			$this->fields = $fields;	
		}
		
		if(count($this->fields) == 0)
		{		
			return NULL;
		}
		
		$settings = $this->get_settings($settings);
		
		$params   = array(
			'varName'   => 'setting['.$this->get_name().']',
			'dataArray' => TRUE
		);
		
		return InterfaceBuilder::table($this->fields, $settings, $params, postmaster_table_attr());
	}	
		
	/**
	 * Parse the object
	 *
	 * @access	public
	 * @param	string 	The hook name
	 * @param	array 	An array of custom vars to parse
	 * @param	mixed 	An array of member data. If FALSE, default is used
	 * @return	
	 */
	 		
	public function parse($array, $vars = array(), $member_data = FALSE, $entry_data = array())
	{
		unset($array['settings']);
		
		$vars = array_merge(array(
			'logged_in_member_id' => $this->EE->session->userdata('member_id'),
			'logged_in_group_id'  => $this->EE->session->userdata('group_id'),
		), $vars);
		
		$vars = $this->EE->channel_data->utility->add_prefix($this->var_prefix, $vars);
		
		if(!$member_data)
		{
			$member_data = $this->EE->postmaster_model->get_member(FALSE);
		}
		
		$member_data = $this->EE->channel_data->utility->add_prefix('member', $member_data);
				
		$vars = array_merge($member_data, $vars);
		
		return $this->EE->channel_data->tmpl->parse_array($array, $vars, $entry_data, FALSE, array(), $this->var_prefix.':');
	}
		
		
	/**
	 * Pre process allows devs to execute arbitrary logic before the 
	 * API class's email is sent.
	 *
	 * @access	public
	 * @param	array
	 * @return	null
	 */
	 	
	public function pre_process($vars = array())
	{
		return;
	}
	
	
	/**
	 * Post process allows devs to execute arbitrary logic after the 
	 * API class's email is sent.
	 *
	 * @access	public
	 * @param	array
	 * @return	null
	 */
	 		
	public function post_process($vars = array())
	{
		return;
	}
		
				
	/**
	 * This method is triggered after the variables have been parsed.
	 *
	 * @access	public
	 * @param	array 	An associative array of parsed values
	 * @return	array
	 */
	
	public function post_parse($parsed_vars = array())
	{
		return $parsed_vars;
	}
	
	
	/**
	 * Triggers the email to be sent
	 *
	 * @access	public
	 * @param	array	An array of custom variables to parse
	 * @param	mixed 	An array of member data. If FALSE, default is used
	 * @param	mixed 	If 'Undefined', NULL is returned, otherwise the
	 *					the passed value is returned
	 * @return	
	 */
	 	
	public function trigger()
	{
		return $this->send();
	}
}

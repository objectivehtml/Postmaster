<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'Postmaster_base_api.php';

/**
 * Base Hook
 * 
 * @package		Postmaster
 * @author		Justin Kimbrell
 * @copyright	Copyright (c) 2012, Objective HTML
 * @link 		http://www.objectivehtml.com/
 * @version		1.2
 * @build		20121129
 */

abstract class Base_notification extends Postmaster_base_api {
		
	/**
	 * Variable prefix
	 * 
	 * @var string
	 */
	 		 
	protected $var_prefix = 'notice';
	
	
	/**
	 * Class suffix
	 * 
	 * @var string
	 */
	 		 
	protected $class_suffix = '_postmaster_notification';
	
	
	/**
	 * The notification's filename
	 * 
	 * @var string
	 */
	 			
	protected $filename = 'notification';
	
	
	/**
	 * The saved notification
	 * 
	 * @var string
	 */
	 			
	protected $notification = array();
	
	
	/**
	 * Response array
	 * 
	 * @var string
	 */
	 		 
	protected $response = array();
	
		
	/**
	 * Sets all the hook default and loads necessary dependencies
	 *
	 * @access	public
	 * @param	array	Associative array to set the default properties
	 * @return	null
	 */
	 		
	public function __construct($params = array())
	{
		parent::__construct($params);
	}	
	
	/**
	 * Method properly perpairs a notification to be parsed, then sent.
	 *
	 * @access	public
	 * @param	array	An array of custom vars to parse
	 * @param	mixed 	An array of member data. If FALSE, default is used
	 * @param	mixed 	If 'Undefined', NULL is returned, otherwise the
	 *					the passed value is returned
	 * @return	object
	 */
	 	
	public function send($vars = array(), $member_data = FALSE, $entry_data = array())
	{			
		if(is_object($member_data))
		{
			$member_data = (array) $member_data;	
		}
		
		if(!is_array($member_data))
		{
			$member_data = FALSE;
			$return_data = $member_data;	
		}
		
		$notification = (array) $this->notification;
		$settings	  = $notification['settings'];
		
		if(is_string($settings))
		{
			$settings = json_decode($settings);
		}

		$parsed_array = $this->parse($notification, $vars, $member_data, $entry_data);
		
		$parsed_array = $this->post_parse($parsed_array);	
			
		$notification['settings'] = (object) $settings;		
		
		if($this->EE->postmaster_lib->validate_conditionals($parsed_array['extra_conditionals']))
		{
			$response = $this->EE->postmaster_lib->send($parsed_array, $notification);
		}
		else
		{
			$response = $this->EE->postmaster_lib->failed_response($$parsed_array);
		}
		
		return $response;	
	}
}
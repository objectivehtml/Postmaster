<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'Base_notification.php';
require_once 'Postmaster_base_lib.php';

class Postmaster_notification extends Postmaster_base_lib {
	
	/**
	 * Base API Directory
	 * 
	 * @var string
	 */
	 
	protected $base_dir = 'notifications';
	
	
	/**
	 * Notifications
	 * 
	 * @var string
	 */
	 
	protected $notifications = array();
	
	
	/**
	 * Class Suffix
	 * 
	 * @var string
	 */
	 
	protected $class_suffix = '_postmaster_notification';
	
		
	/**
	 * Default Notification
	 * 
	 * @var string
	 */
	 
	protected $default_notification = 'Basic';
	
	
	
	/**
	 * Reserved File Names
	 * 
	 * @var string
	 */
	 
	protected $reserved_files = array(
		'Basic.php'
	);
		
	
	/**
	 * Construct
	 *
	 * @access	public
	 * @param	array 	Dynamically set properties
	 * @return	void
	 */
	
	public function __construct($data = array(), $debug = FALSE)
	{
		$data['default_object'] = $this->default_notification;
		
		parent::__construct($data);
	}
	
	
	/**
	 * Get a single notication from the directory
	 *
	 * @access	public
	 * @param	mixed    A valid index or notification name
	 * @return	mixed
	 */
	
	public function get_notification($index = FALSE, $settings = FALSE)
	{		
		return parent::get_object($index, $settings);
	}
	
	
	/**
	 * Get the available notifications from the directory
	 *
	 * @access	public
	 * @return	array
	 */
	
	public function get_notifications($settings = FALSE)
	{
		return parent::get_objects(array(
			'settings' => $settings
		));
	}	
	
		
	/**
	 * Total Notifications
	 *
	 * @access	public
	 * @return	int
	 */
	
	public function total_notifications()
	{
		return parent::total_objects();
	}
	
	
	/**
	 * Triggers all the notification's methods with all the proper args
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @return	array
	 */
	 
	public function trigger($notification_obj, $args = array())
	{
		$enabled = TRUE;
		
		if(isset($notification_obj->enabled))
		{
			$enabled = $notification_obj->enabled;
		}
	
		if($this->EE->postmaster_lib->validate_enabled($enabled, 'hook'))
		{	
			call_user_func_array(array($notification_obj, 'pre_process'), $args);
			
			$response = call_user_func_array(array($notification_obj, 'trigger'), $args);
			
			$notification_obj->set_response($response);
			
			call_user_func_array(array($notification_obj, 'post_process'), $args);
			
			return $notification_obj->get_response();
		}
		
		return FALSE;
	}
	
}
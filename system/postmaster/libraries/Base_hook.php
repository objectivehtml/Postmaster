<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'Postmaster_base_api.php';

/**
 * Base Hook
 * 
 * @package		Postmaster
 * @author		Justin Kimbrell
 * @copyright	Copyright (c) 2012, Objective HTML
 * @link 		http://www.objectivehtml.com/
 * @version		1.3.0
 * @build		20121221
 */

abstract class Base_hook extends Postmaster_base_api {
	 
	/**
	 * Actual EE hook name to use
	 * 
	 * @var string
	 */
	 		 
	protected $hook;
	
	
	/**
	 * Variable prefix
	 * 
	 * @var string
	 */
	 		 
	protected $var_prefix = 'hook';
	
	
	/**
	 * Class suffix
	 * 
	 * @var string
	 */
	 		 
	protected $class_suffix = '_postmaster_hook';
	
	
	/**
	 * The hook's filename
	 * 
	 * @var string
	 */
	 			
	protected $filename = 'hook';
	
	
	/**
	 * Default Settings Array
	 * 
	 * @var string
	 */
	 		 
	protected $default_settings = array(
		'end_script' => FALSE
	);
	
	
	/**
	 * Response array
	 * 
	 * @var string
	 */
	 		 
	protected $responses = array();
	
	
	/**
	 * Default Settings Field Schema
	 * 
	 * @var string
	 */
	 		 	 
	protected $fields = array(
		
		'end_script' => array(
			'label' => 'End Script',
			'id'	=> 'end_script',
			'type'	=> 'radio',
			'description' => 'Setting this value to true will stop the script from finishing (after the email is sent).',
			'settings' => array(
				'options' => array(
					FALSE => 'False',
					TRUE  => 'True'
				)
			)
		)
	);
			
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
	 * Checks a response and sets the end_script to TRUE of needed
	 *
	 * @access	public
	 * @param	array|object	The response array/object
	 * @return	NULL
	 */
	 		
	public function end_script($response)
	{
		return $this->EE->postmaster_hook->end_script($response);
	}
	
	/**
	 * Gets the name of the hook
	 *
	 * @access	public
	 * @return	string
	 */
	public function get_hook()
	{
		return !empty($this->hook) ? $this->hook : $this->name;	
	}
	
	/**
	 * Gets all the installed hooks
	 *
	 * @access	public
	 * @param	string	Name of the hook 
	 * @return	object
	 */
	 	
	public function get_installed_hooks($hook)
	{
		return $this->EE->postmaster_model->get_installed_hooks($hook);
	}
	
	/**
	 * Method properly perpairs a hook to be parsed, then sent.
	 *
	 * @access	public
	 * @param	array	An array of custom vars to parse
	 * @param	mixed 	An array of member data. If FALSE, default is used
	 * @param	mixed 	If 'Undefined', NULL is returned, otherwise the
	 *					the passed value is returned
	 * @return	object
	 */
	 	
	public function send($vars = array(), $member_data = FALSE, $entry_data = array(), $return_data = 'Undefined')
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

		$hook			  = (array) $this->hook;
		$settings		  = $hook['settings'];
		
		if(is_string($settings))
		{
			$settings = json_decode($settings);
		}
		
		$name             = !empty($hook['installed_hook']) ? $hook['installed_hook'] : $hook['user_defined_hook'];		
		
		$parsed_hook      = $this->parse($hook, $vars, $member_data, $entry_data);
		$parsed_hook      = $this->post_parse($parsed_hook);	
			
		$hook['settings'] = (object) $settings;		
		$end_script 	  = isset($hook['settings']->$name->end_script) ? (bool) $hook['settings']->$name->end_script : FALSE;
	
		$obj = array('end_script' => $end_script);
		
		$parsed_hook['hook_id'] = $hook['id'];
		
		if($this->EE->postmaster_lib->validate_conditionals($parsed_hook['extra_conditionals']))
		{
			$obj['response'] = $this->EE->postmaster_lib->send($parsed_hook, $hook);
		}
		else
		{
			$obj['response'] = $this->EE->postmaster_lib->failed_response($parsed_hook);
		}
		
		if($return_data !== 'Undefined')
		{
			$obj['return_data'] = $return_data;	
		}
	
		return (object) $obj;
	}
}
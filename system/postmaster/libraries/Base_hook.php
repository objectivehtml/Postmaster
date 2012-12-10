<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'Base_class.php';

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

abstract class Base_hook extends Base_class {
		
	/**
	 * Hook Title
	 * 
	 * @var string
	 */
	 		 	
	protected $title;
	
		
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
		'post_date_specific',
		'post_date_relative',
		'send_every',
		'extra_conditionals'
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
		parent::__construct($params = array());
		
		$this->EE =& get_instance();
		
		$this->EE->load->driver('interface_builder');
		
		$this->name      = strtolower(str_replace('_postmaster_hook', '', get_class($this)));
		$this->file_name    = ucfirst($this->name).'.php';
		$this->IB           = $this->EE->interface_builder;
		$this->channel_data = $this->EE->channel_data;	
		$this->settings     = $this->get_settings();
								
		$this->IB->set_var_name($this->name);
		$this->IB->set_prefix('setting');
		$this->IB->set_use_array(TRUE);
	}	
	
	
	/**
	 * Display the settings table
	 *
	 * @access	public
	 * @param	array 	The InterfaceBuilder schema array 
	 * @return	string
	 */
	 		
	public function display_settings($data = array())
	{
		if(count($this->fields) == 0)
		{		
			return FALSE;
		}
		
		$settings = isset($data->{$this->name}) ? $data->{$this->name} : $this->get_default_settings();
		
		$this->IB->set_var_name($this->name);
		$this->IB->set_prefix('setting');
		$this->IB->set_use_array(TRUE);
				
		return $this->IB->table($this->fields, $settings, postmaster_table_attr());
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
	 * Pre process allows devs to execute arbitrary logic before the 
	 * hook's email is sent.
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
	 * hook's email is sent.
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
	 * Triggers the email to be sent
	 *
	 * @access	public
	 * @param	array	An array of custom variables to parse
	 * @param	mixed 	An array of member data. If FALSE, default is used
	 * @param	mixed 	If 'Undefined', NULL is returned, otherwise the
	 					the passed value is returned
	 * @return	
	 */
	 	
	public function trigger()
	{
		return $this->send();
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
	 * Parse the hook object
	 *
	 * @access	public
	 * @param	string 	The hook name
	 * @param	array 	An array of custom vars to parse
	 * @param	mixed 	An array of member data. If FALSE, default is used
	 * @return	
	 */
	 		
	public function parse($hook, $vars = array(), $member_data = FALSE, $entry_data = array())
	{
		unset($hook['settings']);
		
		$vars = $this->EE->channel_data->utility->add_prefix($this->var_prefix, $vars);
		
		if(!$member_data)
		{
			$member_data = $this->EE->postmaster_model->get_member(FALSE);
		}
		
		$member_data = $this->EE->channel_data->utility->add_prefix('member', $member_data);
				
		$vars = array_merge($member_data, $vars);
		
		return $this->EE->channel_data->tmpl->parse_array($hook, $vars, $entry_data, FALSE, array(), $this->var_prefix.':');
	}
	
	
	/**
	 * Method properly perpairs a hook to be parsed, then sent.
	 *
	 * @access	public
	 * @param	array	An array of custom vars to parse
	 * @param	mixed 	An array of member data. If FALSE, default is used
	 * @param	mixed 	If 'Undefined', NULL is returned, otherwise the
	 					the passed value is returned
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
		$name             = !empty($hook['installed_hook']) ? $hook['installed_hook'] : $hook['user_defined_hook'];		
		$parsed_hook      = $this->parse($hook, $vars, $member_data, $entry_data);
			
		$hook['settings'] = (object) $settings;		
		$end_script 	  = isset($hook['settings']->$name->end_script) ? (bool) $hook['settings']->$name->end_script : FALSE;
	
		$obj = array('end_script' => $end_script);
		
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
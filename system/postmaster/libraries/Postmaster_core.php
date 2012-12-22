<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once PATH_THIRD.'/postmaster/libraries/Curl.php';
require_once PATH_THIRD.'/postmaster/libraries/Uuid.php';
require_once PATH_THIRD.'postmaster/libraries/Base_class.php';

/**
 * Postmaster Core
 * 
 * @package		Postmaster
 * @author		Justin Kimbrell
 * @copyright	Copyright (c) 2012, Objective HTML
 * @link 		http://www.objectivehtml.com/
 * @version		1.3.0
 * @build		20121221
 */
 
abstract class Postmaster_core extends Base_class {

	/**
	 * Object Title
	 * 
	 * @var string
	 */
	 		 	
	protected $title;
	
	/**
	 * Object Name
	 */
	 
	protected $name;
	
	
	/**
	 * Object Description
	 */
	 
	protected $description;
	
	
	/**
	 * Current GMT Time
	 */
	 
	protected $now;
	
	
	/**
	 * Default Settings
	 */
	 
	protected $default_settings = array();
	
	
	/**
	 * Settings
	 */
	 
	protected $settings;
	
	
	/**
	 * cURL Object
	 */
	 
	protected $curl;
	
	
	/**
	 * Uuid generator
	 */
	 
	protected $uid;
	
	
	/**
	 * Postmaster library
	 */
	 
	protected $lib;

	/**
	 * Variable prefix
	 * 
	 * @var string
	 */
	 		 
	protected $var_prefix;
	
	
	/**
	 * Class suffix
	 * 
	 * @var string
	 */
	 		 
	protected $class_suffix;
	
	
	/**
	 * The API Class's filename
	 * 
	 * @var string
	 */
	 			
	protected $filename;
		
	/**
	 * Default Settings Field Schema
	 * 
	 * @var string
	 */
	 		 	 
	protected $fields = array();
	
	
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
	
	public function __construct()
	{
		$this->EE =& get_instance();
		
		$this->EE->load->library('postmaster_lib');
		$this->EE->load->driver('interface_builder');
		
		$this->curl = new Curl();
		$this->uid  = new Uuid();
		$this->lib  = $this->EE->postmaster_lib;
		$this->now  = $this->EE->localize->now;
				
		$this->EE->interface_builder->set_var_name($this->name);
		$this->EE->interface_builder->set_use_array(TRUE);
		
		$this->IB	=& $this->EE->interface_builder;		
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
		
	
	
	public function action_url($class, $method)
	{
		return $this->EE->postmaster_lib->current_url('ACT', $this->EE->channel_data->get_action_id($class, $method));
	}
	
	public function delegate_url()
	{
		return $this->action_url('Postmaster', 'delegate_action');
	}

	public function call_url($method, $params = array())
	{
		$base_url = $this->action_url('postmaster_mcp', 'call');

		$params = array_merge(
			array(
				'service'        => $this->name,
				'service_method' => $method
			),
			$params
		);

		return $base_url . '&' . http_build_query($params);
	}

	public function json($data)
	{
		header('Content-header: application/json');

		exit(json_encode($data));
	}

	public function show_error($error)
	{
		$this->EE->output->show_user_error('general', '<b>'.$this->name.'</b> - '.$error);
	}
	
	
	/**
	 * Installs the API
	 *
	 * @access	public
	 * @return	
	 */
	 	
	
	public function install()
	{
		return;
	}
	
	
	/**
	 * Updates the API
	 *
	 * @access	public
	 * @return	
	 */
	 	
	public function update()
	{
		return;
	}
}
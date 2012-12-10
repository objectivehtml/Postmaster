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
 * @version		1.0.99
 * @build		20120703
 */
 
abstract class Postmaster_core {

	/**
	 * Object Name
	 */
	 
	public $name;
	
	
	/**
	 * Object Description
	 */
	 
	public $description;
	
	
	/**
	 * Current GMT Time
	 */
	 
	public $now;
	
	
	/**
	 * Default Settings
	 */
	 
	public $default_settings = array();
	
	
	/**
	 * Settings
	 */
	 
	public $settings;
	
	
	/**
	 * cURL Objective
	 */
	 
	public $curl;
	
	
	/**
	 * Uuid generator
	 */
	 
	public $uid;
	
	
	/**
	 * Postmaster library
	 */
	 
	public $lib;

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
}
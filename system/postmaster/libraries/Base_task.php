<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once PATH_THIRD.'postmaster/libraries/Postmaster_base_api.php';

/**
 * Base Task
 * 
 * @package		Postmaster
 * @author		Justin Kimbrell
 * @copyright	Copyright (c) 2012, Objective HTML
 * @link 		http://www.objectivehtml.com/
 * @version		1.3.0
 * @build		20130317
 */

abstract class Base_task extends Postmaster_base_api {
	
	protected $task;
	
	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	void
	 */
	
	public function __construct()
	{
		$this->EE =& get_instance();
		
		$this->EE->load->library('postmaster_lib');
		
		$this->lib  = $this->EE->postmaster_lib;
		$this->now  = $this->EE->localize->now;	
	}
	
	public function get_task()
	{
		return !empty($this->task) ? $this->task : $this->name;
	}
}

if(!class_exists('Postmaster_Task_Response'))
{
	/**
	 * Postmaster Service Response
	 */
	 
	class Postmaster_Task_Response extends Base_class {
	
		public  $task_id,
				$site_id,
				$title,
				$installed_hook,
				$user_defined_hook,
				$actual_hook_name,
				$priority,
				$settings;
	
		public function __construct($data = array())
		{		
			parent::__construct($data);		
		}
	}
}
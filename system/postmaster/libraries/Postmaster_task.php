<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'Base_task.php';
require_once 'Postmaster_base_lib.php';

class Postmaster_task extends Postmaster_base_lib {
	
	/**
	 * Base API Directory
	 * 
	 * @var string
	 */
	 
	protected $base_dir = 'tasks';
	
	
	/**
	 * Hooks
	 * 
	 * @var string
	 */
	 
	protected $tasks = array();
	
	
	/**
	 * Reserved File Names
	 * 
	 * @var string
	 */
	 
	protected $reserved_files = array();
	
		
	/**
	 * Default Hook
	 * 
	 * @var string
	 */
	 
	protected $default_task = '';
	
		
	/**
	 * Class Suffix
	 * 
	 * @var string
	 */
	 
	protected $class_suffix = '_postmaster_task';
	
	
	/**
	 * Construct
	 *
	 * @access	public
	 * @param	array 	Dynamically set properties
	 * @return	void
	 */
	
	public function __construct($data = array(), $debug = FALSE)
	{
		$data['default_object'] = $this->default_task;
		
		parent::__construct($data);
	}
	
	
	/**
	 * Checks a response object or array and sets the end_script property
	 *
	 * @access	public
	 * @param	mixed
	 * @return	bool
	 */
	 
	public function end_script($responses)
	{
		if(!is_array($responses))
		{
			$responses = array($responses);
		}
		
		foreach($responses as $response)
		{
			if(isset($response->end_script) && $response->end_script)
			{
				return TRUE;
			}
		}
		
		return FALSE;
	}
	
	
	/**
	 * Checks a response object or array and returns the proper data type
	 *
	 * @access	public
	 * @param	mixed
	 * @return	null
	 */
	 
	public function return_data($responses)
	{
		if(!is_array($responses))
		{
			$response = array($response);
		}
		
		foreach($responses as $response)
		{
			$response = (object) $response;
			
			if(isset($response->return_data))
			{
				return $response->return_data;
			}
		}
		
		return NULL;
	}
	
	
	/**
	 * Get a single task from the directory
	 *
	 * @access	public
	 * @param	mixed    A valid index or task name
	 * @return	mixed
	 */
	
	public function get_task($index = FALSE, $settings = FALSE)
	{		
		return parent::get_object($index, $settings);
	}
	
	
	/**
	 * Get the available tasks from the directory
	 *
	 * @access	public
	 * @return	array
	 */
	
	public function get_tasks($settings = FALSE)
	{	
		return parent::get_objects(array(
			'settings' => $settings
		));
	}	
		
	
	/**
	 * Total Tasks
	 *
	 * @access	public
	 * @return	int
	 */
	
	public function total_tasks()
	{
		return parent::total_objects();
	}
	

	public function ping($task)
	{
		if($task->get_enable_cron())
		{
			$task->trigger_cron();
		}
	}
	
	/**
	 * Triggers all the task's methods with all the proper args
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @return	array
	 */
	 
	public function trigger($route, $args = array())
	{
		$route = (object) $route;
		//$routes = $this->EE->postmaster_routes_model->get_routes_by_task($index);
		
		$return = array();

		//foreach($routes->result() as $index => $route)
		//{
			$task 	  = $this->EE->postmaster_model->get_task($route->obj_id)->row_array();
			
			$task_obj = $this->get_task($task['task']);

			if(!isset($task['enabled']))
			{
				$task['enabled'] = TRUE;	
			}

			if($this->EE->postmaster_lib->validate_enabled($task['enabled'], 'task'))
			{
				$task_obj->set_settings(json_decode($task['settings']));
				
				call_user_func_array(array($task_obj, 'pre_process'), $args);
				
				$task_obj->set_task($task);
					
				$response    = call_user_func_array(array($task_obj, $route->method), $args);

				if(!is_object($response))
				{
					$response = (object) array(
						'return_data' => $response,
						'end_script'  => FALSE
					);
				}
				
				$responses[] = $response;

				$task_obj->set_response($response);

				call_user_func_array(array($task_obj, 'post_process'), $args);
				
				$return = array_merge($return, array($response));
			}
		// }

		return $return;
	}
}
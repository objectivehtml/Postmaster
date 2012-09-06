<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'Base_class.php';
require_once 'Base_hook.php';

class Postmaster_hook extends Base_class {
	
	/**
	 * Base File Path
	 * 
	 * @var string
	 */
	 
	protected $base_path = '../hooks';
	
	
	/**
	 * Hooks
	 * 
	 * @var string
	 */
	 
	protected $hooks = array();
	
	
	/**
	 * Reserved File Names
	 * 
	 * @var string
	 */
	 
	protected $reserved_files = array(
		'Postmaster_base_hook.php'
	);
	
		
	/**
	 * Default Hook
	 * 
	 * @var string
	 */
	 
	protected $default_hook = 'Postmaster_base_hook.php';
	
		
	/**
	 * Class Suffix
	 * 
	 * @var string
	 */
	 
	protected $class_suffix = '_postmaster_hook';
	
	
	/**
	 * Construct
	 *
	 * @access	public
	 * @param	array 	Dynamically set properties
	 * @return	void
	 */
	
	public function __construct($data = array(), $debug = FALSE)
	{
		parent::__construct($data);
		
		$this->EE =& get_instance();
	}
	
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
	
	public function return_data($responses)
	{
		if(!is_array($responses))
		{
			$response = array($response);
		}
		
		foreach($responses as $response)
		{
			if(isset($response->return_data))
			{
				return $response->return_data;
			}
		}
		
		return NULL;
	}
	
	public function trigger($index, $vars = array())
	{
		$hook_obj = $this->get_hook($index);
		
		$hook_obj->pre_process($vars);
		
		$responses = array();
			
		foreach($this->EE->postmaster_model->get_installed_hooks($index) as $hook)
		{
			$hook_name = !empty($hook['installed_hook']) ? $hook['installed_hook'] : $hook['user_defined_hook'];
			
			$hook_obj->set_settings($hook['settings']);
			
			$responses = array_merge($responses, $hook_obj->trigger($index, $vars));
		}
		
		$responses = $hook_obj->post_process($responses, $vars);
		
		return $responses;
	}
	
	/**
	 * Get Hook
	 *
	 * @access	public
	 * @param	mixed    A valid index or hook name
	 * @return	mixed
	 */
	
	public function get_hook($index = FALSE)
	{		
		$this->hooks = $this->get_hooks();
		
		if($index && is_int($index))
		{
			if(!isset($this->hooks[$index]))
			{
				return $index;
			}
			
			return $this->hooks[$index];
		}
		else
		{
			foreach($this->hooks as $x => $obj)
			{
				$hook = rtrim(get_class($obj), '_postmaster_hook');
								
				if($index == $obj->get_name() || $index == $obj->get_title())
				{
					return $this->hooks[$x];
				}
			}
		}
				
		return $this->get_hook(rtrim($this->default_hook, '.php'));
	}
	
	
	/**
	 * Get Hooks
	 *
	 * @access	public
	 * @return	array
	 */
	
	public function get_hooks()
	{
		$this->EE->load->helper('directory');
		
		$hooks = array(
			$this->load($this->default_hook)
		);
		
		foreach(directory_map($this->base_path) as $file)
		{
			if(!in_array($file, $this->reserved_files))
			{
				if($hook = $this->load($file))
				{
					$hooks[] = $hook;
				}
			}
		}
		
		return $hooks;
	}	
	
	
	/**
	 * Total Hooks
	 *
	 * @access	public
	 * @return	int
	 */
	
	public function total_hooks()
	{
		$this->hooks = $this->get_hooks();
		
		return count($this->hooks);
	}
	
	
	/**
	 * Load
	 *
	 * @access	public
	 * @param	string  A valid file name
	 * @return	mixed
	 */
	
	public function load($file)
	{
		require_once $this->base_path . $file;
		
		$class = str_replace('.php', '', $file);
		
		if(!in_array($file, $this->reserved_files))
		{
			$class .= $this->class_suffix;
		}
		
		if(class_exists($class))
		{
			$return = new $class(array());
			
			return $return;
		}
		
		return FALSE;
	}	
}
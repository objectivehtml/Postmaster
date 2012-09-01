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
	 
	private static $reserved_files = array(
		'Postmaster_base_hook.php'
	);
	
		
	/**
	 * Default Hook
	 * 
	 * @var string
	 */
	 
	private static $default_hook = 'Postmaster_base_hook.php';
	
	
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
	
	public function trigger($index, $args)
	{
		return $this->get_hook($index)->trigger($index, $args);
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
				
				if($index == $hook || $x == $index || $index == $obj->get_title())
				{
					return $this->hooks[$x];
				}
			}
		}
				
		return $this->get_hook(self::$default_hook);
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
		
		$hooks = array();
		
		foreach(directory_map($this->base_path) as $file)
		{
			if(!in_array($file, self::$reserved_files))
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
		
		if(class_exists($class))
		{
			$return = new $class(array());
			
			return $return;
		}
		
		return FALSE;
	}	
}
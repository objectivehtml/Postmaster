<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'Base_class.php';
require_once 'Base_hook.php';
require_once 'Base_notification.php';

abstract class Postmaster_base_lib extends Base_class {
	
	/**
	 * Base File Path
	 * 
	 * @var string
	 */
	 
	protected $base_path;
	
	
	/**
	 * Class Suffix
	 * 
	 * @var string
	 */
	 
	protected $class_suffix;
	
		
	/**
	 * Default Object
	 * 
	 * @var string
	 */
	 
	protected $default_object;
	
				
	/**
	 * Objects
	 * 
	 * @var string
	 */
	 
	protected $obj = array();
	
		
	
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
	
	
	/**
	 * Get a single notication from the directory
	 *
	 * @access	public
	 * @param	mixed    A valid index or object name
	 * @return	mixed
	 */
	
	public function get_object($index = FALSE)
	{		
		$this->objects = $this->get_objects();
		
		if($index && is_int($index))
		{
			if(!isset($this->objects[$index]))
			{
				return $index;
			}
			
			return $this->objects[$index];
		}
		else
		{
			foreach($this->objects as $x => $obj)
			{
				if(is_object($obj))
				{
					$object = rtrim(get_class($obj), $this->class_suffix);
						
					if($index == $obj->get_name() || $index == $obj->get_title())
					{
						return $this->objects[$x];
					}
				}
			}
		}
				
		return $this->get_object(ucfirst(rtrim($this->default_object, '.php')));
	}
	
	
	/**
	 * Get the available objects from the directory
	 *
	 * @access	public
	 * @return	array
	 */
	
	public function get_objects()
	{
		$this->EE->load->helper('directory');
		
		$default_object = $this->load($this->default_object);
		
		if(is_object($default_object))
		{
			$objects = array($default_object);
		}
		
		foreach(directory_map($this->base_path) as $file)
		{
			if(!in_array($file, $this->reserved_files))
			{
				if($object = $this->load($file))
				{
					if(is_object($object))
					{
						$objects[] = $object;
					}
				}
			}
		}
		
		return $objects;
	}	
	
	/**
	 * Get Reserved Files
	 *
	 * @access	public
	 * @return	array
	 */
	
	public function get_reserved_files()
	{
		return $this->reserved_files;
	}	
	
	
	/**
	 * Total Objects
	 *
	 * @access	public
	 * @return	int
	 */
	
	public function total_objects()
	{
		$this->objects = $this->get_objects();
		
		return count($this->objects);
	}
	
		
	/**
	 * Load
	 *
	 * @access	public
	 * @param	string  A valid file name
	 * @return	mixed
	 */
	
	public function load($file, $params = array())
	{
		if(!empty($file))
		{	
			$file = preg_replace('/.php$/', '', $file);
			
			require_once $this->base_path . $file . '.php';
			
			$class = $file;
			
			if(!in_array($file, $this->reserved_files))
			{
				$class .= $this->class_suffix;
			}
			
			if(class_exists($class))
			{
				$return = new $class($params);
				
				return $return;
			}
		}
		
		return FALSE;
	}	
	
		
	/**
	 * Log Action
	 *
	 * @access	public
	 * @param	string  The string to log
	 * @return	mixed
	 */
	
	public function log_action($str)
	{	
		$this->EE->load->library('postmaster_lib');	
		
		$this->EE->postmaster_lib->log_action($str);
	}
}
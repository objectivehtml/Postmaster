<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'Base_class.php';
require_once 'Base_hook.php';
require_once 'Base_notification.php';

abstract class Postmaster_base_lib extends Base_class {
	
	/**
	 * Base API Directory
	 * 
	 * @var string
	 */
	 
	protected $base_dir;
	
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

		$this->base_path = PATH_THIRD . 'postmaster/' . $this->base_dir . '/';
	}
	
	
	/**
	 * Get a single notication from the directory
	 *
	 * @access	public
	 * @param	mixed    A valid index or object name
	 * @return	mixed
	 */
	
	public function get_object($index = FALSE, $params = FALSE)
	{		
		$this->objects = $this->get_objects($params);

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
	
	public function get_objects($params = FALSE)
	{
		$this->EE->load->helper('directory');
		
		$default_object = $this->load($this->default_object, $params);
		
		$objects = array();
		
		if(is_object($default_object))
		{
			$objects[] = $default_object;
		}

		$directory = directory_map($this->base_path, 1);

		if(is_array($directory))
		{
			foreach($directory as $file)
			{
				if(!in_array($file, $this->reserved_files))
				{
					if($object = $this->load($file, $params))
					{
						if(is_object($object))
						{
							$objects[] = $object;
						}
					}
				}
			}
		}

		$original_path = $this->base_path;

		foreach(directory_map(PATH_THIRD, 1) as $file)
		{
			if($file != 'postmaster')
			{	
				if(is_dir(PATH_THIRD . $file))
				{
					$this->base_path = PATH_THIRD . $file . '/postmaster' . '/' . $this->base_dir . '/';

					if(is_dir($this->base_path))
					{
						foreach(directory_map($this->base_path, 1) as $file)
						{
							if($object = $this->load($file, $params, true))
							{
								if(is_object($object))
								{
									$objects[] = $object;
								}
							}
						}
					}
				}
			}
		}

		$this->base_path = $original_path;
		
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
	
	public function load($file, $params = array(), $debug = false)
	{
		if(!empty($file))
		{	
			$file = preg_replace('/.php$/', '', $file);
			
			$class = $file;
			
			if(!in_array($file, $this->reserved_files))
			{
				$class .= $this->class_suffix;
			}
			
			if(!class_exists($class) && file_exists($this->base_path . ucfirst($file) . '.php'))
			{
				require_once $this->base_path . ucfirst($file) . '.php';
			}
			
			if(!class_exists($class) && file_exists($this->base_path . strtolower($file) . '.php'))
			{
				require_once $this->base_path . strtolower($file) . '.php';
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

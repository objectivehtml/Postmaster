<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once PATH_THIRD.'postmaster/libraries/Base_class.php';
require_once PATH_THIRD.'postmaster/libraries/InterfaceBuilder/InterfaceBuilder.php';

abstract class Postmaster_core extends Base_class {
	
	
	/**
	 * The Filename
	 * 
	 * @var string
	 */
	 			
	protected $filename;
		
	
	/**
	 * Object Name
	 */
	 
	protected $name;
	
	
	/**
	 * Object Description
	 */
	 
	protected $description;
	
	
	/**
	 * Object Title
	 * 
	 * @var string
	 */
	 		 	
	protected $title;
	
	
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
	 * Settings Array
	 * 
	 * @var string
	 */
	 		 
	protected $settings = array();
	
	
	/**
	 * Default Settings Array
	 * 
	 * @var string
	 */
	 		 
	protected $default_settings = array();
		
	
	/**
	 * Default Settings Field Schema
	 * 
	 * @var string
	 */
	 		 	 
	protected $fields = array();
	
	
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
	
	
	/**
	 * Uninstall the API
	 *
	 * @access	public
	 * @return	
	 */
	 	
	public function uninstall()
	{
		return;
	}
	
	
	/**
	 * Get object title
	 *
	 * @access	public
	 * @return	string
	 */
	 	
	public function get_title()
	{
		return !empty($this->title) ? $this->title : $this->name;
	}
	

	/**
	 * Trim an array
	 *
	 * @access	public
	 * @param	array    	The array to be trimmed
	 * @return	array
	 */
	 
	public function trim_array($array, $prefix = '', $suffix = '')
	{
		foreach($array as $index => $value)
		{
			$array[$index] = $prefix . trim($value) . $suffix;
		}
		
		return $array;
	}
}
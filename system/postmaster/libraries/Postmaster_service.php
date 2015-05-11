<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'Base_service.php';
require_once 'Postmaster_base_lib.php';

class Postmaster_service extends Postmaster_base_lib {
	
	/**
	 * Base API Directory
	 * 
	 * @var string
	 */
	 
	protected $base_dir = 'services';
	
	
	/**
	 * Services
	 * 
	 * @var string
	 */
	 
	protected $services = array();
	
	
	/**
	 * Reserved File Names
	 * 
	 * @var string
	 */
	 
	protected $reserved_files = array();
	
		
	/**
	 * Default Service
	 * 
	 * @var string
	 */
	 
	protected $default_service = '';
	
		
	/**
	 * Class Suffix
	 * 
	 * @var string
	 */
	 
	protected $class_suffix = '_postmaster_service';
	

	/**
	 * Get a single service from the directory
	 *
	 * @access	public
	 * @param	mixed    A valid index or service name
	 * @return	mixed
	 */
	
	public function get_service($index = FALSE, $settings = FALSE)
	{		
		return parent::get_object($index, $settings);
	}
	
	
	/**
	 * Get the available services from the directory
	 *
	 * @access	public
	 * @return	array
	 */
	
	public function get_services($settings = FALSE)
	{	
		return parent::get_objects(array(
			'settings' => $settings
		));
	}	
	
	/**
	 * Total Services
	 *
	 * @access	public
	 * @return	int
	 */
	
	public function total_services()
	{
		return parent::total_objects();
	}
}
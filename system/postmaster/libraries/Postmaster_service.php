<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'Base_service.php';
require_once 'Postmaster_base_lib.php';

class Postmaster_service extends Postmaster_base_lib {
	
	/**
	 * Base File Path
	 * 
	 * @var string
	 */
	 
	protected $base_path = '../services';
	
	
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
	 * Construct
	 *
	 * @access	public
	 * @param	array 	Dynamically set properties
	 * @return	void
	 */
	
	public function __construct($data = array())
	{
		$this->base_path = PATH_THIRD . 'postmaster/services/';
		
		parent::__construct($data);
	}
	
	
	/**
	 * Get a single service from the directory
	 *
	 * @access	public
	 * @param	mixed    A valid index or service name
	 * @return	mixed
	 */
	
	public function get_service($index = FALSE)
	{		
		return parent::get_object($index);
	}
	
	
	/**
	 * Get the available services from the directory
	 *
	 * @access	public
	 * @return	array
	 */
	
	public function get_services()
	{	
		return parent::get_objects();
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
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once PATH_THIRD.'postmaster/libraries/Postmaster_core.php';

/**
 * Postmaster Service
 * 
 * @package		Postmaster
 * @author		Justin Kimbrell
 * @copyright	Copyright (c) 2012, Objective HTML
 * @link 		http://www.objectivehtml.com/
 * @version		1.0.99
 * @build		20120703
 */

abstract class Base_service extends Postmaster_core {
	
	public $id;
	
	abstract public function send($parsed_object, $parcel);
	
	public function default_settings()
	{
		return (object) $this->default_settings;
	}
	
	public function display_settings($settings, $obj)
	{
		return $this->build_table($settings, $obj);
	}
	
	public function get_settings($settings)
	{
		$default_settings = $this->default_settings();
		
		return isset($settings->{$this->name}) ? (object) array_merge((array) $default_settings, (array) $settings->{$this->name}) : $default_settings;
	}

	public function build_table($settings, $fields)
	{	
		$settings = $this->get_settings($settings);
		
		$this->IB->set_var_name($this->name);
		$this->IB->set_prefix('setting');
		$this->IB->set_use_array(TRUE);
		
		return $this->IB->table($this->fields, $settings, postmaster_table_attr());
	}	
}

class Postmaster_Service_Response extends Base_class {

	public  $parcel_id,
			$channel_id,
			$author_id,
			$entry_id,
			$gmt_date,
			$to_name,
			$to_email,
			$from_name,
			$from_email,
			$cc,
			$bcc,
			$service,
			$subject,
			$status,
			$message,
			$parcel;

	public function __construct($data = array())
	{		
		parent::__construct($data);		
	}
}
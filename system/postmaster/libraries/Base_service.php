<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once PATH_THIRD.'postmaster/libraries/Postmaster_base_api.php';

/**
 * Base Service
 * 
 * @package		Postmaster
 * @author		Justin Kimbrell
 * @copyright	Copyright (c) 2012, Objective HTML
 * @link 		http://www.objectivehtml.com/
 * @version		1.3.0
 * @build		20130317
 */

abstract class Base_service extends Postmaster_base_api {
	
	/**
	 * Service ID
	 */
	 
	public $id;
	
	/**
	 * Current GMT Time
	 */
	 
	protected $now;
	
	
	/**
	 * cURL Object
	 */
	 
	protected $curl;
	
	
	/**
	 * Uuid generator
	 */
	 
	protected $uid;
	
	
	/**
	 * Postmaster library
	 */
	 
	protected $lib;


	/**
	 * Email Service Response
	 */
	 
	protected $response;

	
	/**
	 * Variable prefix
	 * 
	 * @var string
	 */
	 		 
	protected $var_prefix = 'service';
	

	/**
	 * Fields to parse
	 * 
	 * @var string
	 */
	 		 
	protected static $parse_fields = array(
		'to_name',
		'to_email',
		'from_name',
		'from_email',
		'cc',
		'bcc',
		'subject',
		'message',
		'html_message', // New in v1.4
		'plain_message', // New in v1.4
		'post_date_specific',
		'post_date_relative',
		'send_every',
		'extra_conditionals'
	);
	
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
		$this->EE->load->driver('interface_builder');
		
		$this->curl = new Postmaster_curl();
		$this->uid  = new Uuid();
		$this->lib  = $this->EE->postmaster_lib;
		$this->now  = $this->EE->localize->now;	
	}
	
	
	/**
	 * Send the parcel with the parsed object
	 *
	 * @access	public
	 * @param	object 	The parsed the object
	 * @param	object 	The parcel object
	 * @return	object	Returns a Postmaster_Service_Response object
	 */
	abstract public function send($parsed_object, $parcel);
	
	
	/**
	 * Get the action_url from a class and method
	 *
	 * @access	public
	 * @param	string  The class name
	 * @param	string 	The method name  
	 * @return	string
	 */
	 	
	public function action_url($class, $method)
	{
		return $this->EE->postmaster_lib->current_url('ACT', $this->EE->channel_data->get_action_id($class, $method));
	}
	
	
	/**
	 * Get the delegate action url
	 *
	 * @access	public
	 * @return	string
	 */
	 
	public function delegate_url()
	{
		return $this->action_url('Postmaster', 'delegate_action');
	}

	
	/**
	 * Get the call_url from a class and method
	 *
	 * @access	public
	 * @param	string  The class name
	 * @param	string 	The method name  
	 * @return	string
	 */
	 	
	public function call_url($method, $params = array())
	{
		$base_url = $this->action_url('postmaster_mcp', 'call');

		$params = array_merge(
			array(
				'service'        => $this->name,
				'service_method' => $method
			),
			$params
		);

		return $base_url . '&' . http_build_query($params);
	}

	
	/**
	 * Output a JSON string
	 *
	 * @access	public
	 * @param 	mixed  	Some value to ouput as JSON
	 * @return	string
	 */
	 
	public function json($data)
	{
		header('Content-header: application/json');

		exit(json_encode($data));
	}

	/**
	 * Show a system error
	 *
	 * @access	public
	 * @param 	string 	An error string to ouput
	 * @return	void
	 */
	 
	public function show_error($error)
	{
		show_error('<b>'.$this->name.' Service</b> - '.$error);
	}
}

if(!class_exists('Postmaster_Service_Response'))
{
	/**
	 * Postmaster Service Response
	 */
	 
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
				$html_message, // New in v1.4
				$plain_message, // New in v1.4
				$parcel;
	
		public function __construct($data = array())
		{		
			parent::__construct($data);		
		}
	}
}
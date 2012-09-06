<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'Base_class.php';

abstract class Base_hook extends Base_class {
	
	protected $title;
	
	protected $name;
	
	public $settings = array();
	
	protected $var_prefix = 'hook';
	
	protected $file_name = 'hook';
	
	protected $default_settings = array(
		'end_script' => FALSE
	);
	
	protected $fields = array(
		
		'end_script' => array(
			'label' => 'End Script',
			'id'	=> 'end_script',
			'type'	=> 'radio',
			'description' => 'Setting this value to true will stop the script from finishing (after the email is sent).',
			'settings' => array(
				'options' => array(
					FALSE => 'False',
					TRUE  => 'True'
				)
			)
		)
	);	
	
	protected static $parse_fields = array(
		'to_name',
		'to_email',
		'from_name',
		'from_email',
		'cc',
		'bcc',
		'subject',
		'message',
		'post_date_specific',
		'post_date_relative',
		'send_every'
	);
	
	public function __construct($params = array())
	{
		parent::__construct($params = array());
		
		$this->EE =& get_instance();
		
		$this->EE->load->driver('interface_builder');
		
		$this->name      = strtolower(str_replace('_postmaster_hook', '', get_class($this)));
		$this->file_name = ucfirst($this->name).'.php';
		$this->IB	     = $this->EE->interface_builder;	
		$this->settings  = $this->get_settings();
				
		$this->IB->set_var_name($this->name);
		$this->IB->set_prefix('setting');
		$this->IB->set_use_array(TRUE);
	}	
	
	public function pre_process($vars = array())
	{
		return;
	}
	
	public function post_process($responses = array(), $vars = array())
	{
		return $responses;
	}
		
	public function trigger($hook, $vars = array(), $return_data = 'Undefined')
	{
		$response = array();
		
		foreach($this->get_installed_hooks($hook) as $hook)
		{	
			$response[] = $this->send($hook, $vars, $return_data);
		}
		
		return $response;
	}
	
	public function send($hook, $vars = array(), $return_data = 'Undefined')
	{	
		$settings		  = $hook['settings'];
		$name             = !empty($hook['installed_hook']) ? $hook['installed_hook'] : $hook['user_defined_hook'];
		
		$parsed_hook      = $this->parse($hook, $vars);
		
		$hook['settings'] = (object) $settings;		
		$end_script 	  = isset($hook['settings']->$name->end_script) ? (bool) $hook['settings']->$name->end_script : FALSE;
	
		$obj = array(
			'end_script' => $end_script,
			'response'   => $this->EE->postmaster_lib->send($parsed_hook, $hook)
		);
		
		if($return_data != 'Undefined')
		{
			$obj['return_data'] = $return_data;	
		}
		
		return (object) $obj;
	}
	
	public function get_installed_hooks($hook)
	{
		return $this->EE->postmaster_model->get_installed_hooks($hook);
	}
	
	/*
	public function get_settings()
	{
		$default_settings = $this->get_default_settings();
		
		var_dump($this->settings);exit();
		
		return isset($this->settings->{$this->name}) ? (object) array_merge((array) $default_settings, (array) $this->settings->{$this->name}) : $default_settings;
	} 
	*/
	
	public function parse($hook, $vars = array())
	{
		unset($hook['settings']);
		
		$vars = $this->EE->channel_data->utility->add_prefix($this->var_prefix, $vars);
		
		$member_data = $this->EE->channel_data->utility->add_prefix('member', $this->EE->postmaster_model->get_member());
		
		$vars = array_merge($vars, $member_data);
		
		return $this->EE->channel_data->tmpl->parse_array($hook, $vars);
	}
	
	public function display_settings($data = array())
	{
		if(count($this->fields) == 0)
		{		
			return FALSE;
		}
		
		$settings = isset($data->{$this->name}) ? $data->{$this->name} : $this->get_default_settings();
		
		$this->IB->set_var_name($this->name);
		$this->IB->set_prefix('setting');
		$this->IB->set_use_array(TRUE);
				
		return $this->IB->table($this->fields, $settings, postmaster_table_attr());
	}
	
	public function end_script($response)
	{
		return $this->EE->postmaster_hook->end_script($response);
	}
}
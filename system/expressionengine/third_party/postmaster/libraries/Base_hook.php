<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'Base_class.php';

abstract class Base_hook extends Base_class {
	
	protected $title;
	
	protected $name;
	
	protected $settings;
	
	protected $var_prefix = 'hook';
	
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
	
	public function __construct()
	{
		$this->EE =& get_instance();
		
		parent::__construct();
		
		$this->EE->load->library('interface_builder');
		
		$this->name     = strtolower(str_replace('_postmaster_hook', '', get_class($this)));
		$this->IB	    = $this->EE->interface_builder;	
		$this->IB->meta = array('hook' => $this->name);	
		$this->settings = $this->get_settings();
	}	
	
	public function trigger($hook, $args = array())
	{
		$installed_hooks = $this->get_installed_hooks($hook);
		
		$response = array();
		
		foreach($installed_hooks as $hook)
		{	
			$name             = !empty($hook['installed_hook']) ? $hook['installed_hook'] : $hook['user_defined_hook'];
			$hook             = $this->parse($hook);
			$settings         = !empty($hook['settings']) ? json_decode($hook['settings']) : array();
			
			$hook['settings'] = (object) $settings;
			
			$end_script = isset($hook['settings']->$name->end_script) ? (bool) $hook['settings']->$name->end_script : FALSE;
			
			$response[] = (object) array(
				// *optional* 'return_data' => 'Some return data'
				'end_script' => $end_script,
				'response'   => $this->EE->postmaster_lib->send($hook, $hook)
			);
		}
		
		return $response;
	}
	
	public function get_installed_hooks($hook)
	{
		$this->EE->db->order_by('priority', 'asc');
		$this->EE->db->where("(installed_hook != '' AND installed_hook = '$hook') OR (installed_hook = '' AND user_defined_hook = '$hook')", NULL, FALSE);
		
		return $this->EE->db->get('postmaster_hooks')->result_array();
	}
	
	public function get_settings($settings = array())
	{
		$default_settings = $this->get_default_settings();
		
		return isset($settings->{$this->name}) ? (object) array_merge((array) $default_settings, (array) $settings->{$this->name}) : $default_settings;
	}

	public function send($hook)
	{
		$hook = $this->parse($hook);
	}
	
	public function parse($hook, $vars = array())
	{
		$vars = $this->EE->channel_data->utility->add_prefix($this->var_prefix, $vars);
		
		return $this->EE->channel_data->tmpl->parse_array($hook, $vars);
	}
	
	public function display_settings($settings = array())
	{
		if(count($this->fields) == 0)
		{		
			return FALSE;
		}
		
		$settings = $this->get_settings($settings);
		
		$this->IB->set_instance('hook');
		
		return $this->IB->table($this->fields, $settings, postmaster_table_attr());
	}
	
	public function save_settings()
	{
		return '';
	}
}
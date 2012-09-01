<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'Template_Base.php';

class Template_Hook extends Template_Base {

	public $priority = 1;
	
	public $installed_hook = '';
	
	public $user_defined_hook;
		
	public function __construct($params = array())
	{
		parent::__construct($params);
		
		$this->EE->load->driver('interface_builder');
		$this->EE->load->library('postmaster_hook', array(
			'base_path' => PATH_THIRD.'postmaster/hooks/'
		));
		
		$method = 'create_hook_action';
		
		if($this->EE->input->get('id'))
		{
			$method = 'edit_hook_action';	
		}
		
		$this->action = $this->current_url('ACT', $this->EE->channel_data->get_action_id('Postmaster_mcp', $method));
		$this->button = 'Save Hook';
		$this->IB	  = $this->EE->interface_builder;
	}
	
	public function hooks()
	{
		return $this->EE->postmaster_hook->get_hooks();
	}
	
	public function priorities()
	{
		return array(
			1,
			2,
			3,
			4,
			5,
			6,
			7,
			8,
			9,
			10
		);
	}
		
	public function fields()
	{
		return array();
	}
}
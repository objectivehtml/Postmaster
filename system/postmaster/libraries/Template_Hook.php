<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'Template_Base.php';

class Template_Hook extends Template_Base {

	public $priority = 1;
	
	public $installed_hook = '';
	
	public $user_defined_hook;
	
	public $enabled = 1;
		
	public function __construct($params = array())
	{
		parent::__construct($params);
		
		$this->parser_url = $this->parser_url . '&prefix=hook';
		
		$this->EE->load->driver('Interface_builder');
		
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
	
	public function hooks($reserved = FALSE)
	{
		$return = array();
		
		foreach($this->EE->postmaster_hook->get_hooks() as $hook)
		{
			if(!$reserved && !in_array($hook->get_filename(), $this->EE->postmaster_hook->get_reserved_files()) || $reserved)
			{
				$return[] = $hook;
			}
		}
		
		return $return;
	}
	
	public function is_enabled()
	{
		return $this->enabled != 0 ? TRUE : FALSE;
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
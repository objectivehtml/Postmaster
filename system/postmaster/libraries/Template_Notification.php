<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'Template_Base.php';

class Template_Notification extends Template_Base {

	public $notification;
	
	public $enabled;
	
	public function __construct($params = array())
	{
		parent::__construct($params);
		
		$this->parser_url = $this->parser_url . '&prefix=notice';
		
		$this->EE->load->driver('Interface_builder');
		$this->EE->load->library('postmaster_notification', array(
			'base_path' => PATH_THIRD.'postmaster/notifications/'
		));
		
		$method = 'create_notification_action';
		
		if($this->EE->input->get('id'))
		{
			$method = 'edit_notification_action';	
		}
		
		$this->action = $this->current_url('ACT', $this->EE->channel_data->get_action_id('Postmaster_mcp', $method));
		$this->button = 'Save Notification';
		$this->IB	  = $this->EE->interface_builder;
	}
	
	public function is_enabled()
	{
		return $this->enabled != 0 ? TRUE : FALSE;
	}
	
	public function notifications($reserved = FALSE)
	{
		$return = array();
		
		foreach($this->EE->postmaster_notification->get_notifications($this->settings) as $notification)
		{
			if(!$reserved && !in_array($notification->get_filename(), $this->EE->postmaster_notification->get_reserved_files()) || $reserved)
			{
				$return[] = $notification;
			}	
		}
		
		return $return;
	}
}
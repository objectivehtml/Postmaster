<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Postmaster_installer {
	
	public function __construct()
	{	
		$this->EE =& get_instance();
		
		$this->EE->load->library('postmaster_lib');
		
		$this->EE->load->library('postmaster_hook', array(
			'base_path' => PATH_THIRD . 'postmaster/hooks/'
		));
		
		$this->EE->load->library('postmaster_notification', array(
			'base_path' => PATH_THIRD . 'postmaster/notifications/'
		));
		
		$this->EE->load->library('postmaster_service', array(
			'base_path' => PATH_THIRD . 'postmaster/services/'
		));
	}
	
	public function install()
	{
		return $this->run('install');
	}	
	
	public function update($version)
	{
		return $this->run('update', $version);
	}	
	
	public function uninstall()
	{
		return $this->run('uninstall');
	}	
	
	private function run($method, $version = FALSE)
	{
		$services      = $this->EE->postmaster_service->get_services();
		$hooks         = $this->EE->postmaster_hook->get_hooks();		
		$notifications = $this->EE->postmaster_notification->get_notifications();	
		
		foreach(array_merge($services, $hooks, $notifications) as $obj)
		{
			if(is_object($obj))
			{
				$obj->$method($version);
			}
		}
	}
}
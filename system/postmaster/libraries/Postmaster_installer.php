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

		$this->EE->load->library('postmaster_task', array(
			'base_path' => PATH_THIRD . 'postmaster/tasks/'
		));
	}
	
	public function version_update($version)
	{		
		// Version Specific Update Routines
		
		if(version_compare($version, '1.1.99.4', '<'))
		{
			if(!class_exists('Postmaster_lib'))
			{				
				require_once(PATH_THIRD.'postmaster/libraries/Postmaster_lib.php');
			}
			
			$this->EE->postmaster_lib = new Postmaster_lib();
			$this->EE->postmaster_model->assign_site_id();
		}
		
		if(version_compare($version, '1.3.2.1', '<'))
		{
			$this->EE->db->where('date', '0000-00-00 00:00:00');
			
			$update_queue = $this->EE->db->get('postmaster_queue');
			
			foreach($update_queue->result() as $row)
			{
				$data['date']      = date('Y-m-d H:i:s', $row->gmt_date);
				$data['send_date'] = date('Y-m-d H:i:s', $row->gmt_send_date);
				
				$this->EE->db->where('id', $row->id);
				$this->EE->db->update('postmaster_queue', $data);
			}
		}

		if(version_compare($version, '1.4.1', '>'))
		{
			$this->EE->db->where('class', 'Postmaster_ext');
			$this->EE->db->where('method', 'route_hook');
			$this->EE->db->update('extensions', array(
				'method' => 'trigger_hook'
			));

			$this->EE->db->where('class', 'Postmaster_ext');
			$this->EE->db->where('method', 'route_task_hook');
			$this->EE->db->update('extensions', array(
				'method' => 'trigger_task_hook'
			));
		}
	}
	
	public function install_action($class, $method)
	{
		$action = array(
			'class'  => $class,
			'method' => $method
		);
		
		$this->EE->db->where(array(
			'class'  => $action['class'],
			'method' => $action['method']
		));
		
		$existing = $this->EE->db->get('actions');

		if($existing->num_rows() == 0)
		{
			$this->EE->db->insert('actions', $action);
		}
	}
	
	public function install_hook($class, $method, $hook, $priority = 10, $settings = '')
	{
		$this->EE->db->where(array(
			'class'  => $class,
			'method' => $method,
			'hook' 	 => $hook
		));
		
		$existing = $this->EE->db->get('extensions');

		if($existing->num_rows() == 0)
		{
			$this->EE->db->insert(
				'extensions',
				array(
					'class' 	=> $class,
					'method' 	=> $method,
					'hook' 		=> $hook,
					'settings' 	=> $settings,
					'priority' 	=> $priority,
					'version' 	=> POSTMASTER_VERSION,
					'enabled' 	=> 'y',
				)
			);
		}
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
		$tasks 		   = $this->EE->postmaster_task->get_tasks();	
		
		foreach(array_merge($services, $hooks, $notifications, $tasks) as $obj)
		{
			if(is_object($obj))
			{
				$obj->$method($version);
			}
		}
	}
}
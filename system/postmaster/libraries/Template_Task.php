<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'Template_Base.php';

class Template_Task extends Template_Base {

	public $priority = 1;
	
	public $task = '';
	
	public $user_defined_hook;
		
	public $enabled = 1;
	
	public function __construct($params = array())
	{
		parent::__construct($params);
		
		$this->EE->load->library('postmaster_task', array(
			'base_path' => PATH_THIRD.'postmaster/tasks/'
		));
		
		$method = 'create_task_action';
		
		if($this->EE->input->get('id'))
		{
			$method = 'edit_task_action';	
		}
		
		$this->action = $this->current_url('ACT', $this->EE->channel_data->get_action_id('Postmaster_mcp', $method));
		$this->button = 'Save Task';
	}
	
	public function tasks($reserved = FALSE)
	{
		$return = array();
		
		foreach($this->EE->postmaster_task->get_tasks() as $task)
		{
			if(!$reserved && !in_array($task->get_filename(), $this->EE->postmaster_task->get_reserved_files()) || $reserved)
			{
				$return[] = $task;
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
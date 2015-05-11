<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Postmaster
 * 
 * @package		Postmaster
 * @author		Justin Kimbrell
 * @copyright	Copyright (c) 2012, Objective HTML
 * @link 		http://www.objectivehtml.com/postmaster
 * @version		1.2.0
 * @build		20121217
 */

require 'config/postmaster_config.php';

class Postmaster_ext {

    public $name       		= 'Postmaster';
    public $version        	= POSTMASTER_VERSION;
    public $description    	= 'Easily create e-mail template and automatically generated emails every time an entry is submitted.';
    public $settings_exist 	= 'n';
  	public $docs_url       	= 'http://www.objectivehtml.com';
	public $settings 		= array();
	public $required_by 	= array('module');
	
	public $new_entry		= TRUE;
	
	public function __construct()
	{
	   	$this->EE =& get_instance();

        $this->settings = array();

        if(isset($this->EE->extensions->in_progress) && !empty($this->EE->extensions->in_progress))
        {
			$this->EE->session->set_cache('postmaster', 'in_progress', $this->EE->extensions->in_progress);
	    }
    }

	public function settings()
	{
		return '';
	}
	
	public function trigger_task_hook()
	{
		$this->EE->load->library('postmaster_lib');
		$this->EE->load->library('postmaster_hook', array(
			'base_path' => PATH_THIRD.'postmaster/hooks/'
		));

		$hook      = $this->EE->postmaster_lib->get_hook_in_progress();
		$args      = func_get_args();
		$responses = $this->EE->postmaster_lib->trigger_task_hook($hook, $args);
		$return    = $this->EE->postmaster_hook->return_data($responses);
		
		$this->EE->extensions->end_script = $this->EE->postmaster_hook->end_script($responses);
			
		if($return != 'Undefined')
		{
			return $return;
		}
		
		return;
	}
	
	public function trigger_hook()
	{
		$this->EE->load->library('postmaster_lib');
		$this->EE->load->library('postmaster_hook', array(
			'base_path' => PATH_THIRD.'postmaster/hooks/'
		));
		
		$hook      = $this->EE->postmaster_lib->get_hook_in_progress();		
		$args      = func_get_args();
		$responses = $this->EE->postmaster_lib->trigger_hook($hook, $args);
		$return    = $this->EE->postmaster_hook->return_data($responses);

		$this->EE->extensions->end_script = $this->EE->postmaster_hook->end_script($responses);
		
		if($return !== 'Undefined')
		{
			return $return;
		}
		
		return;
	}
		
	public function route_hook()
	{
		return $this->trigger_hook();
	}
		
	public function entry_submission_start($channel_id, $autosave)
	{ 	
	
	}

	public function entry_submission_ready($meta, $data, $autosave)
	{		
		$trigger = 'new';

		if(isset($data['entry_id']) && (int) $data['entry_id'] > 0) 
		{
			$trigger = 'edit';
		}
		
		$this->EE->session->set_cache('postmaster', 'entry_trigger', $trigger);
	}
	
	public function entry_submission_end($entry_id, $meta, $data)
	{			
		$this->EE->load->library('postmaster_lib');		
		
		$this->EE->postmaster_lib->validate_channel_entry($entry_id, $meta, $data);

		return $data;
	}
		
	public function trigger_task_hook_hook()
	{
		return $this->trigger_hook();
	}
	
	public function route_task_hook()
	{
		return $this->trigger_task_hook();
	}
	 
	/**
	 * Activate Extension
	 *
	 * This function enters the extension into the exp_extensions table
	 *
	 * @return void
	 */
	function activate_extension()
	{	    
	    return TRUE;
	}
	
	/**
	 * Update Extension
	 *
	 * This function performs any necessary db updates when the extension
	 * page is visited
	 *
	 * @return  mixed   void on update / false if none
	 */
	function update_extension($current = '')
	{
	    if ($current == '' OR $current == $this->version)
	    {
	        return FALSE;
	    }
	
	    if ($current < '1.0')
	    {
	        // Update to version 1.0
	    }
	
	    $this->EE->db->where('class', __CLASS__);
	    $this->EE->db->update('extensions', array('version' => $this->version));
	}
	
	/**
	 * Disable Extension
	 *
	 * This method removes information from the exp_extensions table
	 *
	 * @return void
	 */
	function disable_extension()
	{
	    $this->EE->db->where('class', __CLASS__);
	    $this->EE->db->delete('extensions');
	}
	
}
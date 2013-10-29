<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Postmaster_email_form_submit_postmaster_task extends Base_task {
	
	protected $title = 'Postmaster Email Form Submit';
	
	protected $hooks = array(
		array(
			'hook'   => 'postmaster_email_form_submit',
			'method' => 'postmaster_email_form_submit',
			'priority' => 1
		)
	);

	protected $enable_cron = TRUE;

	public function __construct()
	{
		parent::__construct();
		
	}
	
	public function trigger_cron()
	{
		exit('trigger cron');
	}

	public function postmaster_email_form_submit($email, $entry, $custom_data = array())
	{
		return 'Undefined';
	}
}
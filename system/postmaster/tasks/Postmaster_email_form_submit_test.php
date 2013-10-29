<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Postmaster_email_form_submit_test_postmaster_task extends Base_task {
	
	protected $title = 'Postmaster Email Form Submit (Test)';
	
	protected $hooks = array(
		array(
			'hook'   => 'postmaster_email_form_submit_test',
			'method' => 'postmaster_email_form_submit_test',
			'priority' => 1
		)
	);

	public function __construct()
	{
		parent::__construct();
		
	}
		
	public function postmaster_email_form_submit_test($email, $entry, $custom_data = array())
	{
		exit('asd');
	}
}
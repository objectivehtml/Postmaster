<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mailchimp_member_signup_postmaster_task extends Base_task {
	
	protected $title = 'Mailchimp Member Signup';

	protected $enable_cron = TRUE;

	protected $hooks = array(
		array(
			'method' 	=> 'your_hook_method',
			'hook'   	=> 'some_hook_name',
			'priority'	=> 10
		),
		array(
			'method' 	=> 'your_hook_method_2',
			'hook'   	=> 'some_hook_name_2',
			'priority'	=> 10
		),
		array(
			'method' 	=> 'your_hook_method',
			'hook'   	=> 'some_hook_name',
			'priority'	=> 10
		)
	);
	
	protected $fields = array(
		'first_name_field' => array(
			'label'       => 'First Name Field',
			'description' => 'This is the name of the member field that stores the member\'s first name. If defined, the value will be used for the subscribers.'
		),
		'last_name_field' => array(
			'label'       => 'Last Name Field',
			'description' => 'This is the name of the member field that stores the member\'s last name. If defined, the value will be used for the subscribers.'
		)
	);
	
	public function __construct()
	{
		parent::__construct();
	}

	public function your_hook_method()
	{
		// Do something for the 'some_hook_name' hook
	}

	public function your_hook_method_2()
	{
		
		// Do something for the 'some_hook_name_2' hook
	}

	public function trigger_cron()
	{
		// Do something for the cron job
	}
}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mailchimp_member_signup_test_postmaster_task extends Base_task {
	
	protected $name  = 'mailchimp_member_signup_test';

	protected $title = 'Mailchimp Member Signup (Test)';

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
		//exit('1');
	}

	public function your_hook_method_2()
	{
		//exit('2');
	}

	public function trigger_cron()
	{
		if ($this->EE->extensions->active_hook('some_hook_name') === TRUE)
	    {
	        $str = $this->EE->extensions->call('some_hook_name');
	    }

		if ($this->EE->extensions->active_hook('some_hook_name_2') === TRUE)
	    {
	        $str = $this->EE->extensions->call('some_hook_name_2');
	    }
	}
}
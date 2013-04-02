<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mailchimp_member_signup_postmaster_task extends Base_task {
	
	protected $title = 'Mailchimp Member Signup';
	
	protected $fields = array(
		'first_name_field' => array(
			'description' => 'This is the name of the member field that stores the member\'s first name. If defined, the value will be used for the subscribers.'
		),
		'last_name_field' => array(
			'description' => 'This is the name of the member field that stores the member\'s last name. If defined, the value will be used for the subscribers.'
		)
	);
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function get_name()
	{
		return $this->title;
	}
}
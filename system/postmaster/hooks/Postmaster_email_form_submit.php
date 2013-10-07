<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Postmaster_email_form_submit_postmaster_hook extends Base_hook {
	
	protected $title = 'Postmaster Email Form Submit';
	
	public function __construct()
	{
		parent::__construct();
		
	}

	public function trigger($email, $entry, $custom_data = array())
	{
		$parse_vars = array_merge(array(
			'email' => $email
		), $custom_data);

		return parent::send($parse_vars, FALSE, $entry);
	}
}
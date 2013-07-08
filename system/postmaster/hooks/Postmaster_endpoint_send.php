<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Postmaster_endpoint_send_postmaster_hook extends Base_hook {
	
	protected $title = 'Postmaster Endpoint Send Email';
	
	public function __construct()
	{
		parent::__construct();
		
	}

	public function parse($array, $vars = array(), $member_data = FALSE, $entry_data = array())
	{
		$return = parent::parse($array, $vars, $member_data, $entry_data);

		foreach($return as $index => $value)
		{
			if(empty($value) && isset($vars[$index]) && !empty($vars[$index]))
			{
				$return[$index] = $vars[$index];
			}
		}

		return $return;
	}
		
	public function trigger($vars = array())
	{
		return $this->send($vars);
	}
}
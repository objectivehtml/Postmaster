<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Member_member_register_postmaster_hook extends Base_hook { 

	protected $title = 'Member member register';
		
	public function __construct($params = array())
	{
		parent::__construct(array());
	}

	public function trigger($data, $member_id) 
	{	
		$parse_vars = $data; 

		$member = $this->channel_data->get_member($member_id)->row_array(); 

		$member['password'] = $_POST['password']; 
		
		$parse_vars = array_merge($parse_vars, $member);

		return $this->send($parse_vars, $member); 
	} 
}
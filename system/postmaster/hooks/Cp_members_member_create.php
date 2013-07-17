<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cp_members_member_create_postmaster_hook extends Base_hook {
	
	protected $title = 'CP Member Registration';
	
	public function __construct()
	{
		parent::__construct();
		
	}
		
	public function trigger($member_id, $data)
	{	
		$member = $this->channel_data->get_member($member_id)->row_array();
		$member['password'] = $_POST['password'];

		return $this->send($member, $member);
	}
}
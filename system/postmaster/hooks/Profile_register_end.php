<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Profile_register_end_postmaster_hook extends Base_hook {
	
	protected $title = 'Profile Register End';
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function trigger($member_id, $member_data, $entry_data, $success, $is_admin)
	{
		if($success)
		{
			$entry = $this->channel_data->get_channel_entry($entry_data['entry_id'])->row_array();
			
			$password = isset($member_data['password']) ? $member_data['password'] : NULL;
			
			$vars = array_merge($entry_data, array(
				'entry_id'  => $entry_data['entry_id'],
				'member_id' => $member_id,
				'is_admin'  => $is_admin,
				'password'  => $password
			));
			
			parent::send($vars, FALSE, $entry);
		}	
	}
}
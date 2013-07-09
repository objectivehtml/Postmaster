<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bwf_notify_users_postmaster_hook extends Base_hook {

	protected $title = 'Better Workflow Notify Users';
	
	public function __construct()
	{
		parent::__construct();
		
	}
		
	public function trigger($channel_id, $entry_id, $entry_title)
	{	
		$entry_data  = $this->channel_data->get_entry($entry_id);
		$member_data = $this->channel_data->get_member($entry_data->row('author_id'));
		
		$parse_vars = array(
			'channel_id'  => $channel_id,
			'entry_id'    => $entry_id,
			'entry_title' => $entry_title
		);

		return $this->send($parse_vars, $member_data->row_array(), $entry_data->row_array());
	}
}
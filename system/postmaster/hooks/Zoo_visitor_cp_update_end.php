<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Zoo_visitor_cp_update_end_postmaster_hook extends Base_hook {
	
	protected $title = 'Zoo Visitor CP Update End';
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function trigger($member_data, $member_id)
	{
		$entry  = $this->channel_data->get_entry($member_data['entry_id']);
		$member = $this->channel_data->get_member($member_id);

		parent::send($member_data, $member->row_array(), $entry->row_array());
	}
}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Zoo_visitor_register_end_postmaster_hook extends Base_hook {
	
	protected $title = 'Zoo Visitor Register End';
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function trigger($member_data, $member_id)
	{
		$member = $this->channel_data->get_member($member_id);

		parent::send($member_data, $member->row_array());
	}
}
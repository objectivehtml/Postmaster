<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Member_select_save_row_postmaster_hook extends Base_hook {
	
	protected $title = 'Member Select Save Row';
	
	public function __construct()
	{
		parent::__construct();
		
	}
		
	public function trigger($value, $label)
	{
		$parse_vars = array();
		$member     = $this->EE->channel_data->get_member($value)->row();
		
		return parent::send($parse_vars, $member);
	}
}
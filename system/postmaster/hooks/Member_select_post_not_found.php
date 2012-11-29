<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class member_select_post_not_found_postmaster_hook extends Base_hook {
	
	protected $title = 'Member Select Not Found (Post Save)';
	
	public function __construct()
	{
		parent::__construct();
		
	}
		
	public function trigger($data, $type, $entry_id, $field_id)
	{
		$parse_vars = array(
			'not_found' => $data,
			'entry_id'  => $entry_id,
			'field_id'  => $field_id,
			$type		=> $data
		);
		
		return parent::send($parse_vars);
	}
}
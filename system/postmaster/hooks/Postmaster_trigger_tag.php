<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Postmaster_trigger_tag_postmaster_hook extends Base_hook {
	
	protected $title = 'Postmaster Trigger Tag';
	
	public function __construct()
	{
		parent::__construct();
		
	}
		
	public function trigger($tagdata, $entry)
	{
		$parse_vars = array(
			'tagdata' => $tagdata
		);
		
		return parent::send($parse_vars, FALSE, $entry, $tagdata);
	}
}
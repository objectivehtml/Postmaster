<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Member_member_login_single_postmaster_hook extends Base_hook {
	
	protected $title = 'Member Login Single';
	
	public $fields = array(
		
		'test' => array(
			'name'  => 'test',
			'label' => 'test',
			'id'	=> 'test',
			'type'	=> 'input'
		)
	);	
	
	public function __construct()
	{
		parent::__construct();
		
	}
}
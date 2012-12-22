<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Basic_postmaster_notification extends Base_notification {
	
	public $title = 'Basic Notification';
	
	public function __construct($params = array())
	{
		parent::__construct($params);
	}	
}
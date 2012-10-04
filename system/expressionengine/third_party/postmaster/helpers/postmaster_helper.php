<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if(!function_exists('postmaster_table_attr'))
{
	function postmaster_table_attr() 
	{
		return array(
			'class'       => 'mainTable padTable',
			'border'      => 0,
			'cellpadding' => 0,
			'cellspacing' => 0
		);
	}
}

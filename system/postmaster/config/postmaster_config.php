<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*------------------------------------------
 *	Version
/* -------------------------------------- */

$config['postmaster_version'] = '1.6.1';

if(!defined('POSTMASTER_VERSION'))
{	
	define('POSTMASTER_VERSION', $config['postmaster_version']);
}

/*------------------------------------------
 *	Postmaster Debug (TRUE|FALSE)
/* -------------------------------------- */

$config['postmaster_debug'] = config_item('postmaster_debug') ? 
							  config_item('postmaster_debug') : 
							  (config_item('debug') != '0' ? TRUE : FALSE);


/*------------------------------------------
 *	Postmaster Base URL
 *  - Override the default current_url with
      one you define.
/* -------------------------------------- */

$config['postmaster_base_url'] = config_item('postmaster_base_url');
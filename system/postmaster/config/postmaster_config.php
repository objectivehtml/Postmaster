<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*------------------------------------------
 *	Version
/* -------------------------------------- */

$config['postmaster_version'] = '1.1.99.4';

if(!defined('POSTMASTER_VERSION'))
{	
	define('POSTMASTER_VERSION', $config['postmaster_version']);
}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Postmaster
 * 
 * @package		Postmaster
 * @author		Justin Kimbrell
 * @copyright	Copyright (c) 2012, Objective HTML
 * @link 		http://www.objectivehtml.com/postmaster
 * @version		1.1.0
 * @build		20120718
 */

require_once 'delegates/Base_Delegate.php';

class Postmaster extends Base_Delegate {
	
	/**
		* Adds delegate support to previous version of EE
		*
		* @access	public
		* @return	object
	*/
	
	public function delegate()
	{
		$delegate = $this->tag_part(2);
		$method	  = $this->tag_part(3);
		
		$this->EE->TMPL->tagparts[1] = $delegate;
		$this->EE->TMPL->tagparts[2] = $method;
		
		return $this->run($delegate);
	}
	
}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Postmaster
 * 
 * @package		Postmaster
 * @author		Justin Kimbrell
 * @copyright	Copyright (c) 2012, Objective HTML
 * @link 		http://www.objectivehtml.com/postmaster
<<<<<<< HEAD
 * @version		1.0.98
 * @build		20120609
=======
 * @version		1.0.981
 * @build		20120612
>>>>>>> refs/heads/dev
 */

require_once 'delegates/Base_Delegate.php';

<<<<<<< HEAD
class Postmaster extends Base_Delegate {}
=======
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
>>>>>>> refs/heads/dev

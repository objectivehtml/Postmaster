<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Endpoint Delegate
 * 
 * @package		Delegates
 * @author		Justin Kimbrell
 * @copyright	Copyright (c) 2012, Objective HTML
 * @link 		http://www.objectivehtml.com/
 * @version		1.0.0
 * @build		20120708
 */

class Endpoint_postmaster_delegate extends Postmaster_base_delegate {

	public $name = 'Endpoint';

	public $description = '';

	protected $to_name = FALSE;

	protected $to_email = FALSE;

	protected $from_name = FALSE;

	protected $from_email = FALSE;

	protected $reply_to = FALSE;

	protected $cc = FALSE;

	protected $bcc = FALSE;

	protected $subject = FALSE;

	protected $html_message = FALSE;

	protected $plain_message = FALSE;

	public function email()
	{
		$this->_get_vars();

		$data = array(
			'to_name'  		=> $this->to_name,
			'to_email' 		=> $this->to_email,
			'from_name'  	=> $this->from_name,
			'from_email' 	=> $this->from_email,
			'reply_to'   	=> $this->reply_to,
			'cc'		 	=> $this->cc,
			'bcc'		 	=> $this->bcc,
			'subject'	 	=> $this->subject,
			'html_message'  => $this->html_message,
			'plain_message' => $this->plain_message
		);

		// -------------------------------------------
	    //  'Postmaster_endpoint_send' hook
	    //   - Pass a data array to Postmaster that will be used to send emails
	    //
	        if ($this->EE->extensions->active_hook('postmaster_endpoint_send'))
	        {
	            $this->EE->extensions->call('postmaster_endpoint_send', $data);
	        }
	    //
	    // -------------------------------------------

		if($return = $this->param('return'))
		{
			$this->EE->functions->redirect($return);
		}
	}

	private function _get_vars()
	{
		$prefix    = $this->param('prefix', 'def:');
		$tmpl_vars = $this->EE->functions->assign_variables($this->EE->TMPL->tagdata);
		
		foreach($tmpl_vars['var_pair'] as $var => $params)
		{
			$var = str_replace($prefix, '', $var);

			if(property_exists($this, $var))
			{
				$this->$var = $this->_get_tagdata($prefix.$var, $params);
			}
		}
	}

	private function _get_tagdata($var, $params)
	{
		preg_match("/(".LD.$var.RD.")(.*)(".LD."\/".$var.RD.")/us", $this->EE->TMPL->tagdata, $matches);

		$tag_open  = $matches[1];
		$tagdata   = $matches[2];
		$tag_close = $matches[3];

		return trim($tagdata);
	}
}
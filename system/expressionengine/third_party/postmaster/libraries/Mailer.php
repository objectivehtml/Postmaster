<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Mailer
 * * 
 * @package		Postmaster
 * @subpackage	Library
 * @author		Justin Kimbrell
 * @copyright	Copyright (c) 2012, Objective HTML
 * @link 		http://www.objectivehtml.com/postmaster
 * @version		1.0.0
 * @build		20120324
 */

class Mailer {

	public $parcel;

	public function __construct($parcel)
	{
		$this->EE =& get_instance();
		
		$this->EE->load->library('email');

		$this->EE->email->clear();

		$config = array();

		foreach($parcel->settings->ExpressionEngine as $setting => $value)
		{
			$config[$setting] = $value;
		}
		
		$this->EE->email->initialize($config);

		$this->EE->email->to($parcel->to_email, $parcel->to_name);
		$this->EE->email->from($parcel->from_email, $parcel->from_name);
		
		if(!empty($parcel->cc))
		{
			$this->EE->email->cc($parcel->cc);
		}
		
		if(!empty($parcel->bcc))
		{
			$this->EE->email->bcc($parcel->bcc);
		}

		$this->EE->email->subject($parcel->subject);
		$this->EE->email->message($parcel->message);

		$this->parcel = $parcel;
	}

	public function to($email, $name = '')
	{
		$this->EE->email->to($email, $name);
	}

	public function from($email, $name = '')
	{
		$this->EE->email->from($email, $name);
	}

	public function cc($email, $name = '')
	{
		$this->EE->email->cc($email, $name);
	}

	public function bcc($email, $name = '')
	{
		$this->EE->email->bcc($email, $name);
	}

	public function subject($subject)
	{
		$this->EE->email->subject($subject);
	}

	public function message($message)
	{
		$this->EE->email->message($message);
	}

	public function alt_message($message)
	{
		$this->EE->email->alt_message($message);
	}

	public function send()
	{
		return $this->EE->email->send();
	}

	public function clear()
	{
		return $this->EE->email->clear();
	}

	public function print_debugger()
	{
		return $this->EE->email->print_debugger();
	}
}
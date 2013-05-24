<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Email_send_postmaster_hook extends Base_hook {
	
	protected $title = 'Send System Email';
	
	public function __construct()
	{
		parent::__construct();
		
	}
		
	public function trigger($vars = array())
	{
		$recipients = implode(', ', $vars['recipients']);
		$from       = preg_replace('/(^<)|(>$)/', '', trim($vars['headers']['From']));
		$to			= preg_replace('/(^<)|(>$)/', '', trim(implode(',', $vars['recipients'])));
		
		$parse_vars = array(
			'headers' 		=> array(
				$vars['headers']
			),
			'header_str'    => $vars['header_str'],
			'header_string' => $vars['header_str'],
			'recipients'    => $recipients,
			'from_email'	=> $from,
			'from_name'	    => '',
			'to_email'      => $to,
			'to_name' 		=> '',
			'reply_to'		=> isset($vars['headers']['Reply-To']) ? $vars['headers']['Reply-To'] : NULL,
			'cc' 			=> implode(',', $vars['cc_array']),
			'bcc' 			=> implode(',', $vars['bcc_array']),
			'subject'       => $vars['subject'],
			'message'		=> $vars['finalbody'],
			'finalbody'		=> $vars['finalbody'],
		);
		
		$return = $this->send($parse_vars, TRUE);
		
		if($return->response->status == 'success')
		{
			$return->return_data = TRUE;
		}
		
		return $return;
	}
	
	public function post_parse($parsed_hook)
	{
		if($this->hook['service'] != 'ExpressionEngine')
		{
			$parsed_hook['message'] = preg_replace('/'.LD.'\/?unwrap'.RD.'/u', '', $parsed_hook['message']);	
		}
		
		return $parsed_hook;		
	}
}
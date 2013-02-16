<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Email_send_postmaster_hook extends Base_hook {
	
	protected $title = 'Send System Email';
	
	public function __construct()
	{
		parent::__construct();
		
	}
		
	public function trigger($vars = array())
	{
		$parse_vars = array(
			'headers' 		=> array(
				$vars['headers']
			),
			'header_str'    => $vars['header_str'],
			'header_string' => $vars['header_str'],
			'recipients'    => $vars['recipients'],
			'from_email'	=> $vars['headers']['From'],
			'from_name'	    => $vars['headers']['From'],
			'to_email'      => $vars['recipients'],
			'to_name' 		=> $vars['recipients'],
			'reply_to'		=> isset($vars['headers']['Reply-To']) ? $vars['headers']['Reply-To'] : NULL,
			'cc' 			=> implode(',', $vars['cc_array']),
			'bcc' 			=> implode(',', $vars['bcc_array']),
			'subject'       => $vars['subject'],
			'message'		=> $vars['finalbody'],
			'finalbody'		=> $vars['finalbody'],
		);
		
		$return = parent::send($parse_vars, TRUE);
		
		if($return->response->status == 'success')
		{
			$return->return_data = TRUE;
		}
		
		return $return;
	}
}
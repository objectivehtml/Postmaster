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
				$vars[0]['headers']
			),
			'header_str'    => $vars[0]['header_str'],
			'header_string' => $vars[0]['header_str'],
			'recipients'    => $vars[0]['recipients'],
			'from_email'	=> $vars[0]['headers']['From'],
			'from_name'	    => $vars[0]['headers']['From'],
			'to_email'      => $vars[0]['recipients'],
			'to_name' 		=> $vars[0]['recipients'],
			'reply_to'		=> isset($vars[0]['headers']['Reply-To']) ? $vars[0]['headers']['Reply-To'] : NULL,
			'cc' 			=> implode(',', $vars[0]['cc_array']),
			'bcc' 			=> implode(',', $vars[0]['bcc_array']),
			'subject'       => $vars[0]['subject'],
			'message'		=> $vars[0]['finalbody'],
			'finalbody'		=> $vars[0]['finalbody'],
		);
		
		return parent::trigger($parse_vars, TRUE);
	}
}
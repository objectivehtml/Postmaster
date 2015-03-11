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

		$from = $this->_extract_email($vars['headers']['From']);

		$to = array();

		foreach($vars['recipients'] as $recipient)
		{
			$recipient = $this->_extract_email($recipient);

			if(!empty($recipient['name']))
			{
				$to[] = '"'.$recipient['name'].'" <'.$recipient['email'].'>';
			}
			else
			{
				$to[] = $recipient['email'];
			}
		}

		$to = implode(',', $to);

		$parse_vars = array(
			'headers' 		=> array(
				$vars['headers']
			),
			'header_str'    => $vars['header_str'],
			'header_string' => $vars['header_str'],
			'recipients'    => $recipients,
			'from_email'	=> $from['email'],
			'from_name'	    => $from['name'],
			'to_email'      => $to,
			'to_name' 		=> '',
			'reply_to'		=> isset($vars['headers']['Reply-To']) ? $vars['headers']['Reply-To'] : NULL,
			'cc' 			=> implode(',', $vars['cc_array']),
			'bcc' 			=> implode(',', $vars['bcc_array']),
			'subject'       => isset($vars['headers']['Subject']) ? $vars['headers']['Subject'] : $vars['subject'],
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
			$parsed_hook['html_message'] = preg_replace('/'.LD.'\/?unwrap'.RD.'/u', '', $parsed_hook['html_message']);
			$parsed_hook['plain_message'] = preg_replace('/'.LD.'\/?unwrap'.RD.'/u', '', $parsed_hook['plain_message']);	
		}

		return $parsed_hook;		
	}

	private function _extract_email($str)
	{
		$result = preg_match_all("/(\".+\")|(<.+>)/us", $str, $matches);

		if(!isset($matches[0][0]))
		{
			$matches = array(
				array('', $str)
			);
		}
		else
		{
			$matches = !isset($matches[0][1]) ? array(array(
				'',
				$matches[0][0]
			)) : $matches;
		}

		return array(
			'name'  => preg_replace('/(^")|("$)/', '', $matches[0][0]),
			'email' => preg_replace('/(^<)|(>$)/', '', $matches[0][1])
		);
	}
}
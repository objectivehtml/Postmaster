<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Mailgun
 *
 * Allows you to push email using Mailgun's email service.
 *
 * @package		Postmaster
 * @subpackage	Services
 * @author		Justin Kimbrell
 * @copyright	Copyright (c) 2012, Objective HTML
 * @link 		http://www.objectivehtml.com/postmaster
 * @version		1.0.0
 * @build		20121026
 */

class Mailgun_postmaster_service extends Base_service {

	public $name = 'Mailgun';
	public $url  = 'https://api.mailgun.net/v2';

	public $default_settings = array(
		'username'        => '',
		'api_key'         => ''
	);
	
	public $fields = array(
		'domain' => array(
			'label' => 'Domain',
			'id'	=> 'mailgun_domain'
		),
		'api_key' => array(
			'label' => 'API Key',
			'id'	=> 'mailgun_api_key'
		),
		'plain_text_only' => array(
			'label' => 'Plain Text Only',
			'id'	=> 'plain_text_only',
			'description' => 'Whether or not to force the email to be only plain text',
			'type'  => 'radio',
			'settings' => array(
				'options' => array(
					'true'   => 'True',
					'false'  => 'False',
				)
			)
		)
	);

	public $description = '
	<p>Mailgun is a set of powerful APIs that allow you to send, receive, track and store email effortlessly.</p>

	<h4>Links</h4>

	<ul>
		<li><a href="http://mailgun.com/">Features</a></li>
		<li><a href="http://mailgun.com/pricing">Pricing</a></li>
		<li><a href="http://mailgun.net/support">Support</a></li>
		<li><a href="http://mailgun.net/sessions/neww">Login</a></li>
	</ul>
	';

	public function __construct()
	{
		parent::__construct();
	}

	public function send($parsed_object, $parcel)
	{
		$settings = $this->get_settings();

		$plain_message = strip_tags($parsed_object->message);
		$html_message  = $parsed_object->message;

		if(isset($parsed_object->html_message) && !empty($parsed_object->html_message))
		{
			$html_message = $parsed_object->html_message;
		}

		if(isset($parsed_object->plain_message) && !empty($parsed_object->plain_message))
		{
			$plain_message = $parsed_object->plain_message;
		}

		$post = array(
			'to'       => trim($parsed_object->to_name.' <'.$parsed_object->to_email.'>'),
			'from'     => trim($parsed_object->from_name.' <'.$parsed_object->from_email.'>'),
			'subject'  => $parsed_object->subject,
			'text'     => $plain_message,
			'html'     => $html_message
		);
		
		if(!empty($parsed_object->cc))
		{
			$post['cc'] = $parsed_object->cc;
		}
		
		if(!empty($parsed_object->bcc))
		{
			$post['bcc'] = $parsed_object->bcc;
		}
		
		if(isset($settings->plain_text_only) && $settings->plain_text_only == 'true')
		{
			$post['html'] = $plain_message;	
		}
		
		$this->curl->create($this->url.'/'.$settings->domain.'/messages');
		$this->curl->http_login('api', $settings->api_key, 'basic');
		$this->curl->option(CURLOPT_CUSTOMREQUEST, 'POST');
		$this->curl->option(CURLOPT_RETURNTRANSFER, TRUE);
		
		$this->curl->post($post);

		$response = $this->curl->execute();
		
		if(!$response)
		{
			$this->show_error('Error: '.$this->curl->error_string.'<p>Consult with Mailgun\'s documentation for more information regarding this error. <a href="http://documentation.mailgun.net/">http://documentation.mailgun.net/</a></p>');
		}
		else
		{
			$response = json_decode($response);
		}

		return new Postmaster_Service_Response(array(
			'status'     => $response->message == 'success' ? POSTMASTER_SUCCESS : POSTMASTER_FAILED,
			'parcel_id'  => $parcel->id,
			'channel_id' => isset($parcel->channel_id) ? $parcel->channel_id : FALSE,
			'author_id'  => isset($parcel->entry->author_id) ? $parcel->entry->author_id : FALSE,
			'entry_id'   => isset($parcel->entry->entry_id) ? $parcel->entry->entry_id : FALSE,
			'gmt_date'   => $this->now,
			'service'    => $parcel->service,
			'to_name'    => $parsed_object->to_name,
			'to_email'   => $parsed_object->to_email,
			'from_name'  => $parsed_object->from_name,
			'from_email' => $parsed_object->from_email,
			'cc'         => $parsed_object->cc,
			'bcc'        => $parsed_object->bcc,
			'subject'    => $parsed_object->subject,
			'message'    => $parsed_object->message,
			'parcel'     => $parcel
		));
	}

	public function display_settings($settings, $parcel)
	{	
		return $this->build_table($settings);
	}
}
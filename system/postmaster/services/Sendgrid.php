<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * SendGrid
 *
 * Allows you to push email using SendGrid's email service.
 *
 * @package		Postmaster
 * @subpackage	Services
 * @author		Justin Kimbrell
 * @copyright	Copyright (c) 2012, Objective HTML
 * @link 		http://www.objectivehtml.com/postmaster
 * @version		1.0.0
 * @build		20120412
 */

class SendGrid_postmaster_service extends Base_service {

	public $name = 'SendGrid';
	public $url  = 'http://sendgrid.com/api/mail.send.json';

	public $default_settings = array(
		'api_user'        => '',
		'api_key'         => '',
		'plain_text_only' => 'false'
	);

	public $fields = array(
		'api_user' => array(
			'label' => 'Username',
			'id'	=> 'sendgrid_api_user'
		),
		'api_key' => array(
			'label' => 'Password',
			'id'	=> 'sendgrid_api_key',
			'type'  => 'password'
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
	<p>SendGrid\'s cloud email infrastructure relieves businesses of the cost and complexity of maintaining custom email systems. SendGrid provides reliable delivery, scalability and real-time analytics along with flexible API\'s that make custom integration a breeze.</p>

	<h4>Links</h4>

	<ul>
		<li><a href="http://sendgrid.com/features.html">Features</a></li>
		<li><a href="http://sendgrid.com/pricing.html">Pricing</a></li>
		<li><a href="http://support.sendgrid.com/home">Support</a></li>
		<li><a href="http://sendgrid.com/account/overview">Login</a></li>
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
			'api_user' => $settings->api_user,
			'api_key'  => $settings->api_key,
			'to'       => $parsed_object->to_email,
			'cc'       => $parsed_object->cc,
			'bcc'      => $parsed_object->bcc,
			'toname'   => $parsed_object->to_name,
			'from'     => $parsed_object->from_email,
			'fromname' => $parsed_object->from_name,
			'subject'  => $parsed_object->subject,
			'text'     => $plain_message,
			'html'     => $html_message,
			'date'     => date('r', $this->now)
		);
		
		if(isset($settings->plain_text_only) && $settings->plain_text_only == 'true')
		{
			$post['html'] = $plain_message;	
		}
		
		$this->curl->create($this->url);		
		$this->curl->option(CURLOPT_HEADER, FALSE);
		$this->curl->option(CURLOPT_RETURNTRANSFER, TRUE);
		$this->curl->post($post);

		$response = $this->curl->execute();

		if(!$response)
		{
			$this->show_error('Error: '.$this->curl->error_string.'<p>Consult with SendGrid\'s documentation for more information regarding this error. <a href="http://docs.sendgrid.com/documentation/api/web-api/">http://docs.sendgrid.com/documentation/api/web-api/</a></p>');
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
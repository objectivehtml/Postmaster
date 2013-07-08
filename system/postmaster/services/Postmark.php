<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Postmark
 *
 * Allows you to push email using Postmark's email service
 *
 * @package		Postmaster
 * @subpackage	Services
 * @author		Justin Kimbrell
 * @copyright	Copyright (c) 2012, Objective HTML
 * @link 		http://www.objectivehtml.com/postmaster
 * @version		1.0.0
 * @build		20120412
 */

class Postmark_postmaster_service extends Base_service {

	public $name = 'Postmark';
	public $url  = 'http://api.postmarkapp.com/email';

	public $default_settings = array(
		'api_key' => 'POSTMARK_API_TEST'
	);
	
	public $fields = array(
		'api_key' => array(
			'label' => 'API Key',
			'id'	=> 'postmark_api_key'
		)
	);

	public $description = '
	<p>Postmark removes the headaches of delivering and parsing transactional email for webapps with minimal setup time and zero maintenance. We have years of experience getting email to the inbox, so you can work and rest easier. Use our Send API or our simple SMTP interface to start sending in minutes, & use our Inbound API to easily parse incoming emails.</p>

	<h4>Links</h4>
	<ul>
		<li><a href="http://postmarkapp.com/why-postmark">Features</a></li>
		<li><a href="http://postmarkapp.com/pricing">Pricing</a></li>
		<li><a href="http://developer.postmarkapp.com/">Support</a></li>
		<li><a href="https://postmarkapp.com/servers">Login</a></li>
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
			'From'     => $parsed_object->from_email,
			'ReplyTo'  => $parsed_object->from_email,
			'To'       => $parsed_object->to_email,
			'Cc'       => $parsed_object->cc,
			'Bcc'      => $parsed_object->bcc,
			'Subject'  => $parsed_object->subject,
			'HtmlBody' => $html_message,
			'TextBody' => $plain_message,
		);

		$this->curl->create($this->url);

		$this->curl->post(json_encode($post), array(
			'httpheader' => array(
				'Accept: application/json',
				'Content-Type: application/json',
				'X-Postmark-Server-Token: ' . $settings->api_key
			)
		));

		$response = $this->curl->execute();

		if($response)
		{
			$response = json_decode($response);

			if($response->ErrorCode != 0)
			{
				$error = 'Error Code: <b>'.$response->ErrorCode.'</b>
				<p>Consult with Postmark\'s documentation for more information regarding this error. <a href="http://developer.postmarkapp.com/developer-build.html#http-response-codes">http://developer.postmarkapp.com/developer-build.html#http-response-codes</a></p>';

				$this->show_error($error);
			}
		}
		else
		{
			$this->show_error('Error: '.$this->curl->error_string.'<p>Consult with Postmark\'s documentation for more information regarding this error. <a href="http://developer.postmarkapp.com/developer-build.html#http-response-codes">http://developer.postmarkapp.com/developer-build.html#http-response-codes</a></p>');
		}

		return new Postmaster_Service_Response(array(
			'status'     => $response->ErrorCode == 0 ? POSTMASTER_SUCCESS : POSTMASTER_FAILED,
			'parcel_id'  => $parcel->id,
			'channel_id' => isset($parcel->channel_id) ? $parcel->channel_id : FALSE,
			'author_id'  => isset($parcel->entry->author_id) ? $parcel->entry->author_id : FALSE,
			'entry_id'   => isset($parcel->entry->entry_id) ? $parcel->entry->entry_id : FALSE,
			'gmt_date'   => strtotime($response->SubmittedAt),
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
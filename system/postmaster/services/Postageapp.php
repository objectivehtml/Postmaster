<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * PostageApp
 *
 * Allows you to push email using the PostageApp service
 *
 * @package		Postmaster
 * @subpackage	Services
 * @author		Justin Kimbrell
 * @copyright	Copyright (c) 2012, Objective HTML
 * @link 		http://www.objectivehtml.com/postmaster
 * @version		1.0.0
 * @build		20120412
 */
 

class Postageapp_postmaster_service extends Base_service {

	public $name = 'PostageApp';
	public $url  = 'https://api.postageapp.com/v.1.0/send_message.json';
	
	public $default_settings = array(
		'api_key' => ''
	);

	public $fields = array(
		'api_key' => array(
			'label' => 'API Key',
			'id'	=> 'postageapp_api_key'
		)
	);

	public $description = '
	<p>PostageApp handles all of the grunt work when it comes to sending your emails - just give us content, specify a recipient, and we’ll do the rest. We have our own delivery engine, fine tuned and whitelisted for mass email, so that you won\'t have to worry about getting your email into inboxes ever again. We take care of your emails, so you can focus on building an incredible app.</p>

	<h4>Links</h4>
	<ul>
		<li><a href="http://postageapp.com/benefits-features">Features</a></li>
		<li><a href="https://secure.postageapp.com/register">Pricing</a></li>
		<li><a href="http://help.postageapp.com/kb">Support</a></li>
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
			'api_key' => $settings->api_key,
			'uid' => $this->uid->v4(),
			'arguments' => array(
				'recipients' => explode(',', $parsed_object->to_email),
				'headers' => array (
					'subject' => $parsed_object->subject,
					'from' => $parsed_object->from_email
				),
				'content' => array(
					'text/plain' => $plain_message,
					'text/html'  => $html_message
				)
			)
		);

		$this->curl->create($this->url);
		$this->curl->option(CURLOPT_HEADER, FALSE);
		$this->curl->option(CURLOPT_RETURNTRANSFER, TRUE);
		$this->curl->post(json_encode($post), array(
			'httpheader' => array(
				'Content-Type: application/json'
			)
		));

		$response = $this->curl->execute();

		if(!$response)
		{
			$this->show_error('Error: '.$this->curl->error_string.'<p>Consult with Postmark\'s documentation for more information regarding this error. <a href="http://developer.postmarkapp.com/developer-build.html#http-response-codes">http://developer.postmarkapp.com/developer-build.html#http-response-codes</a></p>');
		}
		else
		{
			$response = json_decode($response);
		}

		return new Postmaster_Service_Response(array(
			'status'     => $response->response->status == 'ok' ? POSTMASTER_SUCCESS : POSTMASTER_FAILED,
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
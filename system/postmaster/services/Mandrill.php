<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Mandrill
 *
 * Allows you to push email using Madrill's email service.
 *
 * @package		Postmaster
 * @subpackage	Services
 * @author		Justin Kimbrell
 * @copyright	Copyright (c) 2012, Objective HTML
 * @link 		http://www.objectivehtml.com/postmaster
 * @version		1.0.0
 * @build		20120902
 */

class Mandrill_postmaster_service extends Base_service {

	public $name = 'Mandrill';
	public $url  = 'https://mandrillapp.com/api/1.0/messages/send.json';

	public $default_settings = array(
		'api_key'             => '',
		'track_opens'         => 'true',
		'track_clicks'        => 'true',
		'auto_text'           => 'false',
		'url_strip_qs'        => 'false',
		'preserve_recipients' => 'true',
		'plain_text_only' 	  => 'false',
	);
	
	public $fields = array(
		'api_key' => array(
			'label' => 'API Key',
			'id'	=> 'mandrill_api_key'
		),
		'track_opens' => array(
			'label' => 'Track Opens',
			'id'	=> 'mandrill_track_opens',
			'description' => 'Whether or not to turn on open tracking for the message',
			'type'  => 'radio',
			'settings' => array(
				'options' => array(
					'true'  => 'True',
					'false' => 'False',
				)
			)
		),
		'track_clicks' => array(
			'label' => 'Track Clicks',
			'id'	=> 'mandrill_track_clicks',
			'description' => 'Whether or not to turn on click tracking for the message',
			'type'  => 'radio',
			'settings' => array(
				'options' => array(
					'true'  => 'True',
					'false' => 'False',
				)
			)
		),
		'auto_text' => array(
			'label' => 'Auto Text',
			'id'	=> 'mandrill_auto_text',
			'description' => 'Whether or not to automatically generate a text part for messages that are not given text',
			'type'  => 'radio',
			'settings' => array(
				'options' => array(
					'true'  => 'True',
					'false' => 'False',
				)
			)
		),
		'url_strip_qs' => array(
			'label' => 'URL Strip Qs',
			'description' => 'Whether or not to strip the query string from URLs when aggregating tracked URL data',
			'id'	=> 'mandrill_url_strip_qs',
			'type'  => 'radio',
			'settings' => array(
				'options' => array(
					'true'  => 'True',
					'false' => 'False',
				)
			)
		),
		'preserve_recipients' => array(
			'label' => 'Preserve Recipients',
			'id'	=> 'mandrill_preserve_recipients',
			'description' => 'Whether or not to expose all recipients in to "To" header for each email',
			'type'  => 'radio',
			'settings' => array(
				'options' => array(
					'true'  => 'True',
					'false' => 'False',
				)
			)
		),
		'plain_text_only' => array(
			'label'       => 'Plain Text Only',
			'id'          => 'plain_text_only',
			'description' => 'Whether or not to force the email to be only plain text',
			'type'        => 'radio',
			'settings'    => array(
				'options' => array(
					'true'   => 'True',
					'false'  => 'False',
				)
			)
		)
	);

	public $description = '';

	public function __construct()
	{
		parent::__construct();		
	}
	
	public function send($parsed_object, $parcel)
	{
		$settings = $this->get_settings();

		$to      = array();
		$headers = array();
		
		if(isset($parsed_object->reply_to) && !empty($parsed_object->reply_to))
		{
			$headers['Reply-To'] = $parsed_object->reply_to;
		}
		
		$to_names = explode(',', $parsed_object->to_name);
		
		foreach(explode(',', $parsed_object->to_email) as $index => $to_email)
		{
			$name = null;

			if(isset($to_names[$index]))
			{
				$name = $to_names[$index];
			}

			$to[] = (object) array(
				'email' => $to_email,
				'name'  => $name,
				'type'  => 'to'
			);
		}

		if(!empty($parsed_object->cc))
		{
			foreach(explode(',', $parsed_object->cc) as $index => $to_email)
			{
				$to[] = (object) array(
					'email' => $to_email,
					'type' => 'cc'
				);
			}
		}

		if(!empty($parsed_object->bcc))
		{
			foreach(explode(',', $parsed_object->bcc) as $index => $to_email)
			{
				$to[] = (object) array(
					'email' => $to_email,
					'type' => 'bcc'
				);
			}
		}

		$plain_message = $this->plain_text($parsed_object->message);

		$html_message  = $parsed_object->message;

		if(isset($parsed_object->plain_message) && !empty($parsed_object->plain_message))
		{
			$plain_message = $this->plain_text($parsed_object->plain_message);
		}

		if(isset($parsed_object->html_message) && !empty($parsed_object->html_message))
		{
			$html_message = $parsed_object->html_message;
		}

		$post = array(
			'key'	   => $settings->api_key,
			'message'  => array(
				'text'    => $plain_message,
				'subject' => $parsed_object->subject,
				'to'      => $to,
				'from_email'          => $parsed_object->from_email,
				'from_name'           => $parsed_object->from_name,
				'headers'             => (object) $headers,
				'track_opens'         => $settings->track_opens == 'true' ? TRUE : FALSE,
				'track_clicks'        => $settings->track_clicks == 'true' ? TRUE : FALSE,
				'auto_text'           => $settings->auto_text == 'true' ? TRUE : FALSE,
				'url_strip_qs'        => $settings->url_strip_qs == 'true' ? TRUE : FALSE,
				'preserve_recipients' => $settings->preserve_recipients == 'true' ? TRUE : FALSE,
			),
		);

		if(isset($settings->plain_text_only) && $settings->plain_text_only != 'true')
		{	
			$post['message']['html'] = $html_message;
		}
				
		$post['message'] = (object) $post['message'];


		if(!empty($parsed_object->bcc))
		{
			$post['bcc_address'] = $parsed_object->bcc;
		}

		$this->curl->ssl(FALSE);

		$response = $this->curl->simple_post($this->url, $post);
		
		if(!$response)
		{
			$this->show_error('Error: '.$this->curl->error_string);
		}
		else
		{
			$response = json_decode($response);
		}
		
		return new Postmaster_Service_Response(array(
			'status'     => $response[0]->status == 'sent' ? POSTMASTER_SUCCESS : POSTMASTER_FAILED,
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
			'html_message' => $parsed_object->html_message,
			'plain_message' => $parsed_object->plain_message,
			'parcel'     => $parcel
		));
	}
}
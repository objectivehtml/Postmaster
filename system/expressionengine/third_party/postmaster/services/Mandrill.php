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

require_once PATH_THIRD.'postmaster/libraries/Postmaster_service.php';

class Mandrill_postmaster_service extends Postmaster_service {

	public $name = 'Mandrill';
	public $url  = 'https://mandrillapp.com/api/1.0/messages/send.json';

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
		)
	);

	public $description = '';

	public function __construct()
	{
		parent::__construct();		
	}

	public function send($parsed_object, $parcel)
	{
		$settings = $this->get_settings($parcel->settings);

		$to      = array();
		$headers = array();
		
		if(isset($parsed_object->reply_to) && !empty($parsed_object->reply_to))
		{
			$headers['Reply-To'] = $parsed_object->reply_to;
		}
		
		$to_names = explode('|', $parsed_object->to_name);
		
		foreach(explode('|', $parsed_object->to_email) as $index => $to_email)
		{
			if(isset($to_names[$index]))
			{
				$to[] = (object) array(
					'email' => $to_email,
					'name'  => $to_names[$index]
				);
			}	
		}
		
		$post = array(
			'key'	   => '04137e85-fcf8-4b6a-8a85-a04d4ef97744',
			'message'  => (object) array(
				'html'    => $parsed_object->message,
				'text'    => strip_tags($parsed_object->message),
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
		
		if(!empty($parsed_object->bcc))
		{
			$post['bcc_address'] = $parsed_object->bcc;
		}
		
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
			'parcel'     => $parcel
		));
	}
	
	private function post($data)
	{		
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->url);
        
        curl_setopt($ch, CURLOPT_POST, TRUE);
        
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2 * 60 * 1000);
        
        $response   = curl_exec($ch);
        $info       = curl_getinfo($ch);
        $error      = curl_error($ch);
           
        var_dump($response);exit();
        
        curl_close($ch);
	}
	
	public function default_settings()
	{
		return (object) array(
			'api_key'             => '',
			'track_opens'         => 'true',
			'track_clicks'        => 'true',
			'auto_text'           => 'false',
			'url_strip_qs'        => 'false',
			'preserve_recipients' => 'true',
		);
	}

}
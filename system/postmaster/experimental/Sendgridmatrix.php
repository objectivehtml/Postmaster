<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * SendGrid Conditional
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
 
class SendGridMatrix_postmaster_service extends Base_service {

	public $name = 'SendGridMatrix';
	public $url  = 'http://sendgrid.com/api/mail.send.json';

	public $fields = array(
		
		'api_user' => array(
			'name'  => 'api_user',
			'label' => 'Username',
			'id'	=> 'sendgridcond_api_user',
			'type'	=> 'input'
		),
		'api_key' => array(
			'name'  => 'api_key',
			'label' => 'Password',
			'id'	=> 'sendgridcond_api_key',
			'type'  => 'input',
			'settings' => array(
				'type' => 'password'
			)
		),
		'field_map' => array(
			'settings' => array(
				'attributes' => array(),
				'columns' => array(
					array(
						'name' => 'field_name',
						'title' => 'Field Name'
					),
					array(
						'name' => 'value',
						'title' => 'Value'
					),
					array(
						'name' => 'email',
						'title' => 'Email Address'
					)
				)
			),
			'label'    => 'Field Map',
			'type'     => 'matrix',
		),
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
		
		$this->fields['field_map']['settings']['attributes'] = postmaster_table_attr();
		$this->IB->set_meta(array('service' => $this->name));
	}

	public function send($parsed_object, $parcel)
	{
		$settings = $this->get_settings($parcel->settings);

		if(isset($settings->field_map) && is_array($settings->field_map))
		{
			foreach($settings->field_map as $index => $row)
			{
				$field  = $row->field_name;
				$values = explode('|', $row->value);
				$email  = $row->email;
				 
				if(isset($parcel->entry->$field) && in_array($parcel->entry->$field, $values) || isset($parcel->entry->$field) && isset($parcel->entry->$field) == '*')
				{
					$post = array(
						'api_user' => $settings->api_user,
						'api_key'  => $settings->api_key,
						'to'       => $email,
						'from'     => $parsed_object->from_email,
						'fromname' => $parsed_object->from_name,
						'subject'  => $parsed_object->subject,
						'text'     => strip_tags($parsed_object->message),
						'html'     => $parsed_object->message,
						'date'     => date('r', $this->now)
					);
					
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
				}
			}	
		}
		
		return new Postmaster_Service_Response(array(
			'status'     => isset($response->message) && $response->message == 'success' ? POSTMASTER_SUCCESS : POSTMASTER_FAILED,
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
		$settings = $this->get_settings($settings);
		
		return $this->IB->table($this->fields, $settings, postmaster_table_attr());
		
		//return $this->build_table($settings, $this->fields);
	}

	public function default_settings()
	{
		return (object) array(
			'api_user'  => '',
			'api_key'   => '',
			'field_map' => array()
		);
	}

}
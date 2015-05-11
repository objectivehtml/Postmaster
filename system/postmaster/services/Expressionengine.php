<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine
 *
 * Allows you to push email using standard PHP.
 *
 * @package		Postmaster
 * @subpackage	Services
 * @author		Justin Kimbrell
 * @copyright	Copyright (c) 2012, Objective HTML
 * @link 		http://www.objectivehtml.com/postmaster
 * @version		1.0.1
 * @build		20120831
 */

require_once PATH_THIRD . 'postmaster/libraries/Mailer.php';

class ExpressionEngine_postmaster_service extends Base_service {

	public $name = 'ExpressionEngine';

	public $default_settings = array(
		'useragent' 		=> 'CodeIgniter',
		'protocol'			=> 'mail',
		'mailpath'			=> '/usr/bin/sendmail',
		'smtp_host' 		=> '',
		'smtp_user' 		=> '',
		'smtp_pass'			=> '',
		'smtp_port' 		=> '',
		'smtp_timeout' 		=> '5',
		'smtp_wordwrap' 	=> '',
		'wordwrap'			=> TRUE,
		'wrapchars'			=> 76,
		'mailtype'			=> 'html',
		'charset'			=> 'utf-8',
		'validate'			=> FALSE,
		'priority' 			=> 3,
		'crlf'				=> "\n",
		'newline'			=> "\n",
		'bcc_batch_mode'	=> FALSE,
		'bcc_batch_size'	=> 200
	);
	
	public $fields = array(
		'mailtype' => array(
			'type'  => 'select',
			'id'	=> 'expressionengine_mailtype',
			'label' => 'Mail Type',
			'settings' => array(
				'options' => array(
					'text' => 'Text',
					'html' => 'HTML'
				)
			)
		),
		'protocol' => array(
			'type'  => 'select',
			'id'	=> 'expressionengine_protocol',
			'label' => 'Protocol',
			'settings' => array(
				'options' => array(
					'mail'     => 'Mail',
					'sendmail' => 'Sendmail',
					'smtp'     => 'SMTP'
				)		
			)
		),
		'useragent' => array(
			'id'	=> 'expressionengine_useragent',
			'label' => 'User Agent'			
		),
		'mailpath' => array(
			'id'	=> 'expressionengine_mailpath',
			'label' => 'Mail Path'			
		),
		'smtp_host' => array(
			'id'	=> 'expressionengine_smtp_host',
			'label' => 'SMTP Host'			
		),
		'smtp_user' => array(
			'id'	=> 'expressionengine_smtp_user',
			'label' => 'SMTP User'			
		),
		'smtp_pass' => array(
			'type'  => 'password',
			'id'	=> 'expressionengine_smtp_pass',
			'label' => 'SMTP Password'			
		),
		'smtp_port' => array(
			'id'	=> 'expressionengine_smtp_port',
			'label' => 'SMTP Port'			
		),
		'smtp_timeout' => array(
			'id'	=> 'expressionengine_smtp_timeout',
			'label' => 'SMTP Timeout'			
		),
		'charset' => array(
			'id'	=> 'expressionengine_charset',
			'label' => 'Character Set'			
		),
		'wordwrap' => array(
			'type'  => 'select',
			'id'	=> 'expressionengine_wordwrap',
			'label' => 'Wordwrap',
			'settings' => array(
				'options' => array(
					TRUE  => 'True',
					FALSE => 'False'
				)
			)
		),
		'wrapchars' => array(
			'id'	=> 'expressionengine_wrapchars',
			'label' => 'Wrapchars'			
		),
		'validate' => array(
			'type'  => 'select',
			'id'	=> 'expressionengine_validate',
			'label' => 'Validate',
			'settings' => array(
				'options' => array(
					TRUE  => 'True',
					FALSE => 'False'
				)
			)
		),
		'priority' => array(
			'type'  => 'select',
			'id'	=> 'expressionengine_priority',
			'label' => 'Priority',
			'settings' => array(
				'options' => array(
					1 => 1,
					2 => 2,
					3 => 3,
					4 => 4,
					5 => 5
				)
			)
		),
		'crlf' => array(
			'type'  => 'select',
			'id'	=> 'expressionengine_crlf',
			'label' => 'CRLF',
			'settings' => array(
				'options' => array(
					'\r\n' => '\r\n',
					'\r' => '\r',
					'\n' => '\n',
				)
			)
		),
		'newline' => array(
			'type'  => 'select',
			'id'	=> 'expressionengine_newline',
			'label' => 'Newline',
			'settings' => array(
				'options' => array(
					'\r\n' => '\r\n',
					'\r' => '\r',
					'\n' => '\n',
				)
			)
		),
		'bcc_batch_mode' => array(
			'type'  => 'select',
			'id'	=> 'expressionengine_bcc_batch_mode',
			'label' => 'BCC Batch Mode',
			'settings' => array(
				'options' => array(
					TRUE  => 'True',
					FALSE => 'False'
				)
			)
		),
		'bcc_batch_size' => array(
			'id'	=> 'expressionengine_bcc_batch_size',
			'label' => 'BCC Batch Size'			
		)
	);

	public $description = '
	<p>This is the default service which uses your PHP server to send the email. A lot of time, this is all you will need. If you are looking for a reliable way to send to email without worrying about spam, then you should consider use a third-party service. Use this service if you want to use a SMTP Relay.</p>';

	public function __construct()
	{
		parent::__construct();
	}

	public function send($parsed_object, $parcel, $debug = FALSE)
	{
		$parsed_object->settings = $parcel->settings;

		$mailer   = new Mailer($parsed_object);
		$response = $mailer->send();
		
		if(!$response)
		{
			$this->show_error('An unknown error has occurred when sending email with your server.');
		}

		return new Postmaster_Service_Response(array(
			'status'     => $response ? POSTMASTER_SUCCESS : POSTMASTER_FAILED,
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
			'message'    => !empty($parsed_object->message) ? $parsed_object->message : $parsed_object->html_message,
			'html_message'  => $parsed_object->html_message,
			'plain_message' => $parsed_object->plain_message,
			'parcel'        => $parcel
		));
	}

	public function display_settings($settings, $parcel)
	{	
		return $this->build_table($settings);
	}
}
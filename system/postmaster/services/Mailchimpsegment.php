<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MailChimp
 *
 * Allows you to email create/send campaigns using MailChimp
 *
 * @package		Postmaster
 * @subpackage	Services
 * @author		Justin Kimbrell
 * @copyright	Copyright (c) 2012, Objective HTML
 * @link 		http://www.objectivehtml.com/postmaster
 * @version		1.1.0
 * @build		20120610
 */

if(!class_exists('Mailchimp_postmaster_service'))
{
	require_once PATH_THIRD . 'postmaster/services/Mailchimp.php';
}

if(!class_exists('Newsletter_Subscription_Response'))
{
	require_once PATH_THIRD . 'postmaster/delegates/Campaign.php';
}

class MailchimpSegment_postmaster_service extends Mailchimp_postmaster_service {

	public $title = 'Mailchimp Segment';
	
	public $name = 'MailchimpSegment';

	public $url  = '';

	public function __construct()
	{
		$this->EE =& get_instance();

		$tasks = array();

		foreach($this->EE->postmaster_model->get_tasks()->result() as $task)
		{
			$tasks[$task->id] = $task->title;
		}

		$this->fields = array_merge($this->fields, array(	
			'task_id' => array(
				'label' => 'Mailchimp Member Subscription Task',
				'description' => 'You must have a Mailchimp Member Subscription Task setup for this email service to work properly. Select the correct Task from this list.',
				'id'	=> 'task_id',
				'type' => 'select',
				'settings' => array(
					'options' => $tasks
				)
			),
			'segment_value' => array(
				'label' => 'Mailchimp Segment',
				'description' => 'This should be a channel or member field. If a channel field, prefix the value with <em>entry:</em>. So if the name of your segment was stored in the entry\'s title, the value would be <em>entry:title</em>. And for member fields, it should be prefixed with <em>member:</em>. The member represents the entry\'s author, not the logged in member.',
				'id'	=> 'segment_value'
			)
		));

		parent::__construct();
	}

	public function send($parsed_object, $parcel)
	{
		$this->member = $this->EE->channel_data->get_member($parcel->entry->author_id)->row();

		$this->parse_vars = array_merge(
			(array) $this->EE->channel_data->utility->add_prefix('entry', $parcel->entry),
			(array) $this->EE->channel_data->utility->add_prefix('member', $this->member)
		);

		return parent::send($parsed_object, $parcel);
	}

	public function get_campaign_params($list_id, $parsed_object, $parcel)
	{
		$settings = $this->get_settings();

		$params = parent::get_campaign_params($list_id, $parsed_object, $parcel);
		
		if(isset($settings->task_id) && !empty($settings->task_id))
		{
			$task_settings = json_decode($this->EE->postmaster_model->get_task($settings->task_id)->row('settings'));

			if( isset($task_settings->mailchimp_member_subscriptions) &&
				isset($this->parse_vars[$settings->segment_value]))
			{
				$task_settings = $task_settings->mailchimp_member_subscriptions;

				$params['segment_opts'] = array(
					'match' => 'all',
					'conditions' => array(
						array(
							'field' => 'interests-'.$task_settings->group_id,
							'op' => 'one',
							'value' => $this->parse_vars[$settings->segment_value]
						)
					)
				);
			}
		}

		return $params;
	}
}
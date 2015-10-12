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

if(!class_exists('Newsletter_Subscription_Response'))
{
	require_once PATH_THIRD . 'postmaster/delegates/Campaign.php';
}

class Mailchimp_v2_postmaster_service extends Base_service {

	public $name = 'Mailchimp_v2';
	public $url  = '';

	public $default_settings = array(
		'api_key' => ''
	);
	
	public $fields = array(
		'api_key' => array(
			'label' => 'API Key',
			'id'	=> 'mailchimp_api_key'
		)
	);

	public $description = '
	<p>Easy Email Newsletters
MailChimp helps you design email newsletters, share them on social networks, integrate with services you already use, and track your results. It\'s like your own personal publishing platform.</p>

	<h4>Links</h4>

	<ul>
		<li><a href="http://mailchimp.com/features/">Feature</a></li>
		<li><a href="http://mailchimp.com/pricing/">Pricing</a></li>
		<li><a href="http://kb.mailchimp.com/">Support</a></li>
		<li><a href="https://login.mailchimp.com/">Login</a></li>
	</ul>';

	public function __construct()
	{
		parent::__construct();
	}

	public function is_subscribed($params = array())
	{
		$url = $this->api_url($params['api_key'], 'lists/member-info', array(
			'id' => $params['id'],
			'emails' => array(
				array('email' => $params['email'])
			)
		));

		$subscribers = $this->_get($url);

		if(isset($params['group_id']) && $subscribers->success_count)
		{
			$valid_group = false;
			$group_ids = explode('|', $params['group_id']);

			foreach($subscribers->data as $data)
			{
				if(isset($data->merges->GROUPINGS) )
				{
					foreach($data->merges->GROUPINGS as $group)
					{
						$valid_email = false;

						$available_groups = $group->groups;
						$groups = explode('|', $params['groups']);

						foreach($available_groups as $group)
						{
							if(in_array($group->name, $groups))
							{
								return true;
							}
						}
					}
				}
			}
		}

		return $subscribers->success_count >= 1;
	}
	
	public function get_subscribers($params = array())
	{
		return $this->subscribers($params);
	}

	public function subscribers($params = array())
	{
		if(!isset($params['prefix']))
		{
			$params['prefix'] = false;
		}

		$url = $this->api_url($params['api_key'], 'lists/members', array(
			'id'	 => $params['id'],
			'apikey' => $params['api_key']
		));

		$subscribers = $this->_get($url);

		$return = array();

		if(is_object($subscribers) && is_array($subscribers->data))
		{
			foreach($subscribers->data as $index => $subscriber)
			{
				$subscriber->timestamp = strtotime($subscriber->timestamp);
				
				$row[$params['prefix'] ? $params['prefix'].':index' : 'index'] = $index;
				$row[$params['prefix'] ? $params['prefix'].':count' : 'count'] = $index+1;
				$row[$params['prefix'] ? $params['prefix'].':total' : 'total'] = $subscribers->total;
				$row[$params['prefix'] ? $params['prefix'].':email' : 'email'] = $subscriber->email;			
				$row[$params['prefix'] ? $params['prefix'].':data' : 'data']  =  $this->EE->channel_data->utility->add_prefix($params['prefix'], array((array) $subscriber));
				
				$return[$index] = $row;	
			}
		}
		
		return $return;
	}

	public function send($parsed_object, $parcel)
	{
		$response = FALSE;
		$settings = $this->get_settings();


		if(isset($settings->list_id))
		{
			foreach($settings->list_id as $list_id)
			{
				$response = $this->create_campaign($list_id, $parsed_object, $parcel);	
				
				if(empty($response))
				{
					show_error('Something has gone wrong. Your email campaign has not been created.');
				}

				if(isset($response->error) && isset($response->code))
				{
					show_error('Error Code: '.$response->code.' - "'.$response->error.'"');
				}

				$response = $this->send_campaign($settings->api_key, $response->id);
			}
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
			'message'    => $parsed_object->message,
			'parcel'     => $parcel
		));
	}

	public function update_member($params)
	{
		$url = $this->api_url($params['api_key'], 'listUpdateMember', array(
			'id'	 => $params['id'],
			'apikey' => $params['api_key']
		));
		
		$response = $this->post($url, array(
			'email_address' => $params['email'],
			'email_type' => $params['email_type'],
			'merge_vars' => $params['merge_vars']
		));

		return $response;
	}
	
	public function display_settings($settings, $parcel)
	{
		$html = $this->build_table($settings);

		$html .= '
		<h3>Mailing Lists</h3>
		<p>Select all of the following lists in which you want to use to send your campaign. <a href="#" class="mailchimp-refresh">Refresh the List</a></p>';

		$html .= $this->display_mailing_lists($settings, $parcel);

		return $html;
	}

	public function display_mailing_lists($settings, $parcel)
	{
		$settings = $this->get_settings($settings);

		$url = $this->call_url('get_lists');

		$html = "
		<script type=\"text/javascript\">
			$(document).ready(function() {

				var url = '$url';
				
				$('.service-panel').each(function() {
					var t = $(this);

					if(t.css('display') != 'none') {
						t.find('#mailchimp_api_key').blur(function() {
							t.find('.mailchimp-refresh').click();
						});

						t.find('.mailchimp-refresh').click(function(e) {

							var apiKey = t.find('#mailchimp_api_key').val();

							$.get(url+'&api_key='+apiKey+'&ajax=1', function(data) {
								t.find('#mailchimp-lists tbody').html(data);
							});

							e.preventDefault();
						});
					}
				});
			});
		</script>

		<table class=\"mainTable\" id=\"mailchimp-lists\" cellspacing=\"0\" cellpadding=\"0\">
			<thead>
				<tr>
					<th></th>
					<th>Name</th>
					<th>Date Created</th>
					<th>Subscriber Count</th>
					<th>New Subscriber</th>
					<th>Campaign Count</th>
				</tr>
			</thead>
			<tbody>";

			$lists = $this->get_lists($settings->api_key);

			$html .= $this->list_rows($lists, $settings);

		$html .= '
			</tbody>
		</table>';

		return $html;
	}

	public function list_rows($lists, $settings)
	{
		$html = NULL;
		
		if($lists->total > 0)
		{
			foreach($lists->data as $list)
			{
				$checked = in_array($list->id, isset($settings->list_id) ? $settings->list_id : array()) ? 'checked="checked"' : NULL;

				$html .= '
				<tr>
					<td><input type="checkbox" name="setting['.$this->name.'][list_id][]" value="'.$list->id.'" '.$checked.' /></td>
					<td>'.$list->name.'</td>
					<td>'.date('F j Y', strtotime($list->date_created)).'</td>
					<td>'.$list->stats->member_count.'</td>
					<td>'.$list->stats->member_count_since_send.'</td>
					<td>'.$list->stats->campaign_count.'</td>
				</tr>';
			}
		}
		else
		{
			$empty_message = 'You have no lists associated with your MailChimp account. If you are sure you have created lists, make sure your API key is correct and <a href="#" class="mailchimp-refresh">Refresh the List</a>.';

			$api_message = 'You have not entered a MailChimp API. Be sure to enter your API key and <a href="#" class="mailchimp-refresh">Refresh the List</a>.';

			$message = !empty($settings->api_key) ? $empty_message : $api_message;

			$html .= '
			<tr>
				<td></td>
				<td colspan="6"><p>'.$message.'</p></td>
			</tr>';
		}		

		return $html;
	}

	public function api_url($api_key, $method, $params = array())
	{
		$params['apikey'] = $api_key;

		$url = 'https://<dc>.api.mailchimp.com/2.0/'.$method.'?'.http_build_query($params);
		
		return str_replace('<dc>', substr($api_key, strpos($api_key, '-')+1), $url);
	}

	public function get_members_lists($key, $email)
	{
		$url = $this->api_url($key, 'helper/lists-for-email');

		$response = $this->_get($url, array(
			'apikey' => $key,
			'email[email]' => $email
		));

		return $response;
	}

	public function get_member_info($key, $list_id, $email)
	{
		$url = $this->api_url($key, 'lists/member-info');

		$response = $this->post($url, array(
			'id' => $list_id,
			'emails' => array(
				$this->_getStruct($email)
			)
		));

		if(!$response->success_count)
		{
			return;
		}

		return $response->data[0];
	}

	public function subscribe($data)
	{
		$default_settings = array(
			'email_type' => 'html',
			'post' => array()
		);

		$data = array_merge($default_settings, $data);

		$url = $this->api_url($data['api_key'], 'lists/subscribe');

		$params = array(
			'apikey' => $data['api_key'],
			'id' => $data['id'],
			'email' => (object) $this->_getStruct($data['email']),
			'email_type' => $data['email_type'],
			'double_optin'      => filter_var($this->param($data['post'], 'double_optin', TRUE), FILTER_VALIDATE_BOOLEAN),
			'update_existing'   => filter_var($this->param($data['post'], 'update_existing', FALSE), FILTER_VALIDATE_BOOLEAN),
			'replace_interests' => filter_var($this->param($data['post'], 'replace_interests', TRUE), FILTER_VALIDATE_BOOLEAN),
			'send_welcome'      => filter_var($this->param($data['post'], 'send_welcome', FALSE), FILTER_VALIDATE_BOOLEAN),	
		);

		if(isset($data['first_name']) && !empty($data['first_name']))
		{
			$params['fname'] = $data['first_name'];
		}
		
		if(isset($data['last_name']) && !empty($data['last_name']))
		{
			$params['lname'] = $data['last_name'];
		}

		/*
		$groupings = array();

		if($info = $this->get_member_info($data['api_key'], $data['id'], $data['email']))
		{
			if(isset($info->merges->GROUPINGS))
			{
				$groupings = $info->merges->GROUPINGS;
			}
		}

		if(isset($data['post']['group_id']) && isset($data['post']['groups']))
		{
			$i = count($groupings);

			$groupings[$i] = array('groups' => $data['post']['groups']);

			if(isset($data['post']['group_id']))
			{
				$groupings[$i]['id'] = $data['post']['group_id'];
			}

			if(isset($data['post']['group_name']))
			{
				$groupings[$i]['name'] = $data['post']['group_name'];
			}
		}
		*/
		
		$unset = array(
			'double_optin',
			'update_existing',
			'replace_interests',
			'send_welcome', 
			'service', 
			'api_key', 
			'list',
			'group_id',
			'groups'
		);

		foreach($unset as $var)
		{
			unset($data['post'][$var]);
		}
		
		$params['merge_vars'] = $data['post'];

		if(isset($params['fname']))
		{
			$params['merge_vars']['fname'] = $params['fname'];
		}

		if(isset($params['lname']))
		{
			$params['merge_vars']['lname'] = $params['lname'];
		}

		$response = $this->post($url, $params);
		
		$return = new Newsletter_Subscription_Response(array(
			'success' => $response !== NULL ? TRUE : FALSE,
			'data'    => $response,
			'errors'  => $response !== NULL ? array() : array(array('error' => $this->curl->error_string, 'code' => $this->curl->error_code))
		));

		return $return;
	}
		
	public function param($data, $name, $default = FALSE)
	{
		return isset($data[$name]) ? $data[$name] : $default;
	}
	
	public function unsubscribe($data)
	{
		$params = array(
			'apikey' => $data['api_key'],
			'id' => $data['id'],
			'email' => (object) $this->_getStruct($data['email']),
			'delete_member' => $this->param($data['post'], 'delete_member', FALSE),
			'send_goodbye' => $this->param($data['post'], 'send_goodby', TRUE),
			'send_notify' => $this->param($data['post'], 'send_notify', TRUE)
		);

		$url = $this->api_url($data['api_key'], 'lists/unsubscribe');
		
		$response = $this->post($url, $params);
		
		$return = new Newsletter_Subscription_Response(array(
			'success' => $response !== NULL ? TRUE : FALSE,
			'data'    => $response,
			'errors'  => $response !== NULL ? array() : array(array('error' => $this->curl->error_string, 'code' => $this->curl->error_code))
		));
		
		return $return;
	}
	
	public function get_campaign_params($list_id, $parsed_object, $parcel)
	{
		$settings = $parcel->settings->{$this->name};

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

		$params = array(
			'apikey' => $settings->api_key,
			'type'    => 'regular',
			'options' => array(
				'list_id'    => $list_id,
				'subject'    => $parsed_object->subject,
				'from_email' => $parsed_object->from_email,
				'from_name'  => $parsed_object->from_name,
				'to_name'    => $parsed_object->to_name,
				'title'		 => $parcel->entry->title,
			),
			'content' => array(
				'html' => $html_message,
				'text' => $plain_message
			)
		);

		return $params;
	}

	public function create_campaign($list_id, $parsed_object, $parcel)
	{
		$settings = $parcel->settings->{$this->name};

		$params = $this->get_campaign_params($list_id, $parsed_object, $parcel);

		$url = $this->api_url($settings->api_key, 'campaigns/create');

		return $this->post($url, $params);
	}

	public function send_campaign($api_key, $cid)
	{
		$url = $this->api_url($api_key, 'campaigns/send');
		
		return $this->post($url, array(
			'apikey' => $api_key,
			'cid' => $cid
		)); 
	}

	public function get_lists($api_key, $ajax = FALSE)
	{
		$url = $this->api_url($api_key, 'lists/list');

		$lists = $this->_get($url);

		if($lists == NULL)
		{
			$lists = (object) array(
				'total' => 0,
				'data'  => array()
			);
		}

		if(!(bool) $ajax)
		{
			return $lists;
		}

		exit($this->list_rows($lists, (object) array(
			'list_id' => array(),
			'api_key' => $api_key
		)));
	}

	private function _get($url, $params = array())
	{
		return json_decode($this->curl->simple_get($url, $params));
	}
	
	private function post($url, $data = array())
	{
		return json_decode($this->curl->simple_post($url, $data));
	}

	private function _getStruct($email)
	{
		if(is_array($email))
		{
			return (object) $email;
		}

		if(is_object($email))
		{
			return $email;
		}

		$struct = 'euid';

		if(strstr($email, '@'))
		{
			$struct = 'email';
		}

		return array($struct => $email);
	}
}
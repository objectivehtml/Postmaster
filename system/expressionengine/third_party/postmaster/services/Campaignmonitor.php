<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Campaign Monitor
 *
 * Allows you to push email using Campaign Monitor.
 *
 * @package		Postmaster
 * @subpackage	Services
 * @author		Justin Kimbrell
 * @copyright	Copyright (c) 2012, Objective HTML
 * @link 		http://www.objectivehtml.com/postmaster
 * @version		1.0.0
 * @build		20120414
 */

require_once PATH_THIRD.'postmaster/config/postmaster_config.php';
require_once PATH_THIRD.'postmaster/libraries/Postmaster_service.php';

class CampaignMonitor_postmaster_service extends Postmaster_service {

	public $name     = 'CampaignMonitor';
	public $title    = 'Campaign Monitor';
	public $url      = 'http://api.createsend.com/api/v3/';
	public $settings = FALSE;

	public $fields = array(
		'api_key' => array(
			'label' => 'API Key',
			'id'	=> 'compaignmonitor_api_key'
		),
		'client_id' => array(
			'label'   => 'Client',
			'id'      => 'compaignmonitor_client_id',
			'type'    => 'select',
			'options' => array()
		),
	);

	public $description = '
	<p>Send beautiful email campaigns, track the results and manage your subscribers. Earn a tidy profit by letting your clients send at prices you set.</p>

	<h4>Links</h4>

	<ul>
		<li><a href="http://www.campaignmonitor.com/features/">Features</a></li>
		<li><a href="http://www.campaignmonitor.com/pricing/">Pricing</a></li>
		<li><a href="http://www.campaignmonitor.com/support/">Support</a></li>
	</ul>
	';

	public function __construct()
	{
		parent::__construct();
	}
	
	public function subscribers($data)
	{
	
		$return = array();
		$date   = !empty($data['since']) ? date('Y-m-d', strtotime($data['since'])) : NULL;
		$url    = $this->api_url('lists', $data['id'], 'active', array(
			'date'           => !empty($date) ? $date : date('Y-m-01', $this->EE->localize->now),
			'page'           => $data['start'],
			'pagesize'       => $data['limit'],
			'orderfield'     => $data['order_by'],
			'orderdirectory' => $data['sort']
		));
		
		$subscribers = $this->get($url, $data['api_key']);
		
		//var_dump($subscribers);exit();
		foreach($subscribers->Results as $index => $subscriber)
		{
			$result = array(
				$data['prefix'].':email_address' => $subscriber->EmailAddress,
				$data['prefix'].':emailaddress' => $subscriber->EmailAddress,
				$data['prefix'].':EmailAddress' => $subscriber->EmailAddress,
				$data['prefix'].':name' => $subscriber->Name,
				$data['prefix'].':Name' => $subscriber->Name,
				$data['prefix'].':date' => strtotime($subscriber->Date),
				$data['prefix'].':Date' => strtotime($subscriber->Date),
				$data['prefix'].':state' => $subscriber->State,
				$data['prefix'].':State' => $subscriber->State,
				$data['prefix'].':custom_fields' => $subscriber->CustomFields,
				$data['prefix'].':customfields' => $subscriber->CustomFields,
				$data['prefix'].':CustomFields' => $subscriber->CustomFields
			);
			
			$row[$data['prefix'].':index'] = $index;
			$row[$data['prefix'].':count'] = $index+1;
			$row[$data['prefix'].':total'] = count($subscribers->Results);
			$row[$data['prefix'].':email'] = $subscriber->EmailAddress;			
			$row[$data['prefix'].':data']  =  $this->EE->channel_data->utility->add_prefix($data['prefix'], array($result));
			
			$return[$index] = $row;	
		}
		
		return $return;
	}
		
	public function subscribe($data)
	{
		$post = array(
			'EmailAddress' => $data['email'],
			'Name'         => !isset($data['name']) ? ($this->EE->input->post('name', TRUE) ? $this->EE->input->post('name', TRUE) : $data['email']) : $data['name'],
			'CustomFields' => array()
		);
		
		$url = $this->api_url('subscribers', $data['id']);
		
		$response = $this->_send($url, $post, $data['api_key'], FALSE);
		
		$success = POSTMASTER_SUCCESS;
		
		if(!empty($this->curl->error_string))
		{
			$success = POSTMASTER_FAILED;
		}
		
		$return = new Newsletter_Subscription_Response(array(
			'success' => $success == 'success' ? TRUE : FALSE,
			'data'    => $post,
			'errors'  => $success == 'success' ? array() : array(array('error' => lang('postmaster_invalid_email'), 'code' => $this->curl->error_code))
		));
		
		return $return;
		
		/*
		$params = array(
			'apikey'            => $data['api_key'],
			'listid'            => $data['id'],
			'email_address'     => $data['email'],
			'email_type'		=> $data['email_type'],
			'double_optin'      => (bool) $this->param($data['post'], 'double_optin', TRUE),
			'update_existing'   => (bool) $this->param($data['post'], 'update_existing', FALSE),
			'replace_interests' => (bool) $this->param($data['post'], 'replace_interests', TRUE),
			'send_welcome'      => (bool) $this->param($data['post'], 'send_welcome', FALSE),	
		);
		
		$unset = array('double_optin', 'update_existing', 'replace_interests', 'send_welcome');
		
		foreach($unset as $var)
		{
			unset($data['post'][$var]);
		}
		
		$params['merge_vars'] = $data['post'];
	
		$response = $this->post($url, $params);
		*/
		
		$return = new Newsletter_Subscription_Response(array(
			'success' => $response === TRUE ? TRUE : FALSE,
			'data'    => $response,
			'errors'  => $response === TRUE ? array() : array(array('error' => $response->error, 'code' => $response->code))
		));
		
		return $return;
	}
	
	public function unsubscribe($data)
	{
		$post = array(
			'EmailAddress' => $data['email']
		);
		
		$url = $this->api_url('subscribers', $data['id'], 'unsubscribe');
		
		$response = $this->_send($url, $post, $data['api_key'], FALSE);
		
		$success = POSTMASTER_SUCCESS;
		
		if(!empty($this->curl->error_string))
		{
			$success = POSTMASTER_FAILED;
		}
		
		$return = new Newsletter_Subscription_Response(array(
			'success' => $success == 'success' ? TRUE : FALSE,
			'data'    => $post,
			'errors'  => $success == 'success' ? array() : array(array('error' => lang('postmaster_invalid_email'), 'code' => $this->curl->error_code))
		));
		
		return $return;
	}
	
	public function send($parsed_object, $parcel)
	{
		$settings = $this->get_settings($parcel->settings);
		
		$html_url = $this->EE->postmaster_lib->current_url('ACT', $this->EE->channel_data->get_action_id('Postmaster_mcp', 'template')).'&entry_id='.$parcel->entry->entry_id.'&parcel_id='.$parcel->id;

		$text_url = $html_url.'&strip_tags=TRUE';

		$post = array(
			'Name'      => $parcel->entry->title,
			'Subject'   => $parsed_object->subject,
			'FromName'  => $parsed_object->from_name,
			'FromEmail' => $parsed_object->from_email,
			'ReplyTo'   => $parsed_object->from_email,
			'HtmlUrl'   => $html_url,
			'TextUrl'   => $text_url,
			'ListIDs'   => $settings->list_id
		);
	
		$campaign_id = $this->create_campaign($post, $settings);
		$response    = $this->send_campaign($campaign_id, $parsed_object, $settings);

		return new Postmaster_Service_Response(array(
			'status'     => $response ? POSTMASTER_SUCCESS : POSTMASTER_FAILED,
			'parcel_id'  => $parcel->id,
			'channel_id' => $parcel->channel_id,
			'author_id'  => $parcel->entry->author_id,
			'entry_id'   => $parcel->entry->entry_id,
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

	private function send_campaign($campaign_id, $parsed_object, $settings)
	{
		$post = array(
			'ConfirmationEmail' => $parsed_object->from_email,
			'SendDate'			=> date('Y-m-d H:i', $this->now)
 		);

		return $this->_send($this->api_url('campaigns', $campaign_id, 'send'), $post, $settings->api_key);
	}

	private function create_campaign($post, $settings)
	{
		return $this->_send($this->api_url('campaigns', $settings->client_id), $post, $settings->api_key);
	}

	private function _send($url, $post, $api_key, $show_error = TRUE)
	{
		$this->curl->create($url);
		$this->curl->http_login($api_key, '');
		
		$this->curl->post(json_encode($post), array(
			CURLOPT_USERAGENT	   => 'Postmaster v'.POSTMASTER_VERSION,
			CURLOPT_HTTPHEADER     => array(
				'Content-Type: application/json'
			)
		));
		
		$response = $this->curl->execute();

		if($show_error)
		{
			if(!$response && !empty($this->curl->error_string))
			{
				return $this->show_error($this->curl->error_string);
			}
		}
		
		return json_decode($response);
	}

	public function display_settings($settings, $parcel)
	{
		$full_settings = $settings;

		$settings = $this->get_settings($settings);

		$this->settings = $settings;

		if(!empty($settings->api_key))
		{
			$clients  = $this->get_clients($settings->api_key);
			$options  = array();

			foreach($clients as $client)
			{
				$options[$client->ClientID] = $client->Name;
			}

			$this->fields['client_id']['options'] = $options;
		}

		$html = $this->build_table($full_settings, $this->fields);

		$html .= '
		<h3>Mailing Lists</h3>
		<p>Select all of the following lists in which you want to use to send your campaign. <a href="#" class="mailchimp-refresh">Refresh the List</a></p>';

		$html .= $this->display_mailing_lists($settings, $parcel);

		return $html;
	}

	public function display_mailing_lists($settings, $parcel)
	{
		$client_url = $this->call_url('client_options');
		$list_url   = $this->call_url('list_rows');

		$html = "
		<script type=\"text/javascript\">
			$(document).ready(function() {

				function getClients() {
					var url = '".$client_url."';
					var apiKey = $('#compaignmonitor_api_key').val();

					if(apiKey != \"\") {
						$.get(url+'&api_key='+apiKey+'&ajax=1', function(html) {
							$('#compaignmonitor_client_id').html(html);
							getLists();
						});
					}
				}

				//getClients();

				function getLists() {
					var url = '".$list_url."';
					var apiKey = $('#compaignmonitor_api_key').val();
					var clientId = $('#compaignmonitor_client_id').val();

					$.get(url+'&api_key='+apiKey+'&client='+clientId+'&ajax=1', function(html) {
						$('#campaignmonitor-lists').html(html);
					});
				}

				var currentKey;

				$('#compaignmonitor_api_key').focus(function() {
					currentKey = $(this).val();
				});

				$('#compaignmonitor_api_key').blur(function() {
					if(currentKey != $(this).val()) {
						getClients();
					}
				});

				$('#compaignmonitor_client_id').change(function() {
					getLists();
				});

				$('.campaignmonitor-refresh').click(function() {

					var apiKey = $('#compaignmonitor_api_key').val();

					alert(client_url);

					return false;
				});

			});
		</script>

		<ul id=\"campaignmonitor-lists\">";
		
			$html .= $this->list_rows($settings->api_key, $settings->client_id);

		$html .= '
		</ul>';

		return $html;
	}

	public function get_clients($api_key)
	{
		$url = $this->api_url('clients');

		return $this->get($url, $api_key);
	}

	public function client_options($api_key, $ajax = FALSE)
	{
		$data  = $this->get_clients($api_key);
		$html = NULL;

		foreach($data as $client)
		{
			$html = '<option value="'.$client->ClientID.'">'.$client->Name.'</option>';
		}

		if(!$ajax)
		{
			return $html;
		}

		exit($html);
	}

	public function get_lists($api_key, $client_id)
	{
		if(!empty($api_key) && !empty($client_id))
		{
			$url  = $this->api_url('clients', $client_id, 'lists');
			
			$data = $this->get($url, $api_key);			
		}
		else
		{
			$data = array();
		}

		return $data;
	}

	public function list_rows($api_key, $client_id, $ajax = FALSE)
	{	
		$settings = $this->settings;
		$data 	  = $this->get_lists($api_key, $client_id);

		$html = NULL;

		foreach($data as $row)
		{
			if(!is_array($settings->list_id))
			{
				$settings->list_id = array();
			}

			//var_dump($settings->list_id);exit();

			$checked = in_array($row->ListID, $settings->list_id) ? 'checked="checked"' : NULL;
			$html .= '
			<li><label><input type="checkbox" name="setting['.$this->name.'][list_id][]" value="'.$row->ListID.'" '.$checked.' /> '.$row->Name.'</label></li>';
		}

		if(!$ajax)
		{
			return $html;
		}

		exit($html);
	}

	public function api_url($method, $id = FALSE, $endpoint = FALSE, $params = array())
	{
		return $this->url . $method . ( $id ? '/' . $id : NULL) . ($endpoint ? '/' . $endpoint : NULL) . '.json?' . http_build_query($params);
	}

	public function get($url, $api_key)
	{
		$this->curl->create($url);
		$this->curl->http_login($api_key, '');
		
		$response = $this->curl->execute();

		if(!empty($this->curl->error_string))
		{
			show_error($this->curl->error_string);
		}

		return json_decode($response);
	}

	public function default_settings()
	{
		return (object) array(
			'api_key'           => '',
			'client_id'         => '',
			'html_template_url' => '',
			'text_template_url' => '',
			'list_id'           => array()
		);
	}

}
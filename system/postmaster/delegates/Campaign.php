<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Newsletter Delegate
 * 
 * @package		Delegates
 * @author		Justin Kimbrell
 * @copyright	Copyright (c) 2012, Objective HTML
 * @link 		http://www.objectivehtml.com/
 * @version		0.1.1
 * @build		20120609
 */

if(!class_exists('Postmaster_base_delegate'))
{
	require_once PATH_THIRD . 'postmaster/libraries/Postmaster_base_delegate.php';
}

class Campaign_postmaster_delegate extends Postmaster_base_delegate {
	
	public $name        = 'Email Campaign Manager';
	public $description = 'Easily manage your email campaign subscribers using MailChimp and/or CampaignMonitor.';	
	public $doctag      = 'Email Campaign Manager';
	
	protected $service;
	
	public function __construct()
	{
		parent::__construct($this->name);
		
		$this->EE->load->library('postmaster_lib');
		
		$this->lib = $this->EE->postmaster_lib;
	}
	
	public function load_service()
	{		
		$service = $this->param('service', FALSE, FALSE, TRUE);
		$service = $this->lib->load_service($service);
		
		$this->service = $service;
		
		return $this->service;
	}
	
	private function post($name, $decode = FALSE, $default = FALSE, $xss = TRUE)
	{
		$value = $this->EE->input->post($name, $xss);
		
		if(!$value)
		{
			$value = $default;
		}
		
		if($decode)
		{
			$value = $this->EE->base_form->decode($value);
		}
		
		return $value;
	}
	
	public function is_subscribed()
	{
		$this->load_service();
		
		$data = array(
			'api_key' => $this->param('key', $this->param('api_key')),
			'id'      => $this->param('list', FALSE, FALSE, TRUE),
			'email'	  => $this->param('email', FALSE, FALSE, TRUE),
			'group_id'  => $this->param('group_id'),
			'groups'    => $this->param('groups')
		);

		$subscribed = $this->service->is_subscribed($data);
		
		if($this->EE->TMPL->tagdata)
		{
			if($subscribed)
			{
				return $this->EE->TMPL->tagdata;
			}

			return NULL;
		}
		
		return $subscribed;
	}

	public function subscribers()
	{
		$this->load_service();
		
		$data = array(
			'api_key'   => $this->param('key', $this->param('api_key')),
			'id'        => $this->param('list', FALSE, FALSE, TRUE),
			'status'    => $this->param('subscribed'),
			'limit'     => $this->param('limit', $this->param('page_size', 100)),
			'since'     => $this->param('since', ''),
			'start'     => $this->param('start', $this->param('page', 1)), 
			'order_by'  => $this->param('order_by', $this->param('orderby', 'email')),
			'sort'      => $this->param('sort', 'asc'),
			'prefix'    => $this->param('prefix', 'subscriber'),
			'group_id'  => $this->param('group_id'),
			'groups'    => $this->param('groups')
		);
				
		$subscribers = $this->service->subscribers($data);
		
		if(count($subscribers) == 0)
		{
			return $this->EE->TMPL->no_results();	
		}
		
		return $this->parse($subscribers);
	}
	
	public function get_subscribers()
	{
		return $this->subscribers();	
	}
	
	public function subscribe()
	{
		return $this->action(TRUE);
	}
	
	public function unsubscribe()
	{
		return $this->action(FALSE);
	}
	
	public function unsubscribe_form()
	{
		return $this->form(FALSE, 'newsletter_unsubscribe_');
	}
	
	public function subscribe_form()
	{
		return $this->form(TRUE, 'newsletter_subscribe_');
	}
	
	private function action($subscribe)
	{
		$service = $this->load_service();
		$email   = $this->param('email', FALSE, FALSE, TRUE);

		if($email == 'CURRENT_USER')
		{
			$email = $this->EE->session->userdata('email');
		}

		$data          = array(
			'return'     => $this->param('return', $this->EE->config->site_url()),
			'api_key'    => $this->param('key', $this->param('api_key', FALSE, FALSE, TRUE)),
			'email'      => $email,
			'id'	 	 => $this->param('list', FALSE, FALSE, TRUE),
			'email_type' => $this->param('email_type', 'html'),
			'name' 		 => $this->param('name', $email),
			'first_name' => $this->param('first_name', $this->param('fname')),
			'last_name'  => $this->param('last_name', $this->param('lname')),
		);
		
		foreach($this->EE->TMPL->tagparams as $index => $value)
		{
			if(!isset($data[$index]))
			{
				$data['post'][$index] = $this->param($index);
			}
		}
		
		if($subscribe)
		{
			$response = $service->subscribe($data);
		}
		else
		{
			$response = $service->unsubscribe($data);
		}
		
		$vars = array(
			'success' => $response->success,
			'errors'  => count($response->errors) > 0 ? $response->errors : array(),
			'data'    => $response->data
		);
		
		if($response->success && $this->param('success_return'))
		{
			$this->EE->functions->redirect($this->param('success_return'));
		}
		
		if(!$response->success && $this->param('failed_return'))
		{
			$this->EE->functions->redirect($this->param('failed_return'));
		}
		
		return $this->parse(array($vars));
	}
	
	private function form($subscribe, $prefix)
	{
		$this->load_service();
		
		if($this->validate($this->service, 'subscribe'))
		{
			$this->EE->load->library('base_form');
		
			$this->EE->base_form->clear();
			$this->EE->base_form->tagdata = $this->EE->TMPL->tagdata;
	
			$this->EE->base_form->set_rule('email', 'required|email');
			
			if((bool) $this->post($prefix.'form'))
			{				
				if(count($this->EE->base_form->field_errors) == 0)
				{
					$service = $this->EE->input->post($prefix.'service');
					$service = $this->EE->base_form->decode($service);
					$service = $this->lib->load_service($service);
					
					$api_key = $this->post($prefix.'id', TRUE);
					
					$data = array(
						'return' => $this->post('return', TRUE),
						'api_key' => $api_key,
						'email' => $this->post('email', FALSE),
						'id' => $this->post($prefix.'list', TRUE),
						'email_type' => $this->post('email_type', FALSE, 'html'),
						'first_name' => $this->post('first_name', FALSE, $this->post('fname', FALSE)),
						'last_name' => $this->post('last_name', FALSE, $this->post('lname', FALSE)),
					);

					$reserved = array('XID', 'site_url', 'required', 'secure_return', 'ajax_response', 'base_form_submit', 'return', 'rule', 'email');
					
					$data['post'] = array();

					if($group_id = $this->param('group_id'))
					{
						$data['post']['group_id'] = $group_id;
					}
					
					if($group_name = $this->param('group_name'))
					{
						$data['post']['group_name'] = $group_name;
					}
					
					if($groups = $this->param('groups'))
					{
						$data['post']['groups'] = $groups;
					}
					
					if($double_optin = $this->param('double_optin'))
					{
						$data['post']['double_optin'] = $double_optin;
					}

					if($update_existing = $this->param('update_existing'))
					{
						$data['post']['update_existing'] = $update_existing;
					}
					
					if($replace_interests = $this->param('replace_interests'))
					{
						$data['post']['replace_interests'] = $replace_interests;
					}

					if($send_welcome = $this->param('send_welcome'))
					{
						$data['post']['send_welcome'] = $send_welcome;
					}

					foreach($_POST as $index => $value)
					{
						if(!preg_match("/^".$prefix."/", $index) && !in_array($index, $reserved))
						{
							$data['post'][$index] = $this->post($index, FALSE, FALSE, TRUE);
						}
					}

					if($subscribe)
					{
						$response = $service->subscribe($data);
					}
					else
					{
						$response = $service->unsubscribe($data);
					}

					if($this->post('ajax_response', TRUE) == 'y')
					{
						$this->json($response);
					}
					
					if(!$response->success)
					{
						foreach($response->errors as $error)
						{
							$this->EE->base_form->set_error($error['error']);
						}
					}
					else
					{
						$return = $this->post('return', TRUE);
						
						$this->EE->functions->redirect($return);
					}
					
					//unset($_POST[$this->EE->base_form->validation_field]);	
				}				
			}
			
			$hidden_fields = array(
				$prefix.'form' => TRUE,
				$prefix.'service' => $this->param('service', FALSE, FALSE, TRUE),
				$prefix.'id' => $this->param('key', $this->param('api_key', FALSE, FALSE, TRUE)),
				$prefix.'list' => $this->param('list', FALSE, FALSE, TRUE),
				$prefix.'email_type' => $this->param('email_type', ''),
				$prefix.'group_id' => $this->param('group_id'),
				$prefix.'groups' => $this->param('groups'),
			);
			
			return $this->EE->base_form->open($hidden_fields);			
		}
	}
	
}

class Newsletter_Subscription_Response {

	public  $success,
			$errors,
			$data;

	public function __construct($data)
	{
		foreach($data as $index => $value)
		{
			$this->set($index, $value);
		}
		
	}

	public function get($name)
	{
		return isset($this->$name) ? $this->$name : FALSE;
	}

	public function set($name, $value)
	{
		if(property_exists(__CLASS__, $name))
		{
			$this->$name = $value;
		}
	}
}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'third_party/postmaster/libraries/Curl.php';
require_once APPPATH.'third_party/postmaster/libraries/Uuid.php';

abstract class Postmaster_core {

	public $name, $description, $now, $settings;

	public function __construct()
	{
		$this->EE =& get_instance();
		
		$this->EE->load->library('postmaster_lib');
		
		$this->curl = new Curl();
		$this->uid  = new Uuid();
		$this->lib  = $this->EE->postmaster_lib;
		$this->now  = $this->EE->localize->now;
	}
	
	public function action_url($class, $method)
	{
		return $this->EE->postmaster_lib->current_url('ACT', $this->EE->channel_data->get_action_id($class, $method));
	}
	
	public function delegate_url()
	{
		return $this->action_url('Postmaster', 'delegate_action');
	}

	public function call_url($method, $params = array())
	{
		$base_url = $this->action_url('postmaster_mcp', 'call');

		$params = array_merge(
			array(
				'service'        => $this->name,
				'service_method' => $method
			),
			$params
		);

		return $base_url . '&' . http_build_query($params);
	}

	public function json($data)
	{
		header('Content-header: application/json');

		exit(json_encode($data));
	}

	public function show_error($error)
	{
		$this->EE->output->show_user_error('general', '<b>'.$this->name.'</b> - '.$error);
	}
}
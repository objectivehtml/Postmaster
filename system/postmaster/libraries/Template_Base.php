<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'Base_class.php';

abstract class Template_Base extends Base_class {

	public  $id,
			$title,
			$to_name,
			$to_email,
			$from_name,
			$from_email,
			$reply_to,
			$channel_id,
			$cc,
			$bcc,
			$subject,
			$message,
			$settings,
			$fields,
			$height,
			$default_theme,
			$button,
			$action,
			$return,
			$service,
			$post_date_specific,
			$post_date_relative,
			$send_every,
			$extra_conditionals,
			$parser_url,
			$editor_settings,
			$site_id;
			
			
	public function __construct($params = array())
	{
		parent::__construct($params);
		
		$this->EE =& get_instance();
		
		$this->site_id         = config_item('site_id');
		$this->parser_url      = $this->current_url('ACT', $this->EE->channel_data->get_action_id('Postmaster_mcp', 'parser')) . '&site_id='.config_item('site_id');
		$this->editor_settings = $this->EE->postmaster_model->get_editor_settings_json();
		$this->default_theme   = $this->EE->postmaster_model->get_editor_settings('theme');
		$this->height          = $this->EE->postmaster_model->get_editor_settings('height');
		$this->return          = $this->cp_url('index');
	}
		
	public function themes()
	{
		$this->themes = $this->EE->postmaster_lib->get_themes();

		return $this->themes;
	}
		
	public function default_settings()
	{
		$settings = array();

		foreach($this->services() as $service)
		{
			$settings[$service->name] = $service->default_settings();
		}

		return (object) $settings;
	}
	
	public function services()
	{
		if(!isset($this->EE->postmaster_service))
		{
			$this->EE->load->library('postmaster_service');
		}

		return $this->EE->postmaster_service->get_services($this->settings);
	}
	
	protected function cp_url($method = 'index', $useAmp = FALSE)
	{
		return $this->EE->postmaster_lib->cp_url($method, $useAmp);
	}
	
	protected function current_url($append = '', $value = '')
	{
		return $this->EE->postmaster_lib->current_url($append, $value);
	}
}
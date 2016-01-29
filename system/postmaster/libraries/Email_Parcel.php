<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Mailer
 * * 
 * @package		Postmaster
 * @subpackage	Library
 * @author		Justin Kimbrell
 * @copyright	Copyright (c) 2012, Objective HTML
 * @link 		http://www.objectivehtml.com/postmaster
 * @version		1.0.1
 * @build		20120502
 */

require_once PATH_THIRD . 'postmaster/libraries/Base_service.php';

class Email_Parcel {
	
	public  $id,
			$title,
			$to_name,
			$to_email,
			$from_name,
			$from_email,
			$cc,
			$bcc,
			$reply_to,
			$channel_id,
			$categories,
			$status,
			$statuses,
			$member_group,
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
			$extra_conditionals,
			$post_date_specific,
			$post_date_relative,
			$send_every,
			$parser_url,
			$trigger,
			$enabled = TRUE,
			$site_id,
			$send_once = FALSE,
			$match_explicitly = TRUE;

	public 	$channels, $channel_array;

	public function __construct($parcel = FALSE)
	{
		$this->EE =& get_instance();

		$this->site_id         = config_item('site_id');
		$this->trigger 		   = array();
		$this->channels        = $this->EE->channel_data->get_channels(array('where' => array(
			'site_id' => $this->EE->config->item('site_id')
		)));
		
		$this->editor_settings = $this->EE->postmaster_model->get_editor_settings_json();
		$this->default_theme   = $this->EE->postmaster_model->get_editor_settings('theme');
		$this->height          = $this->EE->postmaster_model->get_editor_settings('height');
		$this->return          = $this->cp_url('index');
		$this->action          = $this->current_url('ACT', $this->EE->channel_data->get_action_id('Postmaster_mcp', 'create_parcel_action'));
		$this->button          = 'Save Parcel';

		if($parcel) {
		
			foreach($parcel as $attr => $value)
			{
				$this->$attr = $value;
			}
			
			$this->send_once     = $this->send_once == '0' ? FALSE : TRUE;
			$this->channel 		 = $this->EE->channel_data->get_channel($this->channel_id);
			$this->settings      = json_decode($this->settings);
			$this->categories    = explode('|', $this->categories);
			$this->member_groups = explode('|', $this->member_groups);
			$this->statuses      = $this->statuses != NULL ? explode('|', $this->statuses) : array();
			$this->action        = $this->current_url('ACT', $this->EE->channel_data->get_action_id('Postmaster_mcp', 'edit_parcel_action'));
			$this->trigger 		 = explode('|', $this->trigger);
		}
		else
		{
			$this->channel    = $this->EE->channel_data->get_channels(array(
				'where' => array(
					'site_id' => $this->EE->config->item('site_id')
				),
				'limit' => 1
			));
			
			$this->channel_id = $this->channel->row('channel_id');
			$this->settings   = $this->default_settings();
			$this->service    = 'ExpressionEngine';
		}

		$this->entries 		 = $this->EE->channel_data->get_channel_entries($this->channel_id, array('limit' => 100));
		$this->categories    = !empty($this->categories)    ? $this->categories    : array();
		$this->statuses      = !empty($this->statuses)      ? $this->statuses      : array();
		$this->member_groups = !empty($this->member_groups) ? $this->member_groups : array();
		$this->parser_url    = $this->current_url('ACT', $this->EE->channel_data->get_action_id('Postmaster_mcp', 'parser')) . '&site_id='.config_item('site_id');
	}
	
	public function channels()
	{
		return $this->channels->result();
	}
	
	public function channel()
	{
		$id = $this->channel_id;

		return $this->channel_array[$id];
	}

	public function default_settings()
	{
		$settings = array();

		foreach($this->services() as $service)
		{
			if(is_object($service))
			{
				$settings[$service->get_name()] = $service->default_settings();
			}
		}

		return (object) $settings;
	}

	public function entries()
	{
		return $this->entries->result();
	}

	public function services()
	{
		$this->EE->load->library('postmaster_service');
		
		return $this->EE->postmaster_service->get_services();
	}

	public function fields()
	{	
		$fields = $this->EE->channel_data->get_channel_fields($this->channel_id, array(
			'order_by' => 'field_label',
			'sort'     => 'ASC'
		))->result();

		$channel_fields = array(
			'title'  		=>	 (object) array(
				'field_name'  	=> 'title',
				'field_label' 	=> 'Title'
			),
			'url_title'  	=> (object) array(
				'field_name'  	=> 'url_title',
				'field_label' 	=> 'URL Title'
			),
			'entry_date'  	=> (object) array(
				'field_name'  	=> 'entry_date',
				'field_label' 	=> 'Entry Date'
			),
			'expiration_date'  	=> (object) array(
				'field_name'  	=> 'expiration_date',
				'field_label' 	=> 'Expiration Date'
			),
			'status'  		=> (object) array(
				'field_name'  	=> 'status',
				'field_label' 	=> 'Status'
			),
			'author'  		=> (object) array(
				'field_name'  	=> 'author',
				'field_label' 	=> 'Author'
			)
		);

		$fields = array_merge($channel_fields, $fields);

		return $fields;
	}
	
	public function statuses()
	{
		return $this->EE->channel_data->get_statuses(array(
			'where' => array(
				'site_id'  => config_item('site_id'),
				'group_id' => $this->channel->row('status_group')
			)
		))->result_array();
	}

	public function themes()
	{
		$this->themes = $this->EE->postmaster_lib->get_themes();

		return $this->themes;
	}
	
	public function category_tree()
	{
		$channel	= $this->EE->channel_data->get_channel($this->channel_id);
		
		$cat_group	= $channel->row('cat_group');

		$this->EE->load->library('api');
		$this->EE->api->instantiate('channel_categories');
		
		$category_tree = array();
		
		if(is_string($cat_group))
		{
			$category_tree = $this->EE->api_channel_categories->category_tree($cat_group);
		}

		if(!$category_tree)
		{
			$category_tree = array();
		}
		
		return $category_tree;
	}
	
	public function member_groups()
	{
		$member_groups = $this->EE->channel_data->get('member_groups', array(
			'where' => array(
				'site_id' => config_item('site_id')
			)
		));
		
		return $member_groups->result();
	}
	
	public function is_enabled()
	{
		return $this->enabled != 0 ? TRUE : FALSE;
	}
	
	private function cp_url($method = 'index', $useAmp = FALSE)
	{
		return $this->EE->postmaster_lib->cp_url($method, $useAmp);
	}
	
	private function current_url($append = '', $value = '')
	{
		return $this->EE->postmaster_lib->current_url($append, $value);
	}
}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Zoo_visitor_mailchimp_subscriptions_postmaster_task extends Base_task {
	
	protected $title = 'Zoo Vistor Mailchimp Subscriptions';
	
	protected $hooks = array(
		array(
			'hook'   => 'zoo_visitor_register_end',
			'method' => 'zoo_visitor_register_end',
			'priority' => 2
		),
		array(
			'hook'   => 'zoo_visitor_cp_register_end',
			'method' => 'zoo_visitor_register_end',
			'priority' => 2
		)
	);
		 	 
	protected $fields = array(		
		'api_key' => array(
			'label' => 'API Key',
			'id'	=> 'api_key',
			'description' => 'Your Mailchimp API Key'
		),

		'list_id' => array(
			'label' => 'List ID',
			'id'	=> 'list_id',
			'description' => 'Your Mailchimp List ID'
		),

		'group_id' => array(
			'label' => 'Group ID',
			'id'	=> 'group_id',
			'description' => 'The Group ID used to segment your subscriptions. If this field is left blank, a group will be automatically created.'
		),
	);

	protected $default_settings = array(
		'api_key' => '',
		'list_id' => '',
		'group_id' => '',
	);
	
	public function zoo_visitor_register_end($member_data, $member_id)
	{
		$settings = $this->get_settings();

		if(!empty($settings->api_key) && !empty($settings->list_id))
		{
			$member = $this->channel_data->get_member($member_id)->row();
			$service = $this->EE->postmaster_lib->load_service('Mailchimp');

			if(empty($settings->group_id))
			{
				$group_id = $service->create_group(
					$settings->api_key, 
					$settings->list_id, 
					'Individual Subscriptions',
					array(
						$member->email,
					)
				);

				if(!is_int($group_id))
				{
					ee()->output->fatal_error($group_id->error);
				}

				$settings->group_id = $group_id;

				$this->task['settings'] = json_decode($this->task['settings']);

				$this->task['settings']->{$this->name} = $settings;

				$this->task['settings'] = json_encode($this->task['settings']);

				$this->EE->postmaster_model->edit_task($this->task['id'], $this->task);
			}
			else
			{
				$grouping = $service->add_grouping(
					$settings->api_key, 
					$settings->list_id, 
					$member->email,
					$settings->group_id
				);
			}
		}
	}
}
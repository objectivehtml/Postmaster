<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Postmaster_model extends CI_Model {
	
	public function __construct()
	{
		parent::__construct();
	}

	public function get_mailbox($where = array(), $limit = FALSE, $offset = 0, $order_by = 'date', $sort = 'asc')
	{
		if(count($where))
		{
			$this->db->where($where);
		}

		if($limit)
		{
			$this->db->limit($limit, $offset);
		}

		if($order_by)
		{
			$this->db->order_by($order_by, $sort);
		}

		return $this->db->get('postmaster_mailbox');
	}

	public function has_sent($entry_id, $parcel_id )
	{
		$entry_id = (int) $entry_id;

		if(!$entry_id)
		{
			return FALSE;
		}

		$this->db->where('parcel_id', $parcel_id);
		$this->db->where('entry_id', $entry_id);

		$record = $this->db->get('postmaster_mailbox');

		return $record->num_rows() == 0 ? FALSE : TRUE;
	}
	
	public function assign_site_id($site_id = FALSE, $parcels = TRUE, $hooks = TRUE)
	{
		if(!$site_id)
		{
			$site_id = config_item('site_id');
		}
		
		$site_id = (int) $site_id;
		
		if($parcels)
		{
			$this->db->where('site_id', NULL);
			$this->db->update('postmaster_parcels', array(
				'site_id' => $site_id
			));
		}
		
		if($hooks)
		{
			$this->db->where('site_id', NULL);
			$this->db->update('postmaster_hooks', array(
				'site_id' => $site_id
			));
		}
	}

	public function add_parcel_to_queue($parsed_object, $parcel, $date = FALSE)
	{
		$where = array(
			'parcel_id'     => $parcel->id
		);
		
		foreach(array('author_id', 'channel_id', 'entry_id') as $key)
		{
			if(isset($parcel->entry->$key))
			{
				$where[$key] = $parcel->entry->$key;
			}
		}
		
		$this->db->where($where);

		$existing = $this->db->get('postmaster_queue');

		if($existing->num_rows() == 0)
		{
			$this->add_to_queue($parsed_object, $parcel, $date, $where);
		}
	}
	
	public function add_hook_to_queue($parsed_object, $hook, $date = FALSE)
	{
		$data['hook_id']   = $hook->id;
		$data['author_id'] = $this->session->userdata('author_id');
		
		$this->add_to_queue($parsed_object, $hook, $date, $data);
	}
	
	public function add_to_queue($parsed_object, $parcel, $date = FALSE, $data = array())
	{
		if(!$date)
		{
			$date = $this->postmaster_lib->get_send_date($parsed_object);
		}

		$gmt_offset = time() - $this->postmaster_lib->now(time());
		
		$data = array_merge($data, array(
			'gmt_date'      => time(),
			'gmt_send_date' => $this->postmaster_lib->strtotime($date) + $gmt_offset,
			'date'     	    => date('Y-m-d H:i:s', $this->postmaster_lib->now()),
			'send_date'     => date('Y-m-d H:i:s', $this->postmaster_lib->strtotime($date)),
			'service'       => $parcel->service,
			'to_name'       => $parsed_object->to_name,
			'to_email'      => $parsed_object->to_email,
			'from_name'     => $parsed_object->from_name,
			'from_email'    => $parsed_object->from_email,
			'cc'            => $parsed_object->cc,
			'bcc'           => $parsed_object->bcc,
			'subject'       => $parsed_object->subject,
			'message'       => $parsed_object->message,
			'send_every'    => $parsed_object->send_every
		));

		$this->db->insert('postmaster_queue', $data);
	}
	
	public function blacklist($email)
	{
		$this->unsubscribe($email);

		if(!$this->is_blacklisted($email))
		{
			$this->db->insert('postmaster_blacklist', array(
				'gmt_date'   => $this->localize->now,
				'ip_address' => $this->input->ip_address(),
				'email'      => $email
			));
		}
	}

	public function convert_data($data, $index)
	{
		$array = array();

		foreach($data->result() as $row)
		{
			$array[$row->$index] = $row;
		}
		
		return $array;
	}

	public function convert_string($string, $pool)
	{
		$explode_array = explode('|', $string);

		$array  = array();
		
		foreach($explode_array as $index => $row)
		{
			if(isset($pool[$row]))
			{
				$array[] = $pool[$row];
			}
		}

		return $array;
	}

	public function install_task_hook($task_id, $obj, $hook)
	{
		$task = $this->get_task($task_id);

		$hook['task_id'] = $task_id;
		$hook['task']    = $task->row('task');

		$extension = array(
			'class'    => 'Postmaster_ext',
			'method'   => 'trigger_task_hook',
			'hook'     => $hook['hook'],
			'priority' => $hook['priority'],
			'version'  => POSTMASTER_VERSION,
			'enabled'  => 'y'
		);

		$existing_ext = $this->db->get_where('extensions', $extension);

		if($existing_ext->num_rows() == 0)
		{
			$this->db->insert('extensions', $extension);
		}

		$class = get_class($obj);
		$file  = 'tasks/' . ucfirst($obj->get_name()) . '.php';

		$this->load->model('postmaster_routes_model');

		$this->postmaster_routes_model->create($class, $hook['method'], $hook['hook'], $file, 'task', $task_id);
	}
	
	public function create_hook($hook)
	{	
		$this->load->library('postmaster_hook');
		$this->load->model('postmaster_routes_model');

		$this->postmaster_hook->set_base_path(PATH_THIRD . 'postmaster/hooks/');
		
		$obj = $this->postmaster_hook->get_hook(!empty($hook['installed_hook']) ? $hook['installed_hook'] : $hook['user_defined_hook']);
			
		$extension = array(
			'class'    => 'Postmaster_ext',
			'method'   => 'trigger_hook',
			'hook'     => $obj->get_hook(),
			'priority' => $hook['priority'],
			'version'  => POSTMASTER_VERSION,
			'enabled'  => 'y'
		);
		
		$existing_ext = $this->db->get_where('extensions', $extension);
		
		if($existing_ext->num_rows() == 0)
		{
			$this->db->insert('extensions', $extension);
		}
		
		if(!isset($hook['site_id']))
		{
			$hook['site_id'] = config_item('site_id');
		}
		
		$hook['actual_hook_name'] = $obj->get_hook();
		$hook['extension_id']     = $this->db->insert_id();
		
		$this->db->insert('postmaster_hooks', $hook);

		$class = get_class($obj);
		$file  = 'hooks/' . ucfirst($obj->get_name()) . '.php';

		$this->postmaster_routes_model->create($class, 'trigger', $obj->get_hook(), $file, 'hook', $this->db->insert_id());
	}

	public function create_task($task)
	{
		$this->load->library('postmaster_task', array(
			'base_path' => PATH_THIRD.'postmaster/tasks/'
		));

		$obj = $this->postmaster_task->get_task($task['task']);
		
		$task['enable_cron'] = 0;

		if($obj->get_enable_cron())
		{
			$task['enable_cron'] = 1;
		}

		$this->db->insert('postmaster_tasks', $task);

		$task_id = $this->db->insert_id();

		if(is_array($obj->get_hooks()))
		{
			foreach($obj->get_hooks() as $hook)
			{
				$this->install_task_hook($task_id, $obj, $hook);
			}
		}
	}

	public function edit_task($id, $task)
	{
		$saved_task = $this->get_task($id);

		$this->load->library('postmaster_task', array(
			'base_path' => PATH_THIRD.'postmaster/tasks/'
		));
		
		$this->load->model('postmaster_routes_model');

		$obj = $this->postmaster_task->load($task['task']);
		
		$task['enable_cron'] = 0;

		if($obj->get_enable_cron())
		{
			$task['enable_cron'] = 1;
		}

		if(is_array($obj->get_hooks()))
		{
			$this->load->model('postmaster_routes_model');

			$this->postmaster_routes_model->delete_task($id);

			foreach($obj->get_hooks() as $hook)
			{
				$this->install_task_hook($id, $obj, $hook);
			}			
		}

		$this->db->where('id', $id);
		$this->db->update('postmaster_tasks', $task);
	}
	
	public function delete_task($id)
	{
		$this->load->model('postmaster_routes_model');
		$this->postmaster_routes_model->delete_task($id);

		$this->db->delete('postmaster_tasks', array(
			'id' => $id
		));

		$this->db->delete('postmaster_routes', array(
			'type' 	 => 'task',
			'obj_id' => $id
		));
	}

	/*
	public function get_task_hook($id)
	{
		$this->db->where('id', $id);

		return $this->db->get('postmaster_task_hooks');
	}

	public function get_task_hooks($id)
	{
		$this->db->where('task_id', $id);

		return $this->db->get('postmaster_task_hooks');
	}

	public function is_task_hook_in_use($task)
	{
		$this->db->where('task_id !=', $task->task_id);
		$this->db->where('hook', $task->hook);

		$hooks = $this->db->get('postmaster_task_hooks');

		return $hooks->num_rows() == 0 ? FALSE : TRUE;
	}

	public function delete_task_hooks($id)
	{
		$hooks = $this->get_task_hooks($id);

		foreach($hooks->result() as $row)
		{
			if(!$this->is_task_hook_in_use($row))
			{
				$this->db->delete('extensions', array(
					'class'  => 'Postmaster_ext',
					'method' => 'route_task_hook',
					'hook'   => $row->hook
				));	
			}
		}

		$this->db->where('task_id', $id);
		$this->db->delete('postmaster_task_hooks');
	}
	*/
	public function duplicate_task($id)
	{
		$this->duplicate('postmaster_tasks', $id);
	}
	
	public function create_notification($hook)
	{		
		$this->db->insert('postmaster_notifications', $hook);
	}
	
	public function delete_hook($hook)
	{
		$hook  = $this->get_hook($hook);
		$hooks = $this->get_hooks(array(
			'where' => array(
				'actual_hook_name' => $hook->row('actual_hook_name')
			)
		));

		if($hook->num_rows() == 0)
		{
			return;
		}
		
		$name = $hook->row('actual_hook_name');
		
		if($hooks->num_rows() == 1)
		{
			$this->db->delete('extensions', array(
				'class'  => 'Postmaster_ext',
				'method' => 'trigger_hook',
				'hook'   => $name
			));	
		}
		
		$this->db->delete('postmaster_hooks', array(
			'id' => $hook->row('id')
		));

		$this->db->delete('postmaster_routes', array(
			'type'   => 'hook',
			'obj_id' => $hook->row('id')
		));
	}
	
	public function get_actual_installed_hooks($hook)
	{
		return $this->db->get_where('postmaster_hooks', array(
			'actual_hook_name' => $hook
		));
	}
	
	public function get_installed_hooks($hook, $json_decode = TRUE)
	{		
		$this->db->order_by('priority', 'asc');
		$this->db->where("(installed_hook != '' AND installed_hook = '$hook') OR (installed_hook = '' AND user_defined_hook = '$hook')", NULL, FALSE);
		
		$return = array();
		
		foreach($this->db->get('postmaster_hooks')->result_array() as $index => $hook)
		{
			$return[$index] = $hook;
			
			if($json_decode)
			{
				$return[$index]['settings'] = json_decode($hook['settings']);
			}
		}
		
		return $return;
	}

	public function edit_hook($id, $hook)
	{
		$saved_hook = $this->get_hook($id);
		
		$this->load->library('postmaster_hook');
		$this->load->model('postmaster_routes_model');

		$this->postmaster_hook->set_base_path(PATH_THIRD . 'postmaster/hooks/');
		
		$obj = $this->postmaster_hook->get_hook(!empty($hook['installed_hook']) ? $hook['installed_hook'] : $hook['user_defined_hook']);
			
		$extension = array(
			'class'    => 'Postmaster_ext',
			'method'   => 'trigger_hook',
			'hook'     => $obj->get_hook(),
			'priority' => $hook['priority'],
			'version'  => POSTMASTER_VERSION,
			'enabled'  => 'y'
		);
		
		$hook['actual_hook_name'] = $obj->get_hook();

		$file  = 'hooks/' . ucfirst($obj->get_name()) . '.php';

		if($this->postmaster_routes_model->existing_by_id($id, 'hook'))
		{
			$this->db->where('extension_id', $saved_hook->row('extension_id'));
			$this->db->update('extensions', $extension);	
		}

		$this->postmaster_routes_model->delete_route($id, 'hook');
		$this->postmaster_routes_model->create(get_class($obj), 'trigger_hook', $obj->get_hook(), $file, 'hook', $id);
		
		$this->db->where('id', $id);
		$this->db->update('postmaster_hooks', $hook);
	}
	
	public function edit_notification($id, $notification)
	{
		$this->db->where('id', $id);
		$this->db->update('postmaster_notifications', $notification);
	}
	
	public function delete_notification($id)
	{
		$this->db->where('id', $id);
		$this->db->delete('postmaster_notifications');
	}
	
	public function create_parcel($parcel)
	{
		if(!isset($parcel['site_id']))
		{
			$parcel['site_id']	  = config_item('site_id');
		}
		
		$this->db->insert('postmaster_parcels', $parcel);
	}

	public function edit_parcel($parcel, $id)
	{
		$this->db->where('id', $id);
		$this->db->update('postmaster_parcels', $parcel);
	}
	
	public function delete_parcel($id)
	{
		$this->db->where('id', $id);
		$this->db->delete('postmaster_parcels');
	}
	
	public function duplicate_parcel($id)
	{
		$this->duplicate('postmaster_parcels', $id);
	}
	
	public function duplicate_hook($id)
	{
		$entry_id = $this->duplicate('postmaster_hooks', $id);
		$entry    = $this->get_hook($id)->row_array();
		
		$ext_entry_id = $this->duplicate('extensions', $entry['extension_id'], 'extension_id');
		
		$this->db->insert('postmaster_hooks', array(
			'extension_id' => $ext_entry_id
		));
	}
	
	public function duplicate_notification($id)
	{
		$this->duplicate('postmaster_notifications', $id);
	}
	
	public function duplicate($table, $id, $id_field = 'id')
	{		
		$entry = $this->channel_data->get($table, array(
			'where' => array(
				$id_field => $id
			)
		))->row_array();

		unset($entry[$id_field]);

		$this->db->insert($table, $entry);
		
		return $this->db->insert_id();
	}
	
	public function get_editor_settings($key = FALSE)
	{
		$settings 		 = $this->db->get('postmaster_editor_settings')->result();
		$settings_array  = array();

		foreach($settings as $setting)
		{
			$settings_array[$setting->key] = $setting->value;
		}

		if($key)
		{
			return $settings_array[$key];
		}

		return $settings_array;
	}
	
	public function get_editor_settings_json()
	{
		$settings = $this->get_editor_settings();

		foreach($settings as $index => $setting)
		{
			if($setting == 'true' || $setting == 'false')
			{
				$settings[$index] = $setting == 'true' ? TRUE : FALSE;
			}

			if(preg_match("/\d/", $setting))
			{
				$settings[$index] = (int) $setting;
			}
		}

		return json_encode($settings);
	}

	public function get_email_queue($start = 'now', $end = FALSE)
	{
		if($start == 'now')
		{
			$start = $this->postmaster_lib->now();
		}

		$this->db->where('send_date <=', date('Y-m-d H:i:s', $this->postmaster_lib->strtotime($start)));

		if($end)
		{
			$this->db->where('send_date >=', date('Y-m-d H:i:s', $this->postmaster_lib->strtotime($end)));
		}

		return $this->db->get('postmaster_queue');
	}
	
	public function get_entry($entry_id)
	{
		$entry = $this->channel_data->get_channel_entry($entry_id);
		
		if($entry && $entry->num_rows() == 1)
		{
			return $entry->row();
		}

		return FALSE;
	}
	
	public function get_parcel($id)
	{
		$this->db->where('id', $id);

		return $this->db->get('postmaster_parcels')->row();
	}

	public function get_parcels($params = array(), $all_sites = FALSE)
	{
		if(!$all_sites)
		{
			if(!isset($params['where']['site_id']))
			{
				$params['where']['site_id'] = config_item('site_id');
			}
		}
		
		$parcels = $this->channel_data->get('postmaster_parcels', $params)->result();

		$channels      = $this->convert_data($this->channel_data->get_channels(), 'channel_id');
		$categories    = $this->convert_data($this->channel_data->get_categories(), 'cat_id');
		$member_groups = $this->convert_data($this->channel_data->get_member_groups(), 'group_id');
		
		foreach($parcels as $index => $parcel)
		{
			if(isset($channels[$parcel->channel_id]))
			{
				$parcels[$index]->channel_name 	 = $channels[$parcel->channel_id]->channel_name;
				$parcels[$index]->channel_title	 = $channels[$parcel->channel_id]->channel_title;
			}
			else
			{
				$parcels[$index]->channel_name = '<i>This channel no longer exists.</i>';
			}

			$parcels[$index]->trigger 		 = explode('|', $parcel->trigger);
			$parcels[$index]->categories  	 = $this->convert_string($parcel->categories, $categories);
			$parcels[$index]->member_groups  = $this->convert_string($parcel->member_groups, $member_groups);
			$parcels[$index]->statuses  	 = $parcel->statuses != NULL ? explode('|', $parcel->statuses) : array();
			$parcels[$index]->settings 		 = json_decode($parcel->settings);
			$parcels[$index]->edit_url		 = $this->postmaster_lib->cp_url('edit_parcel') . '&id='.$parcel->id;
			$parcels[$index]->duplicate_url	 = $this->postmaster_lib->cp_url('duplicate_parcel_action') . '&id='.$parcel->id.'&return=index';
			$parcels[$index]->delete_url	 = $this->postmaster_lib->cp_url('delete_parcel_action') . '&id='.$parcel->id.'&return=index';
		}

		return $parcels;
	}
	
	public function get_hook($id)
	{
		$this->db->where('id', $id);
		
		return $this->db->get('postmaster_hooks');
	}
	
	public function get_hooks($params = array(), $all_sites = FALSE)
	{
		return $this->get('hooks', $params, $all_sites);
	}
	
	public function get_task($id)
	{
		$this->db->where('id', $id);
		
		return $this->db->get('postmaster_tasks');
	}
	
	public function get_tasks($params = array(), $all_sites = FALSE)
	{
		return $this->get('tasks', $params, $all_sites);
	}
	
	public function get_notification($id)
	{
		$this->db->where('id', $id);
		
		return $this->db->get('postmaster_notifications');
	}
		
	public function get_notifications($params = array(), $all_sites = FALSE)
	{
		return $this->get('notifications', $params, $all_sites);
	}
	
	public function get($table, $params = array(), $all_sites = FALSE)
	{
		if(!$all_sites)
		{
			if(!isset($params['where']['site_id']))
			{
				$params['where']['site_id'] = config_item('site_id');
			}
		}
		
		return $this->channel_data->get('postmaster_'.$table, $params);
	}
	
	public function get_member($member_id = FALSE, $prefix = FALSE)
	{
		$member = array();
		
		$member_id = $member_id ? $member_id : $this->session->userdata('member_id');
		
		if($member_id)
		{
			$member = $this->channel_data->get_member($member_id)->row_array();
		}
			
		if($prefix)
		{
			$member = $this->channel_data->utility->add_prefix('member', $member);
		}
		
		return $member;
	}
	
	public function get_channels($site_id = FALSE)
	{
		if(!$site_id)
		{
			$site_id = config_item('site_id');
		}
		
		$channels = $this->channel_data->get_channels(array(
			'where' => array(
				'site_id' => $site_id
			)
		))->result();
		
		return $this->channel_data->utility->reindex($channels, 'channel_id'); 
	}
	
	public function get_channel_fields($channel_id)
	{
		$channel_fields = $this->channel_data->get_channel_fields($channel_id)->result();
		$channel_fields = $this->channel_data->utility->reindex($channel_fields, 'field_name');
		
		return $channel_fields;
	}
	
	public function is_blacklisted($email)
	{
		$this->db->where('email', $email);
		$existing = $this->db->get('postmaster_blacklist');

		if($existing->num_rows() == 0)
		{
			return FALSE;
		}

		return TRUE;
	}
	
	public function remove_from_queue($id)
	{
		$this->db->where('id', $id);
		$this->db->delete('postmaster_queue');
	}

	public function save_editor_settings($settings)
	{
		foreach($settings as $key => $value)
		{
			$this->db->where('key', $key);
			$this->db->update('postmaster_editor_settings', array(
				'value' => $value
			));
		}
	}

	public function get_preview($member_id = FALSE)
	{
		if(!$member_id)
		{
			$member_id = $this->session->userdata('member_id');
		}
		
		return $this->db->where('member_id', $member_id)->get('postmaster_previews');
	}
	
	public function save_preview($message, $member_id = FALSE)
	{
		if(!$member_id)
		{
			$member_id = $this->session->userdata('member_id');
		}
		
		$existing_rows = $this->get_preview($member_id);
		
		if($existing_rows->num_rows() == 0)
		{
			$this->db->insert('postmaster_previews', array(
				'member_id' => $member_id,
				'data' 	    => $message
			));
		}
		else {
			$this->db->where('member_id', $member_id);
			$this->db->update('postmaster_previews', array(
				'data' => $message
			));
		}
		
		return $message;
	}
	
	public function save_response($response)
	{
		if(is_object($response->parcel))
		{
			$response->parcel = json_encode($response->parcel);
		}

		$this->db->insert('postmaster_mailbox', (array) $response);
	}

	public function unsubscribe($email)
	{
		$this->db->where('to_email', $email);
		$this->db->delete('postmaster_queue');
	}
}
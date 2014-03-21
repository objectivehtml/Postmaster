<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Store_report_products_sold_postmaster_notification extends Base_notification {
	
	public $title = 'Store Reporting: Total Products Sold';
	 
	/**
	 * Description
	 * 
	 * @var string
	 */
	 	
	public $description = 'This notification is used when you have products in Store that are owned by various people. Say you have an "event", and each event is owned by a different person. Each week every event owner should receive Store sales reports for the tickets sold for each event. This notifications makes it possible send sales reports on specific days of each week until the event has passed. This is just one use scenario for this, but you can use it for many other things as well.';
	

	protected $tables = array(
		'postmaster_store_report_products_sold' => array(
			'entry_id'	=> array(
				'type'				=> 'int',
				'constraint'		=> 100,
				'primary_key'		=> TRUE,
				'auto_increment'	=> TRUE
			),
			'date' 	=> array(
				'type'	=> 'timestamp',
			),
			'day_of_week' => array(
				'type'	=> 'text'
			),
			'qty'	=> array(
				'type'				=> 'int',
				'constraint'		=> 100
			),
			'total'	=> array(
				'type'				=> 'float'
			),
			'total_orders'	=> array(
				'type'				=> 'int',
				'constraint'		=> 100
			),
		)
	);

	protected $fields = array(
		'channel_name' => array(
			'label' => 'Channel Name'
		),
		/*
		'email_field' => array(
			'label' => 'Email Field Name',
			'description' => 'If define, the email field from the entry will be used as the receiving address. Otherwise, the To Name and To Email fields will be used as normal.'
		),
		*/
		'product_status' => array(
			'label' => 'Product Status'
		),
		'days_to_send' => array(
			'label'       => 'Days to Send Emails',
			'description' => 'Enter days of the week seperated by commas that you desire to send emails. This is good if you have a cron job firing at frequent intervals, and want to prevent emails from getting sent. If these days are set, the emails will only be sent once per week, on the days specified.'
		),
		'omit_expired' => array(
			'label' => 'Omit Expired Entries?',
			'type'  => 'select',
			'settings' => array(
				'options' => array(
					'true'  => 'Yes',
					'false' => 'No'
				)
			)
		),
		'email_field' => array(
			'label'       => 'Email Field',
			'description' => 'If you only want to send emails with entries with valid email addresses, enter the name of the channel field storing the value. Note, you still have to set a To Email value, this setting merely validates that field before attempting to send the notification.',
			'type'  	  => 'input'
		),
		'limit' => array(
			'label'       => 'Limit Entries',
			'description' => 'If desired, you may limit the total entries that are emailed in a single request. Use this setting if you are running into API limit or capacity issues.',
			'type'  	  => 'input'
		)
	);
	
	public function __construct($params = array())
	{	
		parent::__construct($params);
	}

	public function send($vars = array(), $member_data = FALSE, $entry_data = array())
	{
		$where = array();

		$settings = $this->get_settings();

		$channel = $this->channel_data->get_channel_by_name($settings->channel_name);

		if($channel->num_rows() == 0)
		{
			return;
		}


		if($settings->omit_expired == 'true')
		{
			$where['OR expiration_date'] = array(0, '>= '.time());
		}

		if(empty($settings->days_to_send))
		{
			$settings->days_to_send = FALSE;
		}

		$limit = FALSE;

		if(isset($settings->limit) && !empty($settings->limit))
		{
			$limit = $settings->limit;
		
		}

		$channel = $channel->row();
		
		$where['postmaster_store_report_products_sold.date'] = array(
			'IS NULL',
			'or <='.date('Y-m-d 00:00:00', strtotime('-1 day'))
		);

		$entries = $this->channel_data->get_channel_entries($channel->channel_id, array(
			'select' => 'postmaster_store_report_products_sold.*',
			'where' => $where,
			'left join' => array(
				'postmaster_store_report_products_sold' => 'channel_data.entry_id = postmaster_store_report_products_sold.entry_id'
			),
			'limit' => $limit
		));

		foreach($entries->result() as $entry)
		{
			$status = 'new';

			if(!empty($settings->status))
			{
				$status = $settings->status;
			}

			if($this->_should_send($entry->entry_id, $settings->days_to_send))
			{
				$vars   = $this->_get_entry_stats($entry->entry_id, $status);

				$member = $this->channel_data->get_member($entry->author_id)->row_array();

				$this->_insert_or_update($entry->entry_id, array(
					'entry_id'     => $entry->entry_id,
					'date'         => date('Y-m-d 00:00:00', time()),
					'day_of_week'  => date('l', time()),
					'qty' 		   => $vars['qty'],
					'total' 	   => $vars['total'],
					'total_orders' => $vars['total_orders']
				));

				$valid = TRUE;

				if(isset($settings->email_field) && !empty($settings->email_field))
				{
					if(isset($entry->{$settings->email_field}))
					{
						$valid = $this->_validate_email(trim($entry->{$settings->email_field}));
					}
					else
					{
						$valid = FALSE;
					}
				}

				if($valid)
				{
					parent::send($vars, $member, $entry);
				}
			}
		}
	}
	
	public function _validate_email($email)
	{
		if(empty($email))
		{
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Install
	 *
	 * @access	public
	 * @return	void
	 */
	
	public function install()
	{		
		$this->EE->data_forge->update_tables($this->tables);
	}
	
	/**
	 * Update
	 *
	 * @access	public
	 * @param	string 	Current version
	 * @return	void
	 */
	
	public function update($current)
	{		
		$this->EE->data_forge->update_tables($this->tables);
	}

	private function _should_send($entry_id, $days_to_send = FALSE)
	{
		if(!$days_to_send)
		{
			return TRUE;
		}

		if(is_string($days_to_send))
		{
			$days_to_send = explode(',', $days_to_send);
		}

		foreach($days_to_send as $day)
		{
			$day = ucfirst(trim($day));

			if($day == date('l'))
			{
				$entry = $this->_get_entry($entry_id, $day);

				if($entry->num_rows() == 0)
				{
					return TRUE;
				}
			}
		}

		return FALSE;
	}

	private function _insert_or_update($entry_id, $data)
	{
		if(!$this->_exists($entry_id))
		{
			$this->_insert($data);
		}
		else
		{
			$this->_update($entry_id, $data);
		}
	}

	private function _exists($entry_id)
	{
		$this->EE->db->where('entry_id', $entry_id);
		
		return $this->EE->db->get('postmaster_store_report_products_sold')->num_rows() > 0? TRUE : FALSE;
	}

	private function _insert($data)
	{
		$this->EE->db->insert('postmaster_store_report_products_sold', $data);
	}

	private function _update($entry_id, $data)
	{
		$this->EE->db->where('entry_id', $entry_id);
		$this->EE->db->update('postmaster_store_report_products_sold', $data);
	}

	private function _get_entry($entry_id)
	{
		$this->EE->db->where('entry_id', $entry_id);
		$this->EE->db->where('date >=', date('Y-m-d 00:00:00', time()));
		
		return $this->EE->db->get('postmaster_store_report_products_sold');
	}
	
	private function _get_entry_stats($entry_id, $status = 'new')
	{
		$return = $this->EE->db->query("
			SELECT 
				SUM(exp_store_order_items.item_qty) as qty,
				SUM(exp_store_order_items.item_total) AS total,
				COUNT(exp_store_order_items.item_qty) AS total_orders
			FROM 
				exp_store_orders
			LEFT JOIN 
				exp_store_order_items ON exp_store_orders.id=exp_store_order_items.order_id 
			WHERE 
				exp_store_order_items.entry_id = ".$entry_id." AND 
				exp_store_orders.order_status_name = '".$status."'
		")->row_array();

		foreach($return as $index => $value)
		{
			if(empty($value))
			{
				$return[$index] = 0;
			}
		}

		return $return;
	}
}
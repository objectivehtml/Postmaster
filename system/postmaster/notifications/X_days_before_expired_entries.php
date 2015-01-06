<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once PATH_THIRD . 'postmaster/libraries/Postmaster_time.php';

if(!class_exists('Base_notification'))
{
	require_once PATH_THIRD . 'postmaster/libraries/Base_notification.php';
}

class X_days_before_expired_entries_postmaster_notification extends Base_notification {
	
	
	/**
	 * Title
	 * 
	 * @var string
	 */
	 	
	public $title = 'X Days Before Expired Entries';
	
	/**
	 * Description
	 * 
	 * @var string
	 */
	 	
	public $description = 'This notification is used when you want to automatically send emails X days before an entry expires. You can configure as many days as desired prior to the expiration of an entry to send an email.';
	
	
	/**
	 * Default Settings Field Schema
	 * 
	 * @var string
	 */
	 		 	 
	protected $fields = array(
		'threshold' => array(
			'label' 	  => 'Relative Send Date',
			'description' => 'Enter amount of relative time before the expiration date. If the current time is past this relative time and before the expiration, and email will send. Be sure to use a negative number.<br>Example: "-24 hours", "-3 days", "-1 week"',
		),
		'channel' => array(
			'label' 	  => 'Channel Name',
			'description' => 'The name of the channel(s) used to fetch the entries. If multiple channels, separate them with a comma.',
		),
		'status' => array(
			'label' 	  => 'Statuses',
			'description' => 'The statuses of the channel entries. Is multiple statuses, separate them with a comma. If no statuses specification needed, leave the field blank.',
		)
	);
	
	
	/**
	 * Default Settings
	 * 
	 * @var string
	 */
	 	
	protected $default_settings = array();
	
	
	/**
	 * Data Tables
	 * 
	 * @var string
	 */
	 
	protected $tables = array(
		'postmaster_expired_entries_emails' 	=> array(
			'entry_id'	=> array(
				'type'				=> 'int',
				'constraint'		=> 100,
				'primary_key'		=> TRUE,
				'auto_increment'	=> TRUE
			)
		)
	);
	
	 	
	public function __construct($params = array())
	{
		parent::__construct($params);
		
		$this->EE->load->library('encrypt');
	}
	
	public function send()
	{
		$settings = $this->get_settings();
		$channel_names = $this->trim_array(explode(',', $settings->channel));
		$channels = array();
		
		foreach($channel_names as $channel_name)
		{
			$channel = $this->EE->channel_data->get_channel_by_name($channel_name);
			
			if($channel->num_rows() > 0)
			{
				$channels[] = $channel->row('channel_id');
			}
		}
		
		$stasuses = array();
		
		$threshold = strtotime($settings->threshold, $this->EE->localize->now);

		$diff = $this->EE->localize->now - $threshold;

		$where = array('expiration_date >'  => $this->EE->localize->now);

		if($diff >= 0)
		{
			$where['expiration_date <='] = $this->EE->localize->now + $diff;
		}
		else
		{
			$where['expiration_date <='] = $this->EE->localize->now - $diff;
		}

		
		if(!empty($settings->status))
		{
			$where['status'] = $this->trim_array(explode(',', $settings->status), 'or ');
		}
		
		if(!empty($settings->channel))
		{
			$where['channel_titles.channel_id'] = $this->trim_array($channels, 'or ');
		}

		$entries = $this->EE->channel_data->get_entries(array(
			'select' => 'postmaster_expired_entries_emails.entry_id IS NOT NULL as \'has_sent\'',
			'where'  => $where,
			'having' => array(
				'has_sent !=' => 1
			),
			'left join'  => array('postmaster_expired_entries_emails' => 'channel_titles.entry_id = postmaster_expired_entries_emails.entry_id')
		));

		foreach($entries->result() as $entry)
		{	
			$this->notification = $this->EE->postmaster_lib->append($this->notification, 'entry', $entry);
			
			$parse_vars = array();

			$member 	= $this->EE->channel_data->get_member($entry->author_id);
			$response 	= parent::send($parse_vars, $member->row(), $entry);
				
			$data = array(
				'entry_id' => $entry->entry_id	
			);
			
			if(!$this->_existing_entry($entry->entry_id))
			{	
				$this->_insert_entry($entry->entry_id, $data);
			}
			else
			{
				$this->_update_entry($entry->entry_id, $data);
			}
			
		}
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
	
	/**
	 * Update an db entry
	 *
	 * @access	private
	 * @param	int 	A row id
	 * @param	array 	Data array to update
	 * @return	void
	 */
	
	private function _update_entry($id, $data = array())
	{
		$this->EE->db->where('entry_id', $id);
		$this->EE->db->update('postmaster_expired_entries_emails', $data);
	}
	
		
	/**
	 * Insert an db entry
	 *
	 * @access	private
	 * @param	int 	A row id
	 * @param	array 	Data array to update
	 * @return	void
	 */
	
	private function _insert_entry($id, $data = array())
	{
		$data['entry_id'] = $id;
		
		$this->EE->db->insert('postmaster_expired_entries_emails', $data);
	}
	
	
	/**
	 * Get existing db entry
	 *
	 * @access	private
	 * @param	int 	A row id
	 * @param	array 	Data array to update
	 * @return	void
	 */
	
	private function _existing_entry($id)
	{
		$this->EE->db->where('entry_id', $id);
		
		$data = $this->EE->db->get('postmaster_expired_entries_emails');
		
		if($data->num_rows() == 0)
		{
			return FALSE;
		}
		
		return $data;
	}
}
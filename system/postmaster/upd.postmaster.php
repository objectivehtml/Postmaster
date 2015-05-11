<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Postmaster
 * 
 * @package		Postmaster
 * @author		Justin Kimbrell
 * @copyright	Copyright (c) 2012, Objective HTML
 * @link 		http://www.objectivehtml.com/postmaster
 * @version		1.2.0
 * @build		20121217
 */

require 'config/postmaster_config.php';

class Postmaster_upd {

    public $version = POSTMASTER_VERSION;
	public $mod_name;
	public $ext_name;
	public $mcp_name;
	
	private $tables = array(
		'postmaster_editor_settings' => array(
			'key'	=> array(
				'type'				=> 'varchar',
				'constraint'		=> 100,
				'primary_key'		=> TRUE
			),
			'value'	=> array(
				'type'			=> 'text'
			)
		),
		'postmaster_previews' => array(
			'id'	=> array(
				'type'				=> 'int',
				'constraint'		=> 100,
				'primary_key'		=> TRUE,
				'auto_increment'	=> TRUE
			),
			'member_id'	=> array(
				'type'				=> 'int',
				'constraint'		=> 10,
			),
			'data'	=> array(
				'type'			=> 'longtext'
			)
		),
		'postmaster_parcels' 	=> array(
			'id'	=> array(
				'type'				=> 'int',
				'constraint'		=> 100,
				'primary_key'		=> TRUE,
				'auto_increment'	=> TRUE
			),
			'site_id' => array(
				'type'			=> 'int',
				'constraint' 	=> 100
			),
			'channel_id' => array(
				'type'			=> 'int',
				'constraint' 	=> 100
			),
			'title'  => array(
				'type'       => 'varchar',
				'constraint' => 250
			),
			'categories' => array(
				'type'	=> 'text'
			),
			'member_groups' => array(
				'type'	=> 'text'
			),
			'trigger'  => array(
				'type' 		 => 'varchar',
				'constraint' => 100
			),
			'service'  => array(
				'type'       => 'varchar',
				'constraint' => 250
			),
			'statuses' => array(
				'type'	=> 'text'
			),
			'to_name' => array(
				'type'	=> 'text'
			),
			'to_email' => array(
				'type'	=> 'text'
			),
			'from_name' => array(
				'type'	=> 'text'
			),
			'from_email' => array(
				'type'	=> 'text'
			),
			'reply_to' => array(
				'type'	=> 'text'
			),
			'cc' => array(
				'type'	=> 'text'
			),
			'bcc' => array(
				'type'	=> 'text'
			),
			'subject' => array(
				'type'	=> 'text'
			),
			'message'	=> array(
				'type'	=> 'longtext'
			),
			/* New in v1.4 */
			'html_message'	=> array(
				'type'	=> 'longtext'
			),
			/* New in v1.4 */
			'plain_message'	=> array(
				'type'	=> 'longtext'
			),
			'settings' => array(
				'type'	=> 'longtext'
			),
			'extra_conditionals' => array(
				'type'	=> 'text'
			),
			'post_date_specific'  => array(
				'type' => 'text'
			),
			'post_date_relative'  => array(
				'type' => 'text'
			),
			'send_every'  => array(
				'type' => 'varchar',
				'constraint' => 100
			),
			'enabled'  => array(
				'type'       => 'int',
				'constraint' => 1,
				'default'    => 1
			),
			'send_once'  => array(
				'type'       => 'int',
				'constraint' => 1,
				'default'    => 0
			),
			'match_explicitly' => array(
				'type'       => 'int',
				'constraint' => 1,
				'default'    => 1
			)
		),
		'postmaster_hooks' 	=> array(
			'id'	=> array(
				'type'				=> 'int',
				'constraint'		=> 100,
				'primary_key'		=> TRUE,
				'auto_increment'	=> TRUE
			),
			'site_id' => array(
				'type'			=> 'int',
				'constraint' 	=> 100
			),
			'title'  => array(
				'type'       => 'varchar',
				'constraint' => 250
			),
			'service'  => array(
				'type'       => 'varchar',
				'constraint' => 250
			),
			'installed_hook'  => array(
				'type'       => 'varchar',
				'constraint' => 250
			),
			'user_defined_hook'  => array(
				'type'       => 'varchar',
				'constraint' => 250
			),
			'actual_hook_name'  => array(
				'type'       => 'varchar',
				'constraint' => 250
			),
			'priority'  => array(
				'type'       => 'varchar',
				'constraint' => 250
			),
			'to_name' => array(
				'type'	=> 'text'
			),
			'to_email' => array(
				'type'	=> 'text'
			),
			'from_name' => array(
				'type'	=> 'text'
			),
			'from_email' => array(
				'type'	=> 'text'
			),
			'reply_to' => array(
				'type'	=> 'text'
			),
			'cc' => array(
				'type'	=> 'text'
			),
			'bcc' => array(
				'type'	=> 'text'
			),
			'subject' => array(
				'type'	=> 'text'
			),
			'message'	=> array(
				'type'	=> 'longtext'
			),
			/* New in v1.4 */
			'html_message'	=> array(
				'type'	=> 'longtext'
			),
			/* New in v1.4 */
			'plain_message'	=> array(
				'type'	=> 'longtext'
			),
			'settings' => array(
				'type'	=> 'longtext'
			),
			'post_date_specific'  => array(
				'type' => 'text'
			),
			'post_date_relative'  => array(
				'type' => 'text'
			),
			'send_every'  => array(
				'type' => 'varchar',
				'constraint' => 100
			),
			'extension_id'  => array(
				'type' => 'int',
				'constraint' => 10
			),
			'extra_conditionals' => array(
				'type'	=> 'text'
			),
			'enabled'  => array(
				'type'       => 'int',
				'constraint' => 1,
				'default'    => 1
			)
		),

		'postmaster_notifications' 	=> array(
			'id'	=> array(
				'type'				=> 'int',
				'constraint'		=> 100,
				'primary_key'		=> TRUE,
				'auto_increment'	=> TRUE
			),
			'site_id' => array(
				'type'			=> 'int',
				'constraint' 	=> 100
			),
			'title'  => array(
				'type'       => 'varchar',
				'constraint' => 250
			),
			'service'  => array(
				'type'       => 'varchar',
				'constraint' => 250
			),
			'notification'  => array(
				'type'       => 'varchar',
				'constraint' => 250
			),
			'to_name' => array(
				'type'	=> 'text'
			),
			'to_email' => array(
				'type'	=> 'text'
			),
			'from_name' => array(
				'type'	=> 'text'
			),
			'from_email' => array(
				'type'	=> 'text'
			),
			'reply_to' => array(
				'type'	=> 'text'
			),
			'cc' => array(
				'type'	=> 'text'
			),
			'bcc' => array(
				'type'	=> 'text'
			),
			'subject' => array(
				'type'	=> 'text'
			),
			'message'	=> array(
				'type'	=> 'longtext'
			),
			/* New in v1.4 */
			'html_message'	=> array(
				'type'	=> 'longtext'
			),
			/* New in v1.4 */
			'plain_message'	=> array(
				'type'	=> 'longtext'
			),
			'settings' => array(
				'type'	=> 'longtext'
			),
			'post_date_specific'  => array(
				'type' => 'text'
			),
			'post_date_relative'  => array(
				'type' => 'text'
			),
			'send_every'  => array(
				'type' => 'varchar',
				'constraint' => 100
			),
			'extra_conditionals' => array(
				'type'	=> 'text'
			),
			'enabled'  => array(
				'type'       => 'int',
				'constraint' => 1,
				'default'    => 1
			)
		),
		
		'postmaster_queue' 	=> array(
			'id'	=> array(
				'type'				=> 'int',
				'constraint'		=> 100,
				'primary_key'		=> TRUE,
				'auto_increment'	=> TRUE
			),
			'parcel_id' => array(
				'type'			=> 'int',
				'constraint' 	=> 100
			),
			'channel_id' => array(
				'type'			=> 'int',
				'constraint' 	=> 100
			),
			'author_id' => array(
				'type'			=> 'int',
				'constraint' 	=> 100
			),
			'hook_id' => array(
				'type'			=> 'int',
				'constraint' 	=> 100
			),
			'entry_id' => array(
				'type'			=> 'int',
				'constraint' 	=> 100
			),
			'gmt_date' => array(
				'type'			=> 'int',
				'constraint' 	=> 100
			),
			'date' => array(
				'type'			=> 'timestamp'
			),
			'gmt_send_date' => array(
				'type'			=> 'int',
				'constraint' 	=> 100
			),
			'send_date' => array(
				'type'			=> 'timestamp'
			),
			'service'  => array(
				'type'       => 'varchar',
				'constraint' => 250
			),
			'to_name' => array(
				'type'	=> 'text'
			),
			'to_email' => array(
				'type'	=> 'text'
			),
			'from_name' => array(
				'type'	=> 'text'
			),
			'from_email' => array(
				'type'	=> 'text'
			),
			'cc' => array(
				'type'	=> 'text'
			),
			'bcc' => array(
				'type'	=> 'text'
			),
			'subject' => array(
				'type'	=> 'text'
			),
			'message'	=> array(
				'type'	=> 'longtext'
			),
			/* New in v1.4 */
			'html_message'	=> array(
				'type'	=> 'longtext'
			),
			/* New in v1.4 */
			'plain_message'	=> array(
				'type'	=> 'longtext'
			),
			'send_every'  => array(
				'type' => 'varchar',
				'constraint' => 100
			)
		),
		'postmaster_mailbox' 	=> array(
			'id'	=> array(
				'type'				=> 'int',
				'constraint'		=> 100,
				'primary_key'		=> TRUE,
				'auto_increment'	=> TRUE
			),
			'parcel_id' => array(
				'type'			=> 'int',
				'constraint' 	=> 100
			),
			'channel_id' => array(
				'type'			=> 'int',
				'constraint' 	=> 100
			),
			'author_id' => array(
				'type'			=> 'int',
				'constraint' 	=> 100
			),
			'entry_id' => array(
				'type'			=> 'int',
				'constraint' 	=> 100
			),
			'gmt_date' => array(
				'type'			=> 'int',
				'constraint' 	=> 100
			),
			'date' => array(
				'type'			=> 'timestamp'
			),
			'service'  => array(
				'type'       => 'varchar',
				'constraint' => 250
			),
			'to_name' => array(
				'type'	=> 'text'
			),
			'to_email' => array(
				'type'	=> 'text'
			),
			'from_name' => array(
				'type'	=> 'text'
			),
			'from_email' => array(
				'type'	=> 'text'
			),
			'cc' => array(
				'type'	=> 'text'
			),
			'bcc' => array(
				'type'	=> 'text'
			),
			'subject' => array(
				'type'	=> 'text'
			),
			'message'	=> array(
				'type'	=> 'longtext'
			),
			/* New in v1.4 */
			'html_message'	=> array(
				'type'	=> 'longtext'
			),
			/* New in v1.4 */
			'plain_message'	=> array(
				'type'	=> 'longtext'
			),
			'status'  => array(
				'type'       => 'text'
			),
			'parcel'  => array(
				'type'       => 'longtext'
			)
		),
		'postmaster_blacklist' 	=> array(
			'id'	=> array(
				'type'				=> 'int',
				'constraint'		=> 100,
				'primary_key'		=> TRUE,
				'auto_increment'	=> TRUE
			),
			'gmt_date' => array(
				'type'			=> 'int',
				'constraint' 	=> 100
			),
			'date' => array(
				'type'			=> 'timestamp'
			),
			'ip_address' => array(
				'type'			=> 'varchar',
				'constraint' 	=> 100
			),
			'email' => array(
				'type'			=> 'varchar',
				'constraint' 	=> 250
			)
		),
		'postmaster_routes' 	=> array(
			'id'	=> array(
				'type'				=> 'int',
				'constraint'		=> 100,
				'primary_key'		=> TRUE,
				'auto_increment'	=> TRUE
			),
			'obj_id'	=> array(
				'type'				=> 'int',
				'constraint'		=> 100,
			),
			'class' => array(
				'type'			=> 'varchar',
				'constraint' 	=> 100
			),
			'method' => array(
				'type'			=> 'varchar',
				'constraint' 	=> 100
			),
			'hook'   => array(
				'type'			=> 'varchar',
				'constraint' 	=> 100
			),
			'file'   => array(
				'type'			=> 'varchar',
				'constraint' 	=> 100
			),
			'type'   => array(
				'type'			=> 'varchar',
				'constraint' 	=> 100
			)
		),
		
		'postmaster_tasks' 	=> array(
			'id'	=> array(
				'type'				=> 'int',
				'constraint'		=> 100,
				'primary_key'		=> TRUE,
				'auto_increment'	=> TRUE
			),
			'site_id' => array(
				'type'			=> 'int',
				'constraint' 	=> 100
			),
			'title'  => array(
				'type'       => 'varchar',
				'constraint' => 250
			),
			'task'  => array(
				'type'       => 'varchar',
				'constraint' => 250
			),
			'settings' => array(
				'type'	=> 'longtext'
			),
			'enabled'  => array(
				'type'       => 'int',
				'constraint' => 1,
				'default'    => 1
			),
			'enable_cron'  => array(
				'type'       => 'int',
				'constraint' => 1,
				'default'    => 0
			)
		)
	);
	
	private $actions = array(
		array(
		    'class'     => 'Postmaster_mcp',
		    'method'    => 'save_settings'
		),
		array(
		    'class'     => 'Postmaster_mcp',
		    'method'    => 'save_editor_settings'
		),
		array(
		    'class'     => 'Postmaster_mcp',
		    'method'    => 'create_hook_action'
		),
		array(
		    'class'     => 'Postmaster_mcp',
		    'method'    => 'edit_hook_action'
		),
		array(
		    'class'     => 'Postmaster_mcp',
		    'method'    => 'delete_hook_action'
		),
		array(
		    'class'     => 'Postmaster_mcp',
		    'method'    => 'duplicate_hook_action'
		),
		array(
		    'class'     => 'Postmaster_mcp',
		    'method'    => 'create_parcel_action'
		),
		array(
		    'class'     => 'Postmaster_mcp',
		    'method'    => 'edit_parcel_action'
		),
		array(
		    'class'     => 'Postmaster_mcp',
		    'method'    => 'delete_parcel_action'
		),
		array(
		    'class'     => 'Postmaster_mcp',
		    'method'    => 'duplicate_parcel_action'
		),
		array(
		    'class'     => 'Postmaster_mcp',
		    'method'    => 'parser'
		),
		array(
		    'class'     => 'Postmaster_mcp',
		    'method'    => 'send_email'
		),
		array(
		    'class'     => 'Postmaster_mcp',
		    'method'    => 'blacklist'
		),
		array(
		    'class'     => 'Postmaster_mcp',
		    'method'    => 'unsubscribe'
		),
		array(
		    'class'     => 'Postmaster_mcp',
		    'method'    => 'call'
		),
		array(
		    'class'     => 'Postmaster_mcp',
		    'method'    => 'template'
		),
		array(
		    'class'     => 'Postmaster_mcp',
		    'method'    => 'trigger_hook'
		),
		array(
		    'class'     => 'Postmaster_mcp',
		    'method'    => 'create_notification_action'
		),
		array(
		    'class'     => 'Postmaster_mcp',
		    'method'    => 'edit_notification_action'
		),
		array(
		    'class'     => 'Postmaster_mcp',
		    'method'    => 'duplicate_notification_action'
		),
		array(
		    'class'     => 'Postmaster_mcp',
		    'method'    => 'notification_action'
		),
		array(
		    'class'     => 'Postmaster_ext',
		    'method'    => 'route_hook'
		),
		array(
		    'class'     => 'Postmaster_mcp',
		    'method'    => 'create_task_action'
		),
		array(
		    'class'     => 'Postmaster_mcp',
		    'method'    => 'edit_task_action'
		),
		array(
		    'class'     => 'Postmaster_mcp',
		    'method'    => 'duplicate_task_action'
		),
		array(
		    'class'     => 'Postmaster_mcp',
		    'method'    => 'task_action'
		)
	);
	
	private $hooks = array(
		array('entry_submission_ready', 'entry_submission_ready'),
		array('entry_submission_end', 'entry_submission_end', '', 1)
	);
	
    public function __construct()
    {
        // Make a local reference to the ExpressionEngine super object
        $this->EE =& get_instance();
        
        $this->mod_name 	= str_replace('_upd', '', __CLASS__);
        $this->ext_name		= $this->mod_name . '_ext';
        $this->mcp_name		= $this->mod_name . '_mcp';
    }
	
	public function install()
	{	
		$this->EE->load->library('postmaster_installer');
		$this->EE->load->dbforge();
		
		//create tables from $this->tables array
		$this->EE->load->library('Data_forge');		
		$this->EE->data_forge->update_tables($this->tables);
		
		$data = array(
	        'module_name' => $this->mod_name,
	        'module_version' => $this->version,
	        'has_cp_backend' => 'y',
	        'has_publish_fields' => 'n'
	    );
	    	
	    $this->EE->db->insert('modules', $data);
	    	    	    
		foreach ($this->hooks as $row)
		{
			$this->EE->db->insert(
				'extensions',
				array(
					'class' 	=> $this->ext_name,
					'method' 	=> $row[0],
					'hook' 		=> ( ! isset($row[1])) ? $row[0] : $row[1],
					'settings' 	=> ( ! isset($row[2])) ? '' : $row[2],
					'priority' 	=> ( ! isset($row[3])) ? 10 : $row[3],
					'version' 	=> $this->version,
					'enabled' 	=> 'y',
				)
			);
		}
		
		foreach($this->actions as $action)
			$this->EE->db->insert('actions', $action);
		
		$this->_set_defaults();
			
		$this->EE->postmaster_installer->install();
		
		return TRUE;
	}
	
	public function update($current = '')
	{
		require_once 'libraries/Data_forge.php';
	
		$this->EE->data_forge = new Data_forge();
		$this->EE->data_forge->update_tables($this->tables);

		$this->EE->load->add_package_path(PATH_THIRD . 'postmaster');
		$this->EE->load->library('postmaster_installer');
		
		foreach($this->actions as $action)
		{
			$this->EE->postmaster_installer->install_action($action['class'], $action['method']);
		}
		
		foreach($this->hooks as $row)
		{
			$hook     = !isset($row[1]) ? $row[0] : $row[1];
			$priority = !isset($row[3]) ? 10 : $row[3];
			$settings = !isset($row[2]) ? '' : $row[2];
			
			$this->EE->postmaster_installer->install_hook($this->ext_name, $row[0], $hook, $priority, $settings);
		}
		
		$this->EE->postmaster_installer->version_update($current);
		$this->EE->postmaster_installer->update($current);
		
		$hooks = $this->EE->postmaster_model->get_hooks();
		
		if($hooks->num_rows() > 0)
		{
			$this->EE->load->library('postmaster_hook', array(
				'base_path' => PATH_THIRD.'postmaster/hooks/'
			));
		
			$this->EE->postmaster_hook->set_base_path(PATH_THIRD . 'postmaster/hooks/');
		
			foreach($hooks->result_array() as $hook)
			{
				$obj = $this->EE->postmaster_hook->get_hook(!empty($hook['installed_hook']) ? $hook['installed_hook'] : $hook['user_defined_hook']);
				
				$this->EE->db->where('id', $hook['id']);
				$this->EE->db->update('postmaster_hooks', array(
					'actual_hook_name' => $obj->get_hook()
				));
			}
		}
		
	    return TRUE;
	}
	
	public function uninstall()
	{
		$this->EE->load->dbforge();
		
		$this->EE->load->library('postmaster_installer');
		$this->EE->postmaster_installer->uninstall();
		
		$this->EE->db->delete('modules', array('module_name' => $this->mod_name));
		$this->EE->db->delete('extensions', array('class' => $this->ext_name));		
		$this->EE->db->delete('actions', array('class' => $this->mod_name));
		
		$this->EE->db->delete('actions', array('class' => $this->mod_name));
		$this->EE->db->delete('actions', array('class' => $this->mcp_name));
		
		foreach(array_keys($this->tables) as $table)
		{
			$this->EE->dbforge->drop_table($table);
		}
			
		return TRUE;
	}
	
	private function _set_defaults()
	{ 
		$text_editor = array(
			array(
				'key' 	=> 'value',
				'value'	=> ''
			),
			array(
				'key' 	=> 'interval',
				'value'	=> '500'
			),
			array(
				'key' 	=> 'mode',
				'value'	=> 'htmlmixed'
			),
			array(
				'key' 	=> 'theme',
				'value'	=> 'rubyblue'
			),
			array(
				'key' 	=> 'indentUnit',
				'value'	=> 2
			),
			array(
				'key' 	=> 'smartUnit',
				'value'	=> 'true'
			),
			array(
				'key' 	=> 'tabSize',
				'value'	=> 4
			),
			array(
				'key' 	=> 'indentWithTabs',
				'value'	=> 'false'
			),
			array(
				'key' 	=> 'electricChars',
				'value'	=> 'true'
			),
			array(
				'key' 	=> 'autoClearEmptyLines',
				'value'	=> 'false'
			),
			array(
				'key' 	=> 'keyMap',
				'value'	=> 'default'
			),
			array(
				'key' 	=> 'lineWrapping',
				'value'	=> 'true'
			),
			array(
				'key' 	=> 'lineNumbers',
				'value'	=> 'true'
			),
			array(
				'key' 	=> 'firstLineNumber',
				'value'	=> 1
			),
			array(
				'key' 	=> 'gutter',
				'value'	=> 'true'
			),
			array(
				'key' 	=> 'fixedGutter',
				'value'	=> 'false'
			),
			array(
				'key' 	=> 'matchBrackets',
				'value'	=> 'true'
			),
			array(
				'key' 	=> 'pollInterval',
				'value'	=> 100
			),
			array(
				'key' 	=> 'undoDepth',
				'value'	=> 40
			),
			array(
				'key' 	=> 'height',
				'value'	=> '500px'
			)
		);

		foreach($text_editor as $row)
		{
			$this->EE->db->insert('postmaster_editor_settings', $row);
		}
	}
}

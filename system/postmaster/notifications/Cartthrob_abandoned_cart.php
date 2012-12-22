<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cartthrob_abandoned_cart_postmaster_notification extends Base_notification {
	
	
	/**
	 * Title
	 * 
	 * @var string
	 */
	 	
	public $title = 'CartThrob Abandoned Cart';
	
	
	/**
	 * Default Settings Field Schema
	 * 
	 * @var string
	 */
	 		 	 
	protected $fields = array();
	
	
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
		'postmaster_cartthrob_cart_emails' 	=> array(
			'id'	=> array(
				'type'				=> 'int',
				'constraint'		=> 100,
				'primary_key'		=> TRUE,
				'auto_increment'	=> TRUE
			),
			'emails_sent' => array(
				'type'			=> 'int',
				'constraint' 	=> 100
			)
		)
	);
	
	 	
	public function __construct($params = array())
	{
		parent::__construct($params);
	}
	
	/*
	public function display_settings($data = array())
	{
		if(count($this->fields) == 0)
		{		
			return FALSE;
		}
		
		$settings = isset($data->{$this->name}) ? $data->{$this->name} : $this->get_default_settings();
		
		$this->IB->set_var_name($this->name);
		$this->IB->set_prefix('setting');
		$this->IB->set_use_array(TRUE);
				
		return $this->IB->table($this->fields, $settings, postmaster_table_attr());
	}
	*/
	
	public function display_settings($data = array())
	{
		$settings = isset($data->{$this->name}) ? $data->{$this->name} : $this->get_default_settings();
		
		$this->IB->set_var_name($this->name);
		$this->IB->set_prefix('setting');
		$this->IB->set_use_array(TRUE);
	
		$field =  array(
			'label' => 'Email Intervals',
			'id'	=> 'email_intervals',
			'type'	=> 'matrix',
			'description' => 'Define as many intervals as you like. If the abandoned cart is older than the defined interval, an email will be sent.',
			'settings' => array(
				'columns' => array(
					array(
						'name'  => 'name',
						'title' => 'Name'
					)
				),
				'attributes' => postmaster_table_attr()
			)
		);
		
		$field = $this->IB->load('email_intervals', $this->IB->convert_array($field));
		
		return $field->display_field($settings);
	}
	
	public function install()
	{		
		$this->EE->data_forge->update_tables($this->tables);
	}
	
	public function update($current)
	{		
		$this->EE->data_forge->update_tables($this->tables);
	}
}
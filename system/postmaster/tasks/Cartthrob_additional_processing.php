<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cartthrob_additional_processing_postmaster_task extends Base_task {
	
	protected $title = 'CartThrob Additional Processing';
	
	protected $hooks = array(
		array(
			'hook'   => 'postmaster_cartthrob_processing',
			'method' => 'postmaster_cartthrob_processing',
			'priority' => 1
		)
	);
		 	 
	protected $fields = array(
		
		'payment_gateway' => array(
			'label' => 'Payment Gateway',
			'id'	=> 'payment_gateway',
			'description' => 'If this setting is defined, you will be able to update the order with a new status after the email has been sent and the order has been processed.'
		),

		'order_status' => array(
			'label' => 'Order Status',
			'id'	=> 'order_status',
			'description' => 'If a payment gateway setting is defined, you must define a status here. If no status is defined, the order will not be updated.'
		)
	);

	protected $default_settings = array(
		'payment_gateway' => '',
		'order_status'    => ''
	);
		
	public function __construct()
	{
		parent::__construct();
		
	}
	
	public function postmaster_cartthrob_processing($order)
	{
		$settings = $this->get_settings();

		if(!empty($settings->payment_gateway) && !empty($settings->order_status))
		{
			if($settings->payment_gateway == $order['payment_gateway'])
			{
				$this->EE->db->where('entry_id', $order['order_id']);
				$this->EE->db->update('channel_titles', array(
					'status' => $settings->order_status
				));
			}
		}
	}
}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once PATH_THIRD . 'postmaster/libraries/Postmaster_time.php';

if(!class_exists('Base_notification'))
{
	require_once PATH_THIRD . 'postmaster/libraries/Base_notification.php';
}

class Cartthrob_abandoned_cart_postmaster_notification extends Base_notification {
	
	
	/**
	 * Title
	 * 
	 * @var string
	 */
	 	
	public $title = 'CartThrob Abandoned Cart';
	
	/**
	 * Description
	 * 
	 * @var string
	 */
	 	
	public $description = 'This notification will allow you to send emails to the people with abandoned carts in CartThrob at specific intervals.';
	
	
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
		'postmaster_cartthrob_emails' 	=> array(
			'cart_id'	=> array(
				'type'				=> 'int',
				'constraint'		=> 100,
				'primary_key'		=> TRUE,
				'auto_increment'	=> TRUE
			),
			'emails_sent' => array(
				'type'	=> 'text'
			)
		)
	);
	
	 	
	public function __construct($params = array())
	{
		parent::__construct($params);
		
		$this->EE->load->library('encrypt');
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
		$settings = $this->get_settings();
		
		$field = array(
			'label' => 'Email Intervals',
			'id'	=> 'email_intervals',
			'type'	=> 'matrix',
			'description' => 'Define as many intervals as you like. If the abandoned cart is older than the defined interval, an email will be sent.',
			'settings' => array(
				'columns' => array(
					array(
						'name'  => 'weeks',
						'title' => 'Weeks'
					),
					array(
						'name'  => 'days',
						'title' => 'Days'
					),
					array(
						'name'  => 'hours',
						'title' => 'Hours'
					),
					array(
						'name'  => 'minutes',
						'title' => 'Minutes'
					),
				),
				'attributes' => postmaster_table_attr()
			)
		);
				
		return InterfaceBuilder::field('email_intervals', $field, $settings, array(
			'dataArray' => TRUE,
			'varName'   => 'setting[cartthrob_abandoned_cart]'
		))->display_field();
	}
	
	public function send()
	{
		$response = FALSE;
		
		$entries = $this->channel_data->get('cartthrob_cart', array(
			'left join' => array(
				'postmaster_cartthrob_emails' => 'cartthrob_cart.id = postmaster_cartthrob_emails.cart_id'
			)
		));
		
		if($entries->num_rows() > 0)
		{
			$entry     = $entries->row();
			$intervals = $this->settings->cartthrob_abandoned_cart->email_intervals;
			
			if ($entries)
			{
				$this->EE->load->library('encrypt');
				
				$cart = $this->_unserialize($entry->cart);
				
				$this->EE->load->add_package_path(PATH_THIRD . 'cartthrob');				
		 		$this->EE->load->model('cart_model'); 
		 		
				$data = $this->EE->cart_model->read_cart($entry->id);
				
				include_once PATH_THIRD.'cartthrob/cartthrob/Cartthrob.php';
				
				$this->EE->cartthrob = Cartthrob_core::instance('ee', array(
					'cart' => $data,
				));
				
				$sent = json_decode($entry->emails_sent);
				
				if(!is_array($sent))
				{
					$sent = array();
				}
				
				foreach($intervals as $index => $interval)
				{
					$time = new Postmaster_time($this->EE->localize->now, $interval);
				
					$interval_string = json_encode($interval);
					
					$items = $this->EE->cartthrob->cart->items();
					
					if(count($items) > 0 && $time->has_time_past($entry->timestamp) && !in_array($interval_string, $sent))
					{	
						$parse_vars = array();
						
						foreach($items as $index => $item)
						{
							$parse_vars['items'][$index] = array(
								'product_id'   => $item->product_id(),
								'row_id'       => $item->row_id(),
								'in_stock'     => $item->in_stock(),
								'item_options' => array($item->item_options()),
								'price'        => $item->price(),
								'tax'          => $item->tax(),
								'weight'       => $item->weight(),
								'shipping'     => $item->shipping(),
								'meta'		   => array($item->meta())
							);
							
							if(isset($parse_vars['items'][$index]['meta'][0]['subscription_options']))
							{
								$parse_vars['items'][$index]['meta'][0]['subscription_options'] = array(
									$parse_vars['items'][$index]['meta'][0]['subscription_options']
								);
							}
						}
						
						$parse_vars['customer_info'] = array(
							$this->EE->cartthrob->cart->customer_info()
						);
						
						$parse_vars = array_merge($parse_vars, array(
							'subtotal'           => $this->EE->cartthrob->cart->subtotal(),
							'total'              => $this->EE->cartthrob->cart->total(),
							'shipping'           => $this->EE->cartthrob->cart->shipping(),
							'tax'                => $this->EE->cartthrob->cart->tax(),
							'discount'           => $this->EE->cartthrob->cart->discount(),
							'item_tax'           => $this->EE->cartthrob->cart->item_tax(),
							'subtotal_with_tax'  => $this->EE->cartthrob->cart->subtotal_with_tax(),
							'shipping_tax'       => $this->EE->cartthrob->cart->shipping_tax(),
							'shipping_plus_tax'  => $this->EE->cartthrob->cart->shipping_plus_tax(),
							'discount_tax'       => $this->EE->cartthrob->cart->discount_tax(),
							'weight'             => $this->EE->cartthrob->cart->weight(),
							'product_ids'        => implode($this->EE->cartthrob->cart->product_ids(), '|'),
							'shippable_subtotal' => $this->EE->cartthrob->cart->shippable_subtotal(),
							'shippable_weight'   => $this->EE->cartthrob->cart->shippable_weight()
						));
						
						$response = parent::send($parse_vars);
										
						$sent[] = $interval_string;
						$data   = array(
							'emails_sent' => json_encode($sent)	
						);
						
						if(!$this->_existing_entry($entry->id))
						{	
							$this->_insert_entry($entry->id, $data);

						}
						else
						{
							$this->_update_entry($entry->id, $data);
						}
						
						break;
					}
				}
	 		}
		}
		
		return $response;
	}
		
	/**
	 * Clear the email history for the current cart_id
	 *
	 * @access	public
	 * @return	void
	 */
	
	public function clear_cart_emails()
	{
		$this->EE->db->where('cart_id', $this->EE->cartthrob->cart->id());
		$this->EE->db->delete('postmaster_cartthrob_emails');
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
		
		$this->EE->load->model('postmaster_routes_model');
		
		$class = 'Cartthrob_abandoned_cart_postmaster_notification';
		$file  = 'notifications/Cartthrob_abandoned_cart.php';
		
		$this->EE->postmaster_installer->install_hook('Postmaster_ext', 'route_hook', 'cartthrob_on_authorize', 1);
		$this->EE->postmaster_routes_model->create($class, 'clear_cart_emails', 'cartthrob_on_authorize', $file);
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
		$this->EE->db->where('cart_id', $id);
		$this->EE->db->update('postmaster_cartthrob_emails', $data);
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
		$data['cart_id'] = $id;
		
		$this->EE->db->insert('postmaster_cartthrob_emails', $data);
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
		$this->EE->db->where('cart_id', $id);
		
		$data = $this->EE->db->get('postmaster_cartthrob_emails');
		
		if($data->num_rows() == 0)
		{
			return FALSE;
		}
		
		return $data;
	}
}
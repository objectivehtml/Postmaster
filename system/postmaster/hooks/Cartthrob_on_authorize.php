<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cartthrob_on_authorize_postmaster_hook extends Base_hook {
	
	protected $title = 'CartThrob on Authorize';
	
	protected $cart;

	protected $order;
	 		 
	public function __construct()
	{
		parent::__construct();
		
		if(isset($this->EE->cartthrob))
		{
			$this->cart = $this->EE->cartthrob->cart;
		}
	}
		
	public function trigger()
	{
		$this->order = $parse_vars = $this->cart->order();
		
		foreach($parse_vars['items'] as $index => $item)
		{
			$meta = array(
				array(
					'subscription_options' => array(
						isset($item['meta']['subscription_options']) ? $item['meta']['subscription_options'] : array()
					),
					'subscription' => isset($item['meta']['subscription']) ? $item['meta']['subscription'] : FALSE
				)
			);
			
			$parse_vars['items'][$index]['item_options'] = array($parse_vars['items'][$index]['item_options']);

			$parse_vars['items'][$index]['meta'] = $meta;
		}
		
		$parse_vars['purchased_items'] = implode('|', $parse_vars['purchased_items']);
		$parse_vars['auth'] = array(
			$parse_vars['auth']
		);

		$obj = parent::send($parse_vars);

		if($obj->response->status)
		{
			if ($this->EE->extensions->active_hook('postmaster_cartthrob_processing') === TRUE)
			{
				$this->EE->extensions->call('postmaster_cartthrob_processing', $this->order);
			}
		}

		return $obj;
	}
	
	public function post_process($vars = array())
	{
		$responses = $this->responses;

		// var_dump($responses);exit();
		
		foreach($responses as $obj)
		{
			if($obj->response->status)
			{	
				if($this->end_script($responses))
				{
					$this->EE->load->add_package_path(PATH_THIRD . 'cartthrob');
					$this->EE->load->model('discount_model');

					$this->EE->cartthrob->process_discounts()->process_inventory();
					
					$this->cart->clear()
						   ->clear_coupon_codes()
						   ->clear_totals();
					
					$this->cart->set_customer_info('use_billing_info', '0');
					$this->EE->form_builder->set_return($this->cart->order('authorized_redirect'));

					$this->cart->save();
					
					$this->EE->form_builder->action_complete();
				}
			}
		}

		return $responses;
	}
}
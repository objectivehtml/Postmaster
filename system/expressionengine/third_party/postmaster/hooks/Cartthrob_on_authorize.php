<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cartthrob_on_authorize_postmaster_hook extends Base_hook {
	
	protected $title = 'CartThrob on Authorize';
	
	protected $cart;
	
	public function __construct()
	{
		parent::__construct();
		
		if(isset($this->EE->cartthrob))
		{
			$this->cart = $this->EE->cartthrob->cart;
		}
	}
	
	public function trigger($vars = array())
	{
		$parse_vars = $this->cart->order();
		$parse_vars['purchased_items'] = implode('|', $parse_vars['purchased_items']);
		$parse_vars['auth'] = array(
			$parse_vars['auth']
		);
		
		return parent::trigger($parse_vars, NULL);
	}
	
	public function post_process($responses = array())
	{
		// If end_script is TRUE, finish processing the order (taken directly from mod.cartthrob.php)
		if($this->end_script($responses))
		{
			$this->EE->cartthrob->process_discounts()->process_inventory();
			
			$this->cart->clear()
				   ->clear_coupon_codes()
				   ->clear_totals();
			
			$this->cart->set_customer_info('use_billing_info', '0');
			$this->EE->form_builder->set_return($this->cart->order('authorized_redirect'));

			$this->cart->save();
			
			$this->EE->form_builder->action_complete();
		}
		
		return $responses;
	}
}
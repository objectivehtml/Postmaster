<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cartthrob_on_processing_postmaster_hook extends Cartthrob_on_authorize_postmaster_hook {
	
	protected $title = 'CartThrob on Processing';
	
	protected $cart;

	protected $order;
	 
	public function post_process()
	{
		$responses = $this->responses;

		foreach($responses as $obj)
		{
			if($obj->response->status)
			{	
				if($this->end_script($responses))
				{
					$this->EE->cartthrob->cart->clear()
								  ->clear_coupon_codes()
								  ->clear_totals();

					// turning this off for next order
					$this->EE->cartthrob->cart->set_customer_info('use_billing_info', '0');
				}
			}
		}

		return $responses;
	}
}
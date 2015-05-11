<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cartthrob_subscription_created_postmaster_hook extends Base_hook {
	
	protected $title = 'CartThrob Subscription Created';
	
	protected $cart;
	
	public function __construct()
	{
		parent::__construct();
		
		if(isset($this->EE->cartthrob))
		{
			$this->cart = $this->EE->cartthrob->cart;
		}
	}
	
	public function trigger($subscription_id, $sub_data, $item, $sub_permissions)
	{		
		$vars = array(
			'subscription_id' => $subscription_id
		);
		
		if($sub_permissions)
		{
			$vars['sub_permissions'] = $sub_permissions;
		}
		
		$vars   = array_merge($vars, $sub_data, $item);
		
		$vars['meta'] = array($vars['meta']);
		$vars['meta'][0]['subscription_options'] = array($vars['meta'][0]['subscription_options']);
		
		$member = $this->EE->postmaster_model->get_member($vars['member_id'], 'member');
		
		return parent::send($vars, $member);
	}
	
	public function post_process($vars = array())
	{
		$responses = $this->responses;
		
		// If end_script is TRUE, finish processing the order (taken directly from mod.cartthrob.php)
		if($this->end_script($responses))
		{
			$update = array('status' => 'hold');
			
			if ($error_message)
			{
				$update['error_message'] = $error_message;
			}
			
			if ($increment_rebill_attempts)
			{
				$update['rebill_attempts'] = $subscription['rebill_attempts'] + 1;
			}
			
			$this->EE->subscription_model->update($update, $subscription['id']);
		}
		
		return $responses;
	}
}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Store_order_complete_end_postmaster_hook extends Base_hook {
	
	protected $title = 'Store Order Complete End';
	
	public function __construct()
	{
		parent::__construct();
		
	}
		
	public function trigger($order)
	{
		$member = $this->channel_data->get_member($order['member_id'])->row_array();

		foreach($order['items'] as $index => $item)
		{
			if(isset($item['modifiers']))
			{
				$order['items'][$index]['modifiers'] = $this->channel_data->utility->add_prefix('modifier', $item['modifiers']);
			}
		}

		$order['items'] = $this->channel_data->utility->add_prefix('item', $order['items']);
		
		return parent::send($order, $member);
	}
}
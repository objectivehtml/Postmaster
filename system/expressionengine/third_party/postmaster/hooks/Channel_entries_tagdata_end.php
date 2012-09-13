<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Channel_entries_tagdata_end_postmaster_hook extends Base_hook {
	
	protected $title = 'Channel Entries Tagdata End';
	
	public function __construct()
	{
		parent::__construct();
		
	}
		
	public function trigger($tagdata, $row, $obj)
	{
		if(preg_match('/^(true|t|y|yes|1)$/', $this->EE->TMPL->fetch_param('email')))
		{		
			$entry = $this->channel_data->get_channel_entry($row['entry_id'])->row_array();
			
			$parse_vars = array_merge(array(
				'tagdata' => $tagdata
			), $entry);
			
			return parent::trigger($parse_vars, $tagdata);
		}
		
		return array('return_data' => $tagdata);
	}
}
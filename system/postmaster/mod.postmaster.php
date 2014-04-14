<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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

require_once PATH_THIRD . 'postmaster/libraries/Postmaster_base_delegate.php';

class Postmaster extends Postmaster_base_delegate {

	public function __construct()
	{
		parent::__construct();
		
		$this->basepath = PATH_THIRD . 'postmaster/delegates/';
		$this->suffix   = '_postmaster_delegate';
	}

	public function mailbox()
	{
		$this->EE->load->model('postmaster_model');

		$where = array();

		foreach($this->EE->TMPL->tagparams as $param => $value)
		{
			if(preg_match('/^where:/', $param))
			{
				$where[preg_replace('/^where:/', '', $param)] = $value;
			}
		}
		
		$entries = $this->EE->postmaster_model->get_mailbox(
			$where, 
			$this->param('limit'),
			$this->param('offset', 0),
			$this->param('order_by', 'date'),
			$this->param('sort', 'asc')
		);

		if(!$entries->num_rows())
		{
			return $this->EE->TMPL->no_results();
		}

		return $this->parse($entries->result_array());
	}
	
	public function trigger()
	{
		$this->EE->load->driver('channel_data');
		
		$entry_id = $this->param('entry_id');
		$entry    = $this->EE->channel_data->get_channel_entry($entry_id);
		$entry    = $entry ? $entry->row_array() : array();
		$tagdata  = $this->EE->TMPL->tagdata;
		
		$entry = array_merge($entry, array(
			'subject' => $this->param('subject')
		));
		
		$tagdata = $this->EE->channel_data->tmpl->parse_string($tagdata, array(), $this->EE->channel_data->utility->add_prefix('hook', $entry),  array(), array(), 'hook:');
		
		// -------------------------------------------
		// 'postmaster_trigger_tag' - 
		// Add a custom postmaster hook to any template
		//
			if ($this->EE->extensions->active_hook('postmaster_trigger_tag') === TRUE)
			{
				$query_result = $this->EE->extensions->call('postmaster_trigger_tag', $tagdata, $entry);
				if ($this->EE->extensions->end_script === TRUE) return;
			}
		//
		// -------------------------------------------	
		
		return $tagdata;
	}

	public function activation_url()
	{
		$member = $this->EE->channel_data->get_members(array(
			'select' => 'authcode',
			'where' => array(
				'members.member_id' => $this->param('member_id', FALSE, FALSE, TRUE)
			)
		));

		$action_id = $this->EE->functions->fetch_action_id('Member', 'activate_member');

		return $this->EE->functions->fetch_site_index(0, 0).QUERY_MARKER.'ACT='.$action_id.'&id='.(!is_array($member->row('authcode')) ? $member->row('authcode') : null);
	}
	
	/**
		* Adds delegate support to previous version of EE
		*
		* @access	public
		* @return	object
	*/
	
	public function delegate()
	{
		$delegate = $this->tag_part(2);
		$method	  = $this->tag_part(3);
		
		$this->EE->TMPL->tagparts[1] = $delegate;
		$this->EE->TMPL->tagparts[2] = $method;
		
		return $this->run($delegate);
	}
}
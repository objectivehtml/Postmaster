<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Solspace_tag_subscriptions_postmaster_hook extends Base_hook {
	
	protected $title = 'Solspace Tag subscriptions';
	
	protected $hook = 'entry_submission_end';
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function trigger($entry_id, $meta, $data)
	{
		var_dump($data);
					exit();
		if($this->EE->session->cache('postmaster', 'entry_trigger')=='new')
		{
			//get tags
			$query = $this->EE->db->select('tag_entries.tag_id, tag_name')
					->from('tag_entries')
					->join('tag_tags', 'tag_entries.tag_id=tag_tags.tag_id', 'left')
					->where('entry_id', $entry_id)
					->get();
			if ($query->num_rows()==0) return;
			$tags = array();
			foreach ($query->result_array() as $row)
			{
				$tags[$row['tag_id']] = $row['tag_name'];
			}
			
			
			$q = $this->EE->db->select('channel_url, comment_url')
					->from('channels')
					->where('channel_id', $meta['channel_id'])
					->get();
			$channel_data = $q->row_array();
			$basepath = ($channel_data['comment_url']!='') ? $channel_data['comment_url'] : $channel_data['channel_url'];
					
			
			//get subscribers
			foreach ($tags as $tag_id=>$tag_name)
			{
				$query = $this->EE->db->select('tag_subscriptions.member_id, screen_name, email')
							->distinct()
							->from('tag_subscriptions')
							->join('members', 'tag_subscriptions.member_id=members.member_id', 'left')
							->where('tag_subscriptions.tag_id', $tag_id)
							->get();
				if ($query->num_rows()==0) continue;
				$data['entry_id'] = $entry_id;
				$data['tag_id'] = $tag_id;
				$data['tag'] = $tag_name;
				$data['entry_id_path'] = $this->EE->functions->create_page_url($basepath, $entry_id);
				$data['url_title_path'] = $data['path'] = $this->EE->functions->create_page_url($basepath, $data['url_title']);
				foreach ($query->result_array() as $row)
				{
					$data = array_merge($data, $row);
					
					parent::send($data);
				}
			}

		}	
	}
	
}
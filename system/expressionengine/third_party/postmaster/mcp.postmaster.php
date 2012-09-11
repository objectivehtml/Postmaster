<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Postmaster
 * 
 * @package		Postmaster
 * @author		Justin Kimbrell
 * @copyright	Copyright (c) 2012, Objective HTML
 * @link 		http://www.objectivehtml.com/postmaster
 * @version		1.1.1
 * @build		20120719
 */

require_once 'libraries/Email_Parcel.php';
require_once 'libraries/Template_Hook.php';
require_once 'config/postmaster_config.php';

class Postmaster_mcp {
	
	public $themes;

	public function __construct()
	{
		$this->EE =& get_instance();
		
		$this->EE->load->library('postmaster_lib');
		$this->EE->load->driver('interface_builder');
		
		if(REQ == 'CP')
		{
			$this->EE->load->library('doctag', array('base_path' => PATH_THIRD.'postmaster/doctags/'));		
			$this->EE->load->library('theme_loader', array(__CLASS__));
			$this->EE->theme_loader->css('postmaster');

			$this->EE->load->driver('channel_data');

			$channels = $this->EE->channel_data->get_channels()->result_array();
			
			$statuses      = array();
			$member_groups = array();
			$categories    = array();
			$fields    	   = array();
			$entries 	   = array();

			foreach($channels as $index => $channel)
			{
				$id = $channel['channel_id'];

				$template = new Email_Parcel();
				
				$template->channel_id = $id;
				$template->channel    = $this->EE->channel_data->get_channel($id);
				$statuses[$id]        = $template->statuses();
				$fields[$id]          = $template->fields();
				$categories[$id]      = $template->category_tree();
				$member_groups[$id]   = $template->member_groups();
				$entries[$id]   	  = $this->EE->channel_data->get_channel_entries($id, array(
					'limit' => 100
				))->result_array();
			}

			$this->EE->cp->add_to_head('
			<script type="text/javascript">
				var Postmaster = {
					channels: '.json_encode($channels).',
					categories: '.json_encode($categories).',
					statuses: '.json_encode($statuses).',
					groups: '.json_encode($member_groups).',
					fields: '.json_encode($fields).',
					entries: '.json_encode($entries).'
				}
			</script>');
		}
	}
	
	public function index()
	{
		$this->EE->theme_loader->javascript('postmaster');
		$this->EE->theme_loader->javascript('qtip');
		$this->EE->theme_loader->css('qtip');
		
		require_once('delegates/Base_Delegate.php');
		
		$delegate = new Base_Delegate();
		
		$results = $this->EE->postmaster_model->get_hooks()->result_array();
		
		foreach($results as $index => $value)
		{
			$results[$index]['edit_url']      = $this->cp_url('hook').'&id='.$value['id'];
			$results[$index]['delete_url']    = $this->cp_url('delete_hook_action').'&id='.$value['id'];
			$results[$index]['duplicate_url'] = $this->cp_url('duplicate_hook_action').'&id='.$value['id'];
			
			$results[$index] = (object) $results[$index];
		}
		
		$vars = array(
			'theme_url' => $this->EE->theme_loader->theme_url(),
			'themes'  	=> $this->themes,
			'parcels' 	=> $this->EE->postmaster_model->get_parcels(),
			'hooks'     => $results,
			'create_parcel_url' => $this->cp_url('create_template'),
			'add_hook_url' => $this->cp_url('hook'),
			'edit_hook_action' => $this->current_url('ACT', $this->EE->channel_data->get_action_id(__CLASS__, 'edit_hook_action')),
			'delegates'	=> $delegate->get_delegates(FALSE, PATH_THIRD.'postmaster/delegates'),
			'doctag_url' => $this->cp_url('doctag'),
			'ping_url'	=> $this->current_url('ACT', $this->EE->channel_data->get_action_id(__CLASS__, 'send_email')),
			'lang'		=> array(
				'documentation' => lang('postmaster_documentation')
			)
		);
		
		$this->EE->cp->set_variable('cp_page_title', 'Postmaster');
		
		$this->EE->cp->set_right_nav(array(
			'postmaster_documentation'     => $this->cp_url('doctag').'&id=Parcels'
			/* 'Text Editor Settings' => $this->cp_url('editor_settings') */
		));

		return $this->EE->load->view('index', $vars, TRUE);
	}
	
	public function hook()
	{
		$this->EE->theme_loader->javascript('postmaster');
		$this->EE->theme_loader->javascript('codemirror');
		$this->EE->theme_loader->javascript('modes');
		$this->EE->theme_loader->javascript('qtip');
		$this->EE->theme_loader->css('codemirror');
		$this->EE->theme_loader->css('qtip');

    	setcookie('postmaster_parcel_message', '', strtotime('+1 week'), '/');
    	
    	$saved_data = array();
    	
    	if($hook_id = $this->EE->input->get('id'))
    	{
	    	$saved_data = $this->EE->postmaster_model->get_hook($hook_id)->row_array();
	    	$saved_data['settings'] = json_decode($saved_data['settings']);
    	}
    	
		$vars = array(
			'ib_path'  => $this->EE->theme_loader->theme_url().'postmaster/javascript/InterfaceBuilder.js',
			'template' => new Template_Hook($saved_data)
		);
		
		$title = 'New Hook';
		
		if($this->EE->input->get('id'))
		{
			$title = 'Edit Hook';	
		}
		
		$this->EE->cp->set_variable('cp_page_title', $title);
		
		$this->EE->cp->set_right_nav(array(
			'&larr; Back to Home'  => $this->cp_url('index'),
			'Text Editor Settings' => $this->cp_url('editor_settings'),
		));
		
		return $this->EE->load->view('hook', $vars, TRUE);
	}
	
	public function doctag()
	{
		$this->EE->cp->set_variable('cp_page_title', 'Documentation');
		$this->EE->cp->set_right_nav(array(
			'&larr; Back to Home' => $this->cp_url('index')
		));
		
		return $this->EE->doctag->page($this->EE->input->get('id'));
	}

	public function parser()
	{
		$this->EE->load->library('postmaster_lib');
		$this->EE->load->driver('channel_data');

		if($message = $this->get('message'))
		{			
			$message = $this->EE->postmaster_model->save_preview($message);
		}
		else
		{
			$message = $this->EE->postmaster_model->get_preview()->row('data');
		}
		
		$parcel = (object) array(
			'to_name'            => $this->get('to_name'),
			'to_email'           => $this->get('to_email'),
			'from_name'          => $this->get('from_name'),
			'from_email'         => $this->get('from_email'),
			'cc'                 => $this->get('cc'),
			'bcc'                => $this->get('bcc'),
			'subject'            => $this->get('subject'),
			'message'            => $message,
			'subject'            => $this->get('subject'),
			'post_date_specific' => $this->get('post_date_specific'),
			'post_date_relative' => $this->get('post_date_relative'),
			'send_every'         => $this->get('send_every'),
			'extra_conditionals' => $this->get('extra_conditionals'),
		);
		
		$entry_id  = $this->get('entry_id');
		$member_id = FALSE;
		
		if(!empty($entry_id))
		{
			$entries = $this->EE->channel_data->get_channel_entry($entry_id);

			$parcel->entry = $entries ? $entries->row() : (object) array();
		}
		else
		{
			$parcel->entry = (object) array();
		}
		
		if(isset($parcel->entry->author_id))
		{
			$member_id = $parcel->entry->author_id;
		}
				
		$parcel_object = $this->EE->postmaster_lib->parse($parcel, $member_id);
		
		if(empty($parcel_object->message)) {
			$parcel_object->message = '
				<h2>Postmaster</h2>
				<h3>Sample Preview</h3>
				<p>Enter some code in the text editor below to generate a live preview. Anything you would expect to be able to use in a standard template, will also work here.</p>';
		}

		exit($parcel_object->message);
	}

	public function delete_hook_action()
	{
		$id  = $this->get('id');
		$url = $this->cp_url('index');

		$this->EE->postmaster_model->delete_hook($id);

		$this->EE->functions->redirect($url);
	}

	public function delete_parcel_action()
	{
		$id  = $this->get('id');
		$url = $this->cp_url($this->EE->input->get('return'));

		$this->EE->postmaster_model->delete_parcel($id);

		$this->EE->functions->redirect($url);
	}

	public function duplicate_hook_action()
	{
		$id  = $this->get('id');
		$url = $this->cp_url('index');

		$this->EE->postmaster_model->duplicate_hook($id);

		$this->EE->functions->redirect($url);
	}

	public function duplicate_parcel_action()
	{
		$id  = $this->get('id');
		$url = $this->cp_url($this->EE->input->get('return'));

		$this->EE->postmaster_model->duplicate_parcel($id);

		$this->EE->functions->redirect($url);
	}

	public function save_editor_settings()
	{
		$this->EE->load->library('postmaster_lib');
		$this->EE->postmaster_model->save_editor_settings($_POST['setting']);

		$this->EE->functions->redirect($_POST['return']);
	}
	
	public function editor_settings()
	{
		$this->EE->cp->set_variable('cp_page_title', 'Text Editor Configuration');
		
		$this->EE->cp->set_right_nav(array(
			'&larr; Back to Home' => $this->cp_url('index'),
			'Create New Template' => $this->cp_url('create_template'),
		));

		$vars = array();

		$settings = $this->EE->postmaster_model->get_editor_settings();

		$options = array(
			'true'  => 'True',
			'false' => 'False' 
		);

		foreach($settings as $key => $value)
		{
			$vars['settings'][$key] = $value;
			$vars['settings'][$key.'_input'] = '<input type="text" name="setting['.$key.']" id="'.$key.'" value="'.$value.'" />';
			$vars['settings'][$key.'_text'] = '<textarea name="setting['.$key.']" id="'.$key.'">'.$value.'</textarea>';
			$vars['settings'][$key.'_bool'] = form_dropdown('setting['.$key.']', $options, $value);
		}

		$options = array();

		foreach($this->EE->postmaster_lib->get_themes() as $theme)
		{
			$options[$theme->value] = $theme->name;
		}

		$vars['settings']['theme_dropdown'] = form_dropdown('setting[theme]', $options, $vars['settings']['theme']);

		$vars['return'] = $this->cp_url('editor_settings');
		$vars['action'] = $this->current_url('ACT', $this->EE->channel_data->get_action_id('Postmaster_mcp', 'save_editor_settings'));

		$vars['json'] 	= $this->EE->postmaster_model->get_editor_settings_json();

		return $this->EE->load->view('editor-settings', $vars, TRUE);
	}

	public function create_template()
	{
		$this->EE->theme_loader->javascript('postmaster');
		$this->EE->theme_loader->javascript('codemirror');
		$this->EE->theme_loader->javascript('modes');
		$this->EE->theme_loader->javascript('qtip');
		$this->EE->theme_loader->css('codemirror');
		$this->EE->theme_loader->css('qtip');

    	setcookie('postmaster_parcel_message', '', strtotime('+1 week'), '/');

		$channels    = $this->EE->channel_data->get_channels()->result();
		$field_data  = array();
		$status_data = array();
		$member_data = array();

		foreach($channels as $channel)
		{	
			$id 	= $channel->channel_id;
			$parcel = new Email_Parcel();

			$field_data[$id]  = $parcel->fields();
			$status_data[$id] = $parcel->statuses();
			$member_data[$id] = $parcel->member_groups();
		}

		$vars = array(
			'ib_path'	    => $this->EE->theme_loader->theme_url().'postmaster/javascript/InterfaceBuilder.js',
			'channels'		=> json_encode($channels),
			'fields'		=> json_encode($field_data),
			'statuses'		=> json_encode($status_data),
			'member_groups' => json_encode($member_data),
		);

		$vars['template'] = new Email_Parcel();

		$this->EE->cp->set_variable('cp_page_title', 'New Parcel');
		
		$this->EE->cp->set_right_nav(array(
			'&larr; Back to Home' => $this->cp_url('index'),
			'Text Editor Settings' => $this->cp_url('editor_settings'),
		));

		return $this->EE->load->view('template', $vars, TRUE);
	}

	public function edit_parcel()
	{
		$this->EE->theme_loader->javascript('postmaster');
		$this->EE->theme_loader->javascript('codemirror');
		$this->EE->theme_loader->javascript('modes');
		$this->EE->theme_loader->javascript('qtip');
		$this->EE->theme_loader->css('postmaster');
		$this->EE->theme_loader->css('codemirror');
		$this->EE->theme_loader->css('qtip');
        

		$channels    = $this->EE->channel_data->get_channels()->result();
		$field_data  = array();
		$status_data = array();
		$member_data = array();
		$categories  = array();

		foreach($channels as $channel)
		{	
			$id 	= $channel->channel_id;
			$parcel = new Email_Parcel();

			$field_data[$id]  = $parcel->fields();
			$status_data[$id] = $parcel->statuses();
			$member_data[$id] = $parcel->member_groups();
			$categories[$id]  = $parcel->category_tree();
		}

		$vars = array(
			'ib_path'	    => $this->EE->theme_loader->theme_url().'postmaster/javascript/InterfaceBuilder.js',
			'channels'		=> json_encode($channels),
			'fields'		=> json_encode($field_data),
			'statuses'		=> json_encode($status_data),
			'member_groups' => json_encode($member_data),
			'categories'	=> json_encode((array)$categories),
		);
		
		$parcel = $this->EE->postmaster_model->get_parcel($this->get('id'));

		$vars['template'] = new Email_Parcel($parcel);

		$this->EE->cp->set_variable('cp_page_title', 'Edit Parcel');
		
		$this->EE->cp->set_right_nav(array(
			'&larr; Back to Home' => $this->cp_url('index'),
			'Text Editor Settings' => $this->cp_url('editor_settings'),
		));

		return $this->EE->load->view('template', $vars, TRUE);
	}

	public function blacklist()
	{
		$this->EE->postmaster_model->blacklist($this->EE->input->get_post('email'));
	}

	
	public function unsubscribe()
	{
		$this->EE->postmaster_model->unsubscribe($this->EE->input->get_post('email'));
	}

	public function create_hook_action()
	{
		$this->EE->load->library('postmaster_lib');
		
		$parcel          = array(
			'title'              => $this->post('title', TRUE),
			'to_name'            => $this->post('to_name', TRUE),
			'to_email'           => $this->post('to_email', TRUE),
			'from_name'          => $this->post('from_name', TRUE),
			'from_email'         => $this->post('from_email', TRUE),
			'reply_to'           => $this->post('reply_to', TRUE),
			'priority'           => $this->post('priority', TRUE),
			'cc'                 => $this->post('cc', TRUE),
			'bcc'                => $this->post('bcc', TRUE),
			'subject'            => $this->post('subject', TRUE),
			'message'            => $this->post('message', TRUE),
			'installed_hook'     => $this->post('installed_hook', TRUE),
			'user_defined_hook'  => $this->post('user_defined_hook', TRUE),
			'post_date_specific' => $this->post('post_date_specific', TRUE),
			'post_date_relative' => $this->post('post_date_relative', TRUE),
			'send_every'         => $this->post('send_every', TRUE),
			'service'            => $this->post('service', TRUE),
			'settings'           => json_encode($this->post('setting', TRUE))
		);

		$this->EE->postmaster_model->create_hook($parcel);

		$this->EE->functions->redirect($this->post('return'));
	}
	
	public function edit_hook_action()
	{
		$this->EE->load->library('postmaster_lib');
		
		$parcel          = array(
			'title'              => $this->post('title', TRUE),
			'to_name'            => $this->post('to_name', TRUE),
			'to_email'           => $this->post('to_email', TRUE),
			'from_name'          => $this->post('from_name', TRUE),
			'from_email'         => $this->post('from_email', TRUE),
			'reply_to'           => $this->post('reply_to', TRUE),
			'priority'           => $this->post('priority', TRUE),
			'cc'                 => $this->post('cc', TRUE),
			'bcc'                => $this->post('bcc', TRUE),
			'subject'            => $this->post('subject', TRUE),
			'message'            => $this->post('message', TRUE),
			'installed_hook'     => $this->post('installed_hook', TRUE),
			'user_defined_hook'  => $this->post('user_defined_hook', TRUE),
			'post_date_specific' => $this->post('post_date_specific', TRUE),
			'post_date_relative' => $this->post('post_date_relative', TRUE),
			'send_every'         => $this->post('send_every', TRUE),
			'service'            => $this->post('service', TRUE),
			'settings'           => json_encode($this->post('setting', TRUE))
		);
		
		$this->EE->postmaster_model->edit_hook($this->EE->input->post('id'), $parcel);

		$this->EE->functions->redirect($this->post('return'));
	}
	
	public function create_parcel_action()
	{
		$this->EE->load->library('postmaster_lib');
		
		$parcel          = array(
			'channel_id'     => $this->post('channel_id', TRUE),
			'title'          => $this->post('title', TRUE),
			'to_name'        => $this->post('to_name', TRUE),
			'to_email'       => $this->post('to_email', TRUE),
			'from_name'      => $this->post('from_name', TRUE),
			'from_email'     => $this->post('from_email', TRUE),
			'reply_to'       => $this->post('reply_to', TRUE),
			'cc'             => $this->post('cc', TRUE),
			'bcc'            => $this->post('bcc', TRUE),
			'categories'     => $this->post('category') ? implode('|', $this->post('category', TRUE)) : NULL,
			'member_groups'  => $this->post('member_group') ? implode('|', $this->post('member_group', TRUE)) : NULL,
			'statuses'       => $this->post('statuses') ? implode('|', $this->post('statuses', TRUE)) : NULL,
			'subject'        => $this->post('subject', TRUE),
			'message'        => $this->post('message', TRUE),
			'trigger'            => is_array($this->post('trigger', TRUE)) ? implode('|', $this->post('trigger', TRUE)) : $this->post('trigger'),
			'post_date_specific' => $this->post('post_date_specific', TRUE),
			'post_date_relative' => $this->post('post_date_relative', TRUE),
			'send_every'         => $this->post('send_every', TRUE),
			'service'            => $this->post('service', TRUE),
			'extra_conditionals' => $this->post('extra_conditionals', TRUE),
			'settings'           => json_encode($this->post('setting', TRUE))
		);

		$this->EE->postmaster_model->create_parcel($parcel);

		$this->EE->functions->redirect($this->post('return'));
	}
	
	public function edit_parcel_action()
	{
		$this->EE->load->library('postmaster_lib');

		//var_dump($_POST['setting']['SendGridConditional']['field_map']);exit();
		
		$parcel          = array(
			'channel_id'         => $this->post('channel_id'),
			'title'              => $this->post('title'),
			'to_name'            => $this->post('to_name'),
			'to_email'           => $this->post('to_email'),
			'from_name'          => $this->post('from_name'),
			'from_email'         => $this->post('from_email'),
			'reply_to'           => $this->post('reply_to'),
			'cc'                 => $this->post('cc'),
			'bcc'                => $this->post('bcc'),
			'categories'         => $this->post('category') ? implode('|', $this->post('category')) : NULL,
			'member_groups'      => $this->post('member_group') ? implode('|', $this->post('member_group')) : NULL,
			'statuses'           => $this->post('statuses') ? implode('|', $this->post('statuses')) : NULL,
			'subject'            => $this->post('subject'),
			'message'            => $this->post('message'),
			'trigger'            => is_array($this->post('trigger')) ? implode('|', $this->post('trigger')) : $this->post('trigger'),
			'post_date_specific' => $this->post('post_date_specific'),
			'post_date_relative' => $this->post('post_date_relative'),
			'send_every'         => $this->post('send_every'),
			'service'            => $this->post('service'),
			'extra_conditionals' => $this->post('extra_conditionals'),
			'settings'           => json_encode($this->post('setting'))
		);

		$this->EE->postmaster_model->edit_parcel($parcel, $this->post('id'));

		$this->EE->functions->redirect($this->post('return'));
	}

	public function send_email()
	{
		require_once APPPATH.'libraries/Template.php';

		$this->EE->TMPL = new EE_Template();

		$queue = $this->EE->postmaster_model->get_email_queue();

		foreach($queue->result() as $row)
		{
			$this->EE->postmaster_lib->send_from_queue($row);
		}
	}

	public function call()
	{
		$params = array();
		$void   = array('service', 'service_method', 'method', 'ACT', 'S', 'D', 'C', 'M', 'module');

		if($this->EE->session->userdata('member_id'))
		{
			$service = $this->get('service', TRUE);
			$method  = $this->get('service_method', TRUE);
		
			foreach(array_merge($_GET, $_POST) as $name => $value)
			{
				if(!in_array($name, $void))
				{
					$params[$name] = $value;
				}
			}

			$service = $this->EE->postmaster_lib->load_service($service);

			if(!method_exists($service, $method))
			{
				show_error('Error: <b>'.$method.'</b> is not a valid method.');
			}

			call_user_func_array(array($service, $method), $params);
		}
	}

	public function template()
	{
		$member_id = FALSE;
		$entry_id  = $this->get('entry_id');
		$parcel_id = $this->get('parcel_id');

		if(!$entry_id && !$parcel_id)
		{
			return;
		}

		$parcel        = $this->EE->postmaster_model->get_parcel($parcel_id);
		$parcel->entry = $this->EE->channel_data->get_channel_entry($entry_id)->row();	

		if(isset($parcel->entry->author_id))
		{
			$member_id = $parcel->entry->author_id;
		}
		
		$parsed_object = $this->EE->postmaster_lib->parse($parcel, $member_id);

		if($this->get('strip_tags'))
		{
			$parsed_object->message = strip_tags($parsed_object->message);
		}

		exit($parsed_object->message);
	}

	private function post($name)
	{
		$return = $this->EE->input->post($name);
		$return = $return !== FALSE ? $return : '';

		return $return;
	}

	private function get($name, $require = FALSE)
	{
		$return = $this->EE->input->get_post($name);

		if($require && !$return)
		{
			show_error('The <b>'.$name.'</b> parameter is required');
		}

		return $return;
	}

	private function cp_url($method = 'index', $useAmp = FALSE)
	{
		$amp  = !$useAmp ? AMP : '&';

		$file = substr(BASE, 0, strpos(BASE, '?'));
		$file = str_replace($file, '', $_SERVER['PHP_SELF']) . BASE;

		$url  = $file .$amp. '&C=addons_modules' .$amp . 'M=show_module_cp' . $amp . 'module=postmaster' . $amp . 'method=' . $method;

		return str_replace(AMP, $amp, $url);
	}
	
	private function current_url($append = '', $value = '')
	{
		$url = (!empty($_SERVER['HTTPS'])) ? 'https://'.$_SERVER['SERVER_NAME'] : 'http://'.$_SERVER['SERVER_NAME'];
		
		if(!empty($append))
			$url .= '?'.$append.'='.$value;
		
		return $url;
	}
	
}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Newsletter Delegate
 * 
 * @package		Delegates
 * @author		Justin Kimbrell
 * @copyright	Copyright (c) 2012, Objective HTML
 * @link 		http://www.objectivehtml.com/
 * @version		0.1.1
 * @build		20120609
 */

class Email_form_postmaster_delegate extends Postmaster_base_delegate {
	
	public $name        = 'Standalone Email Form';
	public $description = 'A simple standalone email form that hooks into Postmaster.';
	
	protected $service;
	
	public function __construct()
	{
		parent::__construct($this->name);
	}
	
	public function open()
	{		
		$this->EE->load->library('base_form');
		$this->EE->load->driver('channel_data');
				
		$hidden_fields = array(
			'postmaster_email_form' => TRUE,
			'form_field' 		    => $this->param('field', FALSE, FALSE, TRUE)
		);
		
		if($this->EE->input->post('postmaster_email_form'))
		{
			$this->EE->base_form->validate();


			if(count($this->EE->base_form->field_errors) == 0)
			{
				$response['errors'] = (array) $this->EE->base_form->errors;
			}
			else
			{
				$response['errors'] = (array) $this->EE->base_form->field_errors;
			}

			if(count($this->EE->base_form->field_errors) == 0)
			{	
				$form_field   = $this->EE->base_form->post('form_field');			
				$form_data    = $this->EE->input->post($form_field);	
				$custom_data  = $this->EE->input->post($this->param('data_field', 'data'));
				$entry_data   = $this->EE->channel_data->get_channel_entry($this->param('entry_id'));
				$global_data  = $this->EE->input->post($this->param('global_field', 'global'));
				
				if($entry_data && $entry_data->num_rows() > 0)
				{
					$entry_data = $entry_data->row();
				}
				else
				{
					$entry_data = array();
				}
				
				if(is_string($form_data))
				{
					$form_data = array($form_data);	
				}
				
				foreach($form_data as $index => $email)
				{
					$data = array();

					if(isset($custom_data[$index]))
					{
						if(is_array($custom_data[$index]))
						{
							$data = $custom_data[$index];
						}
					}

					if($global_data && !is_array($global_data))
					{
						$global_data = array($global_data);
					}

					if($global_data && count($global_data) > 0)
					{
						$data = array_merge($data, $global_data);
					}

					// -------------------------------------------
				    //  'postmaster_email_form_submit' hook
				    //   - Used to send emails with Postmaster
				    //
				        if ($this->EE->extensions->active_hook('postmaster_email_form_submit'))
				        {
				        	$row_data = $this->EE->extensions->call('postmaster_email_form_submit', $email, $entry_data, $data);
				        }
				    //
				    // -------------------------------------------
				}

				if($return = $this->param('return'))
				{
					$this->EE->functions->redirect($return);
				}
			}
		}
		
		return $this->EE->base_form->open($hidden_fields);
	}
}
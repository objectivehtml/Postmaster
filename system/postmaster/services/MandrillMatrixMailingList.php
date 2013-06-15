<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Mandrill
 *
 * Allows you to push email using Madrill's email service.
 *
 * @package		Postmaster
 * @subpackage	Services
 * @author		Justin Kimbrell
 * @copyright	Copyright (c) 2012, Objective HTML
 * @link 		http://www.objectivehtml.com/postmaster
 * @version		1.0.0
 * @build		20120902
 */
 
if(!class_exists('Mandrill_postmaster_service'))
{
	require_once PATH_THIRD . 'postmaster/services/Mandrill.php';
}

class MandrillMatrixMailingList_postmaster_service extends Mandrill_postmaster_service {

	public $name     = 'MandrillMatrixMailingList';
	public $title    = 'Mandrill Matrix Mailing List';
	
	public function __construct()
	{
		$orig_fields = $this->fields;
		$new_fields  = array(
			'matrix_field' => array(
				'label' => 'Matrix Field'
			),
			'first_name_col' => array(
				'label' => 'First Name Column'
			),
			'last_name_col' => array(
				'label' => 'Last Name Column'
			),
			'email_col' => array(
				'label' => 'Email Column'
			),
			'match_col' => array(
				'label' => 'Match Column',
				'description' => 'If you you want send the email based on a value of a column matching a specific value, enter the name of that column here.'
			),
			'match_val' => array(
				'label' => 'Match Value',
				'description' => 'This is the value you want to match in the column specified above.'
			),
		);
		
		$this->fields = array_merge($new_fields, $orig_fields);
				
		parent::__construct();		
	}
	
	public function send($parsed_object, $parcel)
	{
		$settings    = $this->get_settings();
		$field       = $this->EE->channel_data->get_field_by_name($settings->matrix_field);
		$cols        = $this->EE->channel_data->get('matrix_cols');
		$cols		 = $this->EE->channel_data->utility->reindex('col_name', $cols->result());
		
		$match_col = $settings->match_col;
		$match_val = $settings->match_val;
			
		$select = array();
		
		$select_fields = array('first_name', 'last_name', 'email');
		
		foreach($select_fields as $name)
		{
			$column = $settings->{$name.'_col'};
			
			if(!empty($column))
			{
				$select[] = 'col_id_'.$cols[$column]->col_id . ' as \''.$column.'\'';
			}
		}
		
		if(isset($cols[$match_col]))
		{
			$select[] = 'col_id_'.$cols[$match_col]->col_id . ' as \''.$cols[$match_col]->col_name.'\'';
		}
		
		$matrix_data = $this->EE->channel_data->get('matrix_data', array(
			'select' => $select, 
			'where'  => array(
				'site_id'  => config_item('site_id'),
				'entry_id' => $parcel->entry->entry_id,
				'field_id' => $field->row('field_id')
			)
		));
		
		foreach($matrix_data->result() as $row)
		{
			$name = '';
			
			if(isset($row->{$settings->first_name_col}) && !empty($row->{$settings->first_name_col}))
			{
				$name .= $row->{$settings->first_name_col} . ' ';		
			}
			
			if(isset($row->{$settings->last_name_col}) && !empty($row->{$settings->last_name_col}))
			{
				$name .= $row->{$settings->last_name_col} . ' ';		
			}
			
			$name  = trim($name);
			$email = trim($row->{$settings->email_col});
			
			$parsed_object->to_name  = $name;
			$parsed_object->to_email = $email;
						
			if(!isset($row->$match_col) || isset($row->$match_col) && !empty($row->$match_col))
			{
				$response = parent::send($parsed_object, $parcel);
			}
			else
			{
				$response = $this->failed_response();
			}
		}
		
		return $response;
	}
}
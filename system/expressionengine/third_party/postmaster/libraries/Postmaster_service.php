<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once PATH_THIRD.'postmaster/libraries/Postmaster_core.php';

/**
 * Postmaster Service
 * 
 * @package		Postmaster
 * @author		Justin Kimbrell
 * @copyright	Copyright (c) 2012, Objective HTML
 * @link 		http://www.objectivehtml.com/
 * @version		1.0.99
 * @build		20120703
 */

abstract class Postmaster_service extends Postmaster_core {

	public $id;
	
	abstract public function send($parsed_object, $parcel);
	abstract public function default_settings();
	abstract public function display_settings($settings, $obj);
	
	public function get_settings($settings)
	{
		$default_settings = $this->default_settings();
		
		return isset($settings->{$this->name}) ? (object) array_merge((array) $default_settings, (array) $settings->{$this->name}) : $default_settings;
	}

	public function build_table($settings, $fields)
	{	
		$html = '
		<table class="mainTable" cellpadding="0" cellspacing="0">
			<tr>
				<th>Preference</th>
				<th>Setting</th>
			</tr>';

		foreach($fields as $field_name => $field)
		{
			$html .= '<tr>';

			$setting = isset($settings->{$this->name}->$field_name) ? $settings->{$this->name}->$field_name : '';

			if(!isset($field['type']))
			{
				$field['type'] = 'text';
			}

			if($field['type'] == 'select' && isset($field['options']))
			{
				$html .= '
				<td>
					<label for="'.$field['id'].'">'.$field['label'].'</label>
				</td>
				<td>
					<select name="setting['.$this->name.']['.$field_name.']" id="'.$field['id'].'">';

					foreach($field['options'] as $value => $label)
					{
						$selected = $setting == $value ? 'selected="selected"' : '';
						$html .= '<option value="'.$value.'" '.$selected.'>'.$label.'</option>';
					}

					$html .= '
					</select>
				</td>';
			}
			else
			{
				if(!isset($field['options']))
				{
					$html .= '
					<td>
						<label for="'.$field['id'].'">'.$field['label'].'</label>
					</td>
					<td>
						<input type="'.$field['type'].'" name="setting['.$this->name.']['.$field_name.']" value="'.$setting.'" id="'.$field['id'].'" />
					</td>';
				}
				else
				{
					$html .= '
					<td><label>'.$field['label'].'<label></td>
					<td>';

					foreach($field['options'] as $value => $label)
					{
						$checked = $value == $setting ? 'checked="checked"' : NULL;
						$html .= '
						<label><input type="'.$field['type'].'" name="setting['.$this->name.']['.$field_name.']" value="'.$value.'" '.$checked.' /> '.$label.'</label><br>';
					}

					$html .= '
					</td>';
				}
			}

			$html .= '</tr>';
		}

		$html .= '</table>';

		return $html;
	}
}

class Postmaster_Service_Response {

	public  $parcel_id,
			$channel_id,
			$author_id,
			$entry_id,
			$gmt_date,
			$to_name,
			$to_email,
			$from_name,
			$from_email,
			$cc,
			$bcc,
			$service,
			$subject,
			$status,
			$message,
			$parcel;

	public function __construct($data)
	{
		foreach($data as $index => $value)
		{
			$this->set($index, $value);
		}
		
	}

	public function get($name)
	{
		return isset($this->$name) ? $this->$name : FALSE;
	}

	public function set($name, $value)
	{
		if(property_exists(__CLASS__, $name))
		{
			$this->$name = $value;
		}
	}
}
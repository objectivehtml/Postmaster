<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'third_party/postmaster/libraries/Curl.php';
require_once APPPATH.'third_party/postmaster/libraries/Uuid.php';

abstract class Postmaster_service {

	public $name, $id, $description, $now;

	public function __construct()
	{
		$this->EE =& get_instance();

		$this->curl = new Curl();
		$this->uid  = new Uuid();

		$this->now  = $this->EE->localize->now;
	}

	abstract public function send($parsed_object, $parcel);
	abstract public function default_settings();
	abstract public function display_settings($settings, $parcel);

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

	public function call_url($method, $params = array())
	{
		//$base_url = $this->EE->postmaster_lib->cp_url('call');
		$base_url = $this->EE->postmaster_lib->current_url('ACT', $this->EE->channel_data->get_action_id('postmaster_mcp', 'call'));

		$params = array_merge(
			array(
				'service'        => $this->name,
				'service_method' => $method
			),
			$params
		);

		return $base_url . '&' . http_build_query($params);
	}

	public function get_settings($settings)
	{
		return isset($settings->{$this->name}) ? $settings->{$this->name} : $this->default_settings();
	}

	public function json($data)
	{
		header('Content-header: application/json');

		exit(json_encode($data));
	}

	public function show_error($error)
	{
		$this->EE->output->show_user_error('general', '<b>'.$this->name.'</b> - '.$error);
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
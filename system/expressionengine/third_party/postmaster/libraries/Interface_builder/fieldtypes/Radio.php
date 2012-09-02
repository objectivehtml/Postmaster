<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Radio_IBField extends IBFieldtype {

	public function display_field($data = '')
	{
		$html = array();

		foreach($this->settings['options'] as $option_value => $option_name)
		{

			$checked = $data == $option_value ? 'checked="checked"' : NULL;

			$html[] = '<label><input type="radio" name="'.$this->name.'" value="'.$option_value.'" id="'.$this->id.'" '.$checked.' style="margin-right:.5em">'.$option_name.'</label><br>';
		}

		return implode(NULL, $html);
	}

}
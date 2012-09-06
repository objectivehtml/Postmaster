<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Checkbox_IBField extends IBFieldtype {

	public function display_field($data = array())
	{		
		if(is_null($data))
		{
			$data = array();	
		}
		
		$html = array();

		foreach($this->settings['options'] as $option_value => $option_name)
		{
			$checked = in_array($option_value, $data) ? 'checked="checked"' : NULL;

			$html[] = '<label><input type="checkbox" name="'.$this->name.'[]" id="'.$this->id.'" value="'.$option_value.'" '.$checked.' style="margin-right:.5em">'.$option_name.'</label><br>';
		}

		return implode(NULL, $html);
	}

}
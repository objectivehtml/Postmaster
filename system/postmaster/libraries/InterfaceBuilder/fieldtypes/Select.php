<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Select_IBFieldType extends InterfaceBuilderField {

	public function displayField($data = FALSE)
	{
		if($data)
		{
			$this->data = $data;	
		}
		
		$html = array('<select name="'.$this->name.'" id="'.$this->id.'">');

		foreach($this->settings['options'] as $option_value => $option_name)
		{
			$html[]   = '<option value="'.$option_value.'" '.((string) $this->data == (string) $option_value ? 'selected="selected"' : NULL).'>'.$option_name.'</option>';
		}

		$html[] = '</select>';

		return implode(NULL, $html);
	}
}
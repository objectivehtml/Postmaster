<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Checkbox_IBFieldType extends InterfaceBuilderField {

	public function displayField($data = FALSE)
	{
		if($data)
		{
			$this->data = $data;	
		}
				
		if(!$this->data)
		{
			$this->data = array();	
		}
		
		$html = array();
		
		if(isset($this->settings['options']))
		{
			foreach($this->settings['options'] as $option_value => $option_name)
			{
				$checked = in_array($option_value, $data) ? 'checked="checked"' : NULL;
	
				$html[] = '<label><input type="checkbox" name="'.$this->name.'[]" id="'.$this->id.'" value="'.$option_value.'" '.$checked.' style="margin-right:.5em">'.$option_name.'</label><br>';
			}
		}
		
		return implode(NULL, $html);
	}

}
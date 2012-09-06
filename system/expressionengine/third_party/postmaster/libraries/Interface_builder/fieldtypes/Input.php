<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Input_IBField extends IBFieldtype {

	public $input_type;

	public function display_field($data = '')
	{
		if(empty($data))
		{
			$data = $this->default;
		}

		if(isset($this->settings['type']))
		{
			$input_type = $this->settings['type'];
		}
		else
		{
			$input_type = 'text';
		}
		
		return '<input type="'.$input_type.'" name="'.$this->name.'" value="'.$this->form_prep($data).'" id="'.$this->id.'" />';
	}
}
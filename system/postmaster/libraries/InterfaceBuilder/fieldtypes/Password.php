<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Password_IBFieldType extends InterfaceBuilderField {

	public $input_type;

	public function displayField($data = FALSE)
	{
		if($data)
		{
			$this->data = $data;	
		}
		
		if(empty($this->data))
		{
			$this->data = $this->default;
		}

		return '<input type="password" name="'.$this->name.'" value="'.$this->form_prep($this->data).'" id="'.$this->id.'" />';
	}
}
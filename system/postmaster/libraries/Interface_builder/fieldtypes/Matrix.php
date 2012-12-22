<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Matrix_IBField extends IBFieldtype {

	public $input_type;

	public function display_field($data = '')
	{
		$html = array();

		$attribute_array = array();

		if(isset($this->settings['attributes']))
		{
			foreach($this->settings['attributes'] as $name => $value)
			{
				$attribute_array[] = $name.'="'.$value.'"';
			}
		}
		
		$html[] = '<div id="ib-matrix-'.$this->name.'" data-name="'.$this->name.'" class="ib-matrix">';
		$html[] = '<table '.implode(NULL, $attribute_array).' class="ib-field-matrix">';
		$html[] = '<thead><tr><th style="width:1px"></th>';

		foreach($this->settings['columns'] as $column)
		{
			if(is_array($column))
			{
				$name = $column['name'];
				
				if(isset($column['title']))
				{
					$column = $column['title'];
				}
				else
				{
					$column = $column['name'];
				}
			}
			
			$html[] = '<th data-column-name="'.$name.'">'.$column.'</th>';
		}

		$html[] = '<th style="width:40px"></th>';	
		$html[] = '</tr>';
		$html[] = '</thead>';
		$html[] = '<tbody>';

		if(is_array($data))
		{
			foreach($data as $index => $row)
			{
				$html[] = '<tr><td><div class="ib-drag-handle"></div></td>';

				foreach($this->settings['columns'] as $column)
				{
					$data[$index] = (array) $data[$index];

					$html[] = '<td><input type="text" name="'.$this->name.'['.$index.']['.$column['name'].']" value="'.(isset($data[$index][$column['name']]) ? $data[$index][$column['name']] : NULL).'" class="ib-cell" /></td>';
				}
				
				$html[] = '<td><a href="#'.$index.'" class="ib-delete-row">Delete</a></td>';
				$html[] = '</tr>';
			}
		}
		
		$html[] = '</tbody>';
		$html[] = '</table>
			<a href="#" class="ib-add-row">Add Row</a>
		</div>';
		
		return implode(NULL, $html);
	}
}
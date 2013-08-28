<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Matrix_IBFieldType extends InterfaceBuilderField {

	public $input_type;

	public function displayField($data = FALSE)
	{			
		if($data)
		{
			$this->data = $data;
		}
		
		$html = array();

		$attribute_array = array();

		if(isset($this->settings['attributes']))
		{
			foreach($this->settings['attributes'] as $name => $value)
			{
				$attribute_array[] = $name.'="'.$value.'"';
			}
		}

		$html[] = '<div id="ib-matrix-'.$this->getId().'" data-name="'.$this->getName().'" class="ib-matrix">';
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
			
			
			$html[] = '<th data-col="'.$name.'">'.$column.'</th>';
		}

		$html[] = '<th style="width:40px"></th>';	
		$html[] = '</tr>';
		$html[] = '</thead>';
		$html[] = '<tbody>';

		if(is_array($this->data))
		{
			foreach($this->data as $index => $row)
			{
				$html[] = '<tr><td><div class="ib-drag-handle"></div></td>';

				foreach($this->settings['columns'] as $column)
				{
					$this->data[$index] = (array) $this->data[$index];

					$html[] = '<td><input type="text" name="'.$this->name.'['.$index.']['.$column['name'].']" value="'.(isset($this->data[$index][$column['name']]) ? $this->data[$index][$column['name']] : NULL).'" class="ib-cell" /></td>';
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
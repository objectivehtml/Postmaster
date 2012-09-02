<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Select_IBField extends IBFieldtype {

	public function display_field($data = '')
	{
		$html = array();

		if($this->settings['options'] == 'CATEGORY_GROUPS_DROPDOWN')
		{
			$channels = array('' => '--');

			foreach($this->channel_data->get_category_groups(array(
				'order_by' => 'group_id',
				'sort' => 'asc'
			))->result() as $index => $row)
			{
				$channels[$row->group_id] = $row;
			}

			$this->settings['options'] = $this->build_options($channels, 'group_id', 'group_name');
		}

		if($this->settings['options'] == 'CHANNEL_DROPDOWN')
		{
			$channels = array('' => '--');

			foreach($this->channel_data->get_channels(array(
				'order_by' => 'channel_name',
				'sort' => 'asc'
			))->result() as $index => $row)
			{
				$channels[$row->channel_id] = $row;
			}

			$this->settings['options'] = $this->build_options($channels, 'channel_id', 'channel_title');
		}

		if($this->settings['options'] == 'FIELD_DROPDOWN')
		{
			$fields = array('' => '--', 'title' => 'Title');

			foreach($this->channel_data->get_fields(array(
				'order_by' => 'group_id, field_name',
				'sort'     => 'asc, asc'
			))->result() as $row)
			{
				$fields[$row->field_id] = $row;
			}

			$this->settings['options'] = $this->build_options($fields, 'field_id', 'field_label');

		}

		$html[] = '<select name="'.$this->name.'" id="'.$this->id.'">';

		if(!is_array($this->settings))
		{
			var_dump($this->name);exit();
		}
		
		foreach($this->settings['options'] as $option_value => $option_name)
		{
			$html[]   = '<option value="'.$option_value.'" '.((string) $data == (string) $option_value ? 'selected="selected"' : NULL).'>'.$option_name.'</option>';
		}

		$html[] = '</select>';

		return implode(NULL, $html);
	}

	private function build_options($options, $index_field, $value_field)
	{
		$dropdown = array();

		foreach($options as $index => $option_value)
		{
			if(is_object($option_value))
			{
				$option = (array) $option_value;
			}
			else
			{
				$option = array();
			}
			
			if(!isset($option[$index_field]))
			{
				$option[$index_field] = $index;
			}

			if(!isset($option[$value_field]))
			{
				$option[$value_field] = $option_value;
			}

			$dropdown[$option[$index_field]] = $option[$value_field];
		}

		return $dropdown;
	}

}
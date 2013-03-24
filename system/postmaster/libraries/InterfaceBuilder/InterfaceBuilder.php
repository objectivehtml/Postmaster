<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Interface Builder v0.9.0
 *
 */

if(!class_exists('InterfaceBuilder'))
{
	require_once 'InterfaceBuilderCore.php';
	require_once 'InterfaceBuilderField.php';
	
	class InterfaceBuilder extends InterfaceBuilderCore {				
		
			
		/**
		 * The class suffix is appended to the fieldtype class being loaded
		 *
		 * @access	protected
		 * @type	string
		 */
		 
		protected $classSuffix = '_IBFieldType';
		
		
		/**
		 * The data array to be passed to the fields when loaded
		 *
		 * @access	protected
		 * @type	array
		 */
		 
		protected $data = array();
		
		
		/**
		 * Convert the field output to an array of fields under a single
		 * indexe defined by $var_name
		 *
		 * @access	protected
		 * @type	bool
		 */
		 
		protected $dataArray = FALSE;
		
		
		/**
		 * The debug property is used to debug objects should it be needed
		 *
		 * @access	protected
		 * @return	bool
		 */
		 
		protected $debug = FALSE;
				
				
		/**
		 * The default input type
		 *
		 * @access	protected
		 * @type	string
		 */
		 
		protected $defaultInputType = 'input'; 
		
		
		/**
		 * The base directory path that stores the fieldtype classes
		 *
		 * @access	protected
		 * @return	string
		 */
		 
		protected $directory = 'fieldtypes/';
		
		
		/**
		 * The file extension of each fieldtype class
		 *
		 * @access	protected
		 * @type	string
		 */
		 
		protected $extension = '.php';
				
				
		/**
		 * An associative array of fields.
		 *
		 * @access	protected
		 * @type	array
		 */
		 
		protected $fields = array();
		
		
		/**
		 * Deprecated - Aliased parameter for $data_array
		 *
		 * @access	protected
		 * @type	book
		 */
		 
		protected $use_array  = NULL;
		
		
		/**
		 * The $prefWidth property is used when building tables. The first
		 * column in the table gets assigned this width by default.
		 *
		 * @access	protected
		 * @type	mixed
		 */
		 
		protected $prefWidth = '40%';
		
		
		/**
		 * If $data_array is TRUE, the fields by be assigned to a single
		 * variable name. This parameter stores that name.
		 *
		 * @access	protected
		 * @type	mixed
		 */
		 
		protected $varName = NULL;
		
				
		/**
		 * Constructor
		 *
		 * @access	public
		 * @return	null
		 */
		 			
		public function __construct($properties = array())
		{
			parent::__construct($properties);
			
			// Support the legacy use_array
			
			if(!is_null($this->use_array) && $this->dataArray === FALSE)
			{
				$this->dataArray = $this->use_array;
			}
		}
		
		
		/**
		 * Add a single field to the IB object
		 *
		 * @access	public
		 * @param	array	A standard field array
		 * @return	null
		 */
		
		public function addField($name, $field)
		{	
			if(is_array($field))
			{
				$this->fields[$name] = $this->load($name, $field);
			}
		}
		
		
		/**
		 * Add an array of fields to the IB object
		 *
		 * @access	public
		 * @param	array	A standard field array
		 * @return	null
		*/
		
		public function addFields($fields)
		{
			if(is_array($fields))
			{
				foreach($fields as $name => $field)
				{
					$this->addField($name, $field);
				}
			}
		}
		
		
		/**
		 * Converts an array to HTML attributes
		 *
		 * @access	public
		 * @param	array	An array of HTML attributes
		 * @return	string
		*/
		
		public function attributes($attributes = array())
		{
			$attributeArray = array();
	
			foreach($attributes as $name => $value)
			{
				if(!is_null($value))
				{
					$attributeArray[] = $name.'="'.$value.'"';
				}
			}
	
			return ' '.implode(' ', $attributeArray);
		}
		
		
		/**
		 * Build an HTML table from the fields. The left col is the setting
		 * and the right col is the field.
		 *
		 * @access	public
		 * @param	array	An array of HTML attributes
		 * @param	string	A valid CSS width for the prefs column
		 * @return	string
		*/
		
		public function buildTable($attributes = array())
		{
			$html = array();
	
			$html[] = '<table'.$this->attributes($attributes).'>';
			$html[] = '<tr><th style="width:'.$this->prefWidth.'">Preference</th><th>Setting</th></tr>';
	
			foreach($this->fields as $fieldName => $field)
			{
				$html[] = '
				<tr>
					<td>
						'.$field->displayLabel()
						 .$field->displayDescription().'
					</td>
				    <td>'.$field->displayField().'</td>
				</tr>';
			}
	
			$html[] = '</table>';
	
			return implode(NULL, $html);
		}
		
		
		/**
		 * Build an HTML fieldset from the fields
		 *
		 * @access	public
		 * @param	array	An array of HTML attributes
		 * @param	string	A valid CSS width for the prefs column
		 * @return	string
		*/
		
		public function buildFieldset($legend = FALSE, $attributes = array())
		{
			$html = array();
	
			$html[] = '<fieldset'.$this->attributes($attributes).'>';
			
			if($legend)
			{
				$html[] = '<legend>'.$legend.'</legend>';
			}
			
			$html[] = $this->buildTable();	
			$html[] = '</fielset>';
	
			return implode(NULL, $html);
		}
		
		
		/**
		 * Get a specific field after it has instantiated
		 *
		 * @access	public
		 * @param	array	A standard field array
		 * @return	null
		*/
		
		public function getField($field)
		{
			if(isset($this->fields[$field]))
			{
				return $this->fields[$field];
			}
			
			return FALSE;
		}
		
		
		/**
		 * Instantiate the field object
		 *
		 * @access	public
		 * @param	array	An array of standard field arrays
		 * @return	object
		*/
		
		public function load($name, $field, $data = FALSE)
		{				
			$default_field = array(
				'type' => 'input'
			);
						
			if(is_object($this->data) && isset($this->data->$name))
			{
				$data = $this->data->$name;
			}
			else if(is_array($this->data) && isset($this->data[$name]))
			{
				$data = $this->data[$name];
			}
			else if($data === FALSE)
			{
				$data = NULL;
			}
			
			$field     = (array) array_merge($default_field, $field);
			$className = ucfirst($field['type']) . $this->classSuffix;
			
			if($this->dataArray)
			{
				$name = $this->varName . '[' . $name . ']';
			}
			
			$defaultProperties = array(
				'data'        => $data,
				'name'        => $name,
				'label'       => $name,
				'description' => '',
				'required'    => FALSE,
				'type'        => $this->defaultInputType
			);
			
			$field = array_merge($defaultProperties, $field);
			
			if(!class_exists($className))
			{
				require_once $this->directory . ucfirst($field['type']) . $this->extension;
			}
			
			return new $className($field);
		}
		
		static function attr($attributes)
		{
			$IB = new InterfaceBuilder();
			
			return $IB->attributes($attributes);
		}
		
		static function field($fieldName, $field, $data = array(), $properties = array())
		{
			$IB = new InterfaceBuilder(array_merge($properties, array(
				'data' => $data,
			)));

			$IB->addField($fieldName, $field);
			
			return $IB->getField($fieldName);
		}
		
		static function table($fields, $data, $properties = array(), $attributes = array())
		{
			$IB = new InterfaceBuilder(array_merge($properties, array(
				'data' => $data,
			)));
			
			$IB->addFields($fields);
			
			return $IB->buildTable($attributes);
		}
		
		static function fieldset($fields, $data, $legend = FALSE, $properties = array(), $attributes = array())
		{
			$IB = new InterfaceBuilder(array_merge($properties, array(
				'data' => $data,
			)));
			
			$IB->addFields($fields);
			
			return $IB->buildFieldset($legend, $attributes);
		}
	}
}
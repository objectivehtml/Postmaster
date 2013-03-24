<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if(!class_exists('InterfaceBuilderField'))
{
	abstract class InterfaceBuilderField extends InterfaceBuilderCore {
				
			
		/**
		 * Field data
		 *
		 * @access	protected
		 * @type		
		*/
		
		protected $data;
			
			
		/**
		 * Field Description
		 *
		 * @access	protected
		 * @type	string
		*/
		
		protected $description;
		
		
		/**
		 * Field ID
		 *
		 * @access	protected
		 * @type		
		*/
		
		protected $id;
			
			
		/**
		 * Field Label
		 *
		 * @access	protected
		 * @type	null
		*/
		
		protected $label;
			
			
		/**
		 * Field Name
		 *
		 * @access	protected
		 * @type		
		*/
		
		protected $name;
		
				
		/**
		 * Is the field required?
		 *
		 * @access	protected
		 * @type	bool	
		*/
		
		protected $required = FALSE;
		
				
		/**
		 * Settings array to pass anything you need to each fieldtype
		 *
		 * @access	protected
		 * @type	array	
		*/
		
		protected $settings;
			
				
		/**
		 * Constructor
		 *
		 * @access	protected
		 * @type	string	
		*/
		
		public function __construct($properties = array())
		{
			parent::__construct($properties);
		}
		
				
		/**
		 * Get the field ID
		 *
		 * @access	protected
		 * @return	string	
		*/
		
		public function getId()
		{
			return !empty($this->id) ? $this->id : $this->name;
		}
		
				
		/**
		 * Get the field label
		 *
		 * @access	protected
		 * @return	string	
		*/
		
		public function getLabel()
		{
			return !empty($this->label) ? $this->label : $this->name;
		}
			
				
		/**
		 * Display the field label
		 *
		 * @access	protected
		 * @return	string	
		*/
		
		public function displayLabel()
		{
			return '<label for="'.$this->getId().'">'.$this->getLabel().'</label>';
		}
			
				
		/**
		 * Display the field label
		 *
		 * @access	protected
		 * @return	string	
		*/
		
		public function displayDescription()
		{
			return '<p>'.$this->description.'</p>';
		}
		
		
		/**
		 * Display the fieldtype
		 *
		 * @access	protected
		 * @return	string	
		*/
		
		abstract function displayField($data);
		
		
		/**
		 * Validate the fieldtype
		 *
		 * @access	protected
		 * @return	mixed	
		*/
		
		public function validate()
		{
			return TRUE;
		}
		
		
		/**
		 * Save the fieldtype
		 *
		 * @access	protected
		 * @return	mixed	
		*/
		
		public function save()
		{
			return $this->data;
		}
		
		
		/**
		 * Helper method to prepare the form data
		 *
		 * @access	public
		 * @return	string	
		*/
		
		public function form_prep($str = '', $field_name = '')
		{
			$prepped_fields = array();
	
			// if the field name is an array we do this recursively
			if (is_array($str))
			{
				foreach ($str as $key => $val)
				{
					$str[$key] = form_prep($val);
				}
	
				return $str;
			}
	
			if (empty($str) || isset($prepped_fields[$field_name]))
			{
				return $str;
			}
	
			// In case htmlspecialchars misses these.
			$str = htmlspecialchars(str_replace(array("'", '"'), array("&#39;", "&quot;"), $str));
	
			if ($field_name != '')
			{
				$prepped_fields[$field_name] = $field_name;
			}
	
			return $str;
		}
	}
}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** 
 * Base Delegate
 *  
 * @author		Justin Kimbrell
 * @copyright	Copyright (c) 2012, Objective HTML
 * @link 		http://www.objectivehtml.com/
 * @version		0.1.3
 * @build		20130504
 */

if(!class_exists('Base_delegate'))
{
	class Base_delegate {
		
		public $EE, $suffix = '_delegate', $name;
		
		public $basepath = '../delegates/';
			
		public function __construct($name = FALSE)
		{
			$this->EE =& get_instance();
			
			if(!$name)
			{
				$name = strtolower(str_replace('_delegate', '', ($name ? $name : __CLASS__)));
			}
			
			$this->name	  = $name;
		}
		
		// ------------------------------------------------------------------
	
		/**
			* Magic method that runs the delegate router
			*
			* @access	public
			* @param	string 	Name of the delegate.
			* @param	array	This parameter is required by PHP, but is not used.
			* @return	object;
		*/
	
		public function __call($name, $arguments = array())
		{		
			return $this->run($name, $arguments);		
		}
		
		// ------------------------------------------------------------------
			
		/**
			* Method that handles the delegate routing
			*
			* @access	protected
			* @param	string 		Name of the delegate.
			* @param	array		This parameter is required by PHP, but is not used.
			* @return	object;
		*/
		
		protected function run($name, $arguments = array())
		{
			if(!method_exists($this, $name))
			{
				$delegate = $this->load($name);
				
				$method   = $this->EE->TMPL->tagparts[2];
				$params   = $this->EE->TMPL->tagparams;
			
				if(!method_exists($delegate, $method))
				{
					$this->show_error('\''.$method.'\' is not a valid method in the \''.ucfirst($name).'\' delegate.');
				}
				
				return $delegate->$method($params);
			}
			else
			{
				return call_user_func_array(array($this, $name), $arguments);
			}
		}
		
		// ------------------------------------------------------------------
		
		/**
			* Load the specified delegate
			*
			* @access	protected
			* @param	string The name of the delegate
			* @return	object
		*/
		
		protected function load($name)
		{
			include_once $this->basepath.ucfirst($name).'.php';
			
			$class = $name.$this->suffix;
	
			if(!class_exists($class))
			{
				$this->show_error('\''.ucfirst($name).'\' is not a valid delegate');	
			}	
			
			return new $class;
		}
		
		// ------------------------------------------------------------------
		
		/**
			* Validate the delegate method as valid before it's called.
			*
			* @access	public
			* @param	string	A name of a PHP class
			* @param 	string  A name of a method with the defined class.
			* @return	mixed
		*/
	
		public function validate($class, $method)
		{
			if(!method_exists($class, $method))
			{
				$this->show_error('The <em>'.$method.'</em> method  does not exist in the <em>'.get_class($class).'</em> class.');
			}
			
			return TRUE;
		}
		
		// ------------------------------------------------------------------
		
		/**
			* Get the available delegates
			*
			* @access	public
			* @param	string	If a name is passed, the method will return a single delegate (object).
			*					Otherwise, an array is returned.
			* @return	mixed
		*/
		
		public function get_delegates($name = FALSE, $directory = '../delegates')
		{
			$delegates = array();
			
			foreach(directory_map($directory) as $file)
			{
				if(file_exists($directory.'/'.$file) && $file != 'Base_delegate.php')
				{
					$delegates[] = $this->load(str_replace('.php', '', $file));
				}
			}
			
			if(!$name)
			{
				return $delegates;
			}
			else
			{
				return $delegates[$name];
			}
		}
		
		// ------------------------------------------------------------------
		
		/**
			* Get a single delegate object
			*
			* @access	public
			* @param	string The name of the delegate
			* @return	object
		*/
		
		public function get_delegate($name)
		{
			return $this->get_delegates($name);
		}
		
		// ------------------------------------------------------------------
		
		/**
			* Return a JSON response
			*
			* @access	public
			* @param	mixed 
			* @return	JSON
		*/
		
		public function json($data)
		{
			header('Content-header: application/json');
	
			exit(json_encode($data));
		}
		
		// ------------------------------------------------------------------
	
		/**
			* Show a user error
			*
			* @access	public
			* @param	string	The error string
			* @return	error
		*/
		
		public function show_error($error)
		{
			show_error($error);
		}
		
		// ------------------------------------------------------------------
		
		/**
			* Get the tagparts using the same syntax a parameter
			*
			* @access	protected
			* @param	string		The name of the parameter
			* @param 	mixed		The default parameter value
			* @param 	bool		Should the param return a boolean value?
			* @param 	bool		Is the parameter required?
			* @return	mixed
		*/
		
		protected function tag_part($part, $default = FALSE, $boolean = FALSE)
		{
			if(!isset($this->EE->TMPL->tagparts[(int)$part]))
			{
				$this->show_error('This \''.$part.'\' is not a valid tagpart.');	
			}
			
			$part 	= $this->EE->TMPL->tagparts[$part];
			
			if($part === FALSE && $default !== FALSE)
			{
				$part = $default;
			}
			else
			{				
				if($boolean)
				{
					$part = strtolower($part);
					$part = ($part == 'true' || $part == 'yes') ? TRUE : FALSE;
				}			
			}
			
			return $part;			
		}
			
		// ------------------------------------------------------------------
		
		/**
			* Parse variables in an EE template
			*
			* @access	protected
			* @param	array 	An array of variables to be parsed
			* @param	mixed 	You may pass your own tagdata if desired
			* @return	string
		*/
		
		protected function parse($vars, $tagdata = FALSE)
		{
			if($tagdata === FALSE)
			{
				$tagdata = $this->EE->TMPL->tagdata;
			}
				
			return $this->EE->TMPL->parse_variables($tagdata, $vars);
		}
		
		// ------------------------------------------------------------------
		
		/**
			* Easily fetch parameters from an EE template
			*
			* @access	protected
			* @param	string		The name of the parameter
			* @param 	mixed		The default parameter value
			* @param 	bool		Should the param return a boolean value?
			* @param 	bool		Is the parameter required?
			* @return	mixed
		*/
		
		protected function param($param, $default = FALSE, $boolean = FALSE, $required = FALSE)
		{
			$name 	= $param;
			$param 	= $this->EE->TMPL->fetch_param($param);
			
			if($required && !$param) show_error('You must define a "'.$name.'" parameter.');
				
			if($param === FALSE && $default !== FALSE)
			{
				$param = $default;
			}
			else
			{				
				if($boolean)
				{
					$param = strtolower($param);
					$param = ($param == 'true' || $param == 'yes') ? TRUE : FALSE;
				}			
			}

			// Parse non-cachable variables
			foreach ( $this->EE->session->userdata as $var => $val)
			{
				if(is_string($val))
				{
					$param = str_replace(LD . 'logged_in_' . $var . RD, $val, $param);
				}
			}
			
			return $param;			
		}
	}
}
/* End of file Base_delegate.php */
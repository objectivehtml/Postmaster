<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Base Class
 * 
 * @author		Justin Kimbrell
 * @copyright	Copyright (c) 2012, Objective HTML
 * @link 		http://www.objectivehtml.com/
 * @version		1.0.2
 * @build		20121014
 */

if(!class_exists('Base_class'))
{
	abstract class Base_class {
			
	    /**
	     * Contructor
	     *
	     * @access	public
	     * @param	array 	Pass object properties as array keys to set default values
	     * @return	void
	     */
	   	    	
	    public function __construct($data = array())
	    {
		    foreach($data as $key => $value)
		    {
			    if(property_exists($this, $key))
			    {
				    $this->$key = $value;
			    }
		    }
		    
		    return;
	    }    
	   
	    
	    /**
	     * Dynamic create setter/getter methods
	     *
	     * @access	public
	     * @param	string 	method name to call
	     * @param	array 	arguments in the form of an array
	     * @return	mixed
	     */
		    
		public function __call($method, $args)
		{
			$magic_methods = array(
				'/^get_/'    => 'get_' , 
				'/^set_/'    => 'set_',
				'/^append_/' => 'append_'
			);
			
			foreach($magic_methods as $regex => $replace)
			{
		    	if(preg_match($regex, $method))
		    	{
		    		$property = str_replace($replace, '', $method);
		    		$method = rtrim($replace, '_');
			    }
		    }
		    
		    $args = array_merge(array($property), $args);	    	
		    	
		    return call_user_func_array(array($this, $method), $args);
		}
		
		
		/**
		 * Get the value of a defined property
		 *
		 * @access	public
		 * @param	string 	propery name
		 * @return	mixed
		 */
	       
	    public function get($prop)
	    {
		    if(isset($this->$prop))
		    {
			    return $this->$prop;
		    }
		    
		    return NULL;
	    }
	    
	    
		/**
		 * Set the value of a defined property
		 *
		 * @access	public
		 * @param	string 	propery name
		 * @param	string 	propery value
		 * @return	mixed
		 */
	       
	    public function set($prop, $value)
	    {
		    if(property_exists($this, $prop))
		    {
			    $this->$prop = $value;
		    }
	    }
	    
	    
		/**
		 * Append the value of a defined property
		 *
		 * @access	public
		 * @param	string 	propery name
		 * @param	string 	propery value
		 * @return	mixed
		 */
	       
		protected function append($prop = 'fields', $value)
		{
			if(isset($this->$prop))
			{
				$this->$prop = array_merge($this->{'get_'.$prop}(), $value);
			}
		}
		
	}
}
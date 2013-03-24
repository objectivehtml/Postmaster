<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * InterfaceBuilderCore (Base Class renamed)
 * 
 * @author		Justin Kimbrell
 * @copyright	Copyright (c) 2012, Objective HTML
 * @link 		http://www.objectivehtml.com/
 * @version		1.2.1
 * @build		20121116
 */

if(!class_exists('InterfaceBuilderCore'))
{
	abstract class InterfaceBuilderCore {
			
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
			$orig_method = $method;
			
			$magic_methods = array('get', 'set', 'append');
			
			// Add support for legacy code not supporting PSR-2. 
			// Setters/getters can look like get_method() & set_method()
			
			if(preg_match("/\w*_/us", $method))
			{			
				$newMethod = array();
				
				foreach(explode('_', $method) as $index => $part)
				{
					if($index > 0)
					{
						$part = ucfirst($part);
					}
					
					$newMethod[] = $part;
				}
				
				$method = implode('', $newMethod);
				
				return call_user_func_array(array($this, $method), $args);
			}
			
			foreach($magic_methods as $replace)
			{
				$regex = "/^(".$replace.")([A-Z]\w*)/";
				
		    	if(preg_match($regex, $method))
		    	{	
		    		if(preg_match("/^(get)(Total)(\w*)$/", $method, $matches))
	    			{
	    				if(isset($matches[3]))
	    				{
	    					$method   = 'count';
		    				$property = lcfirst($matches[3]);
	    				}
	    			}
	    			else
	    			{
			    		$method   = preg_replace($regex, '$1 $2', $method);
			    		$method   = explode(' ', $method);
			    		$property = lcfirst($method[1]);
			    		$method   = $method[0];
	    			}		    		
			    }
		    }
		    
		    if(!isset($property))
		    {
				return;    
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
	       
		public function append($prop, $value)
		{
			if(isset($this->$prop))
			{
				$this->$prop = array_merge($this->{'get'.ucfirst($prop)}(), $value);
			}
		}
		
		/**
		 * Count the defined property
		 *
		 * @access	public
		 * @param	string 	propery name
		 * @return	int
		 */
	       
		public function count($prop)
		{
			if(isset($this->$prop))
			{
				return count($this->$prop);
			}
			
			return 0;
		}	
	}
}

if(function_exists('lcfirst') === false) {
    function lcfirst($str) {
        $str[0] = strtolower($str[0]);
        return $str;
    }
}
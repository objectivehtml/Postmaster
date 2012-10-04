<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Interface Builder Core (Base Class re-named)
 * 
 * @author		Justin Kimbrell
 * @copyright	Copyright (c) 2012, Objective HTML
 * @link 		http://www.objectivehtml.com/
 * @version		1.0
 * @build		20120824
 */

abstract class Interface_builder_core {
	
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
		foreach(array('/^get_/' => 'get_' , '/^set_/' => 'set_') as $regex => $replace)
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
	    if(property_exists($this, $prop))
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
}
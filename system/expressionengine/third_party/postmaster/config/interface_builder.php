<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* Auto formats the inputs into an array 
	
	Example
	setting[SomeIndex1][SomeIndex2] = 'Some Value';
	
	vs
	
	SomeIndex2 = 'Some Value';
*/

$config['interface_builder_use_array']   = TRUE;



/* Use_array is TRUE, this is the variable name of the array */

$config['interface_builder_var_name'] = array(
	'service' => 'setting',
	'hook'    => 'setting'
);



/* This is the name of the index that is the meta array */

$config['interface_builder_meta_index']  = array(
	'service' => 'service',
	'hook'	  => 'hook'
);
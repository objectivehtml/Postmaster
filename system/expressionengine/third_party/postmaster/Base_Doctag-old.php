<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*------------------------------------------
 *	Doctag Core Class
/* -------------------------------------- */

abstract class Base_Doctag_Core {

	public $EE;
	public $name;
	public $description;
		
	public function __construct($params = array())
	{
		$this->EE =& get_instance();
		
		foreach($params as $name => $value)
		{
			if(property_exists($this, $name))
			{
				$this->$name = $value;
			}			
		}
	}
	
	protected function get($name, $index)
	{
		$value = $this->get($name);
		
		if(isset($value[$index]))
		{
			return $value[$index];
		}
				
		return FALSE;
	}
}

/*------------------------------------------
 *	Base Doctag
/* -------------------------------------- */

class Base_Doctag extends Base_Doctag_Core {
	
	public $name;
	public $addon;
	public $description = NULL;
	public $suffix = '_doctag';
	protected $methods = array();
	
	public function __construct($params = array())
	{
		parent::__construct($params);
		
		$this->EE->load->helper('directory');
	}
	
	/*------------------------------------------
	 *	Loader
	/* -------------------------------------- */
	
	protected function load($name)
	{
		include_once ucfirst($name).'.php';
		
		$class = $name.$this->suffix;

		if(!class_exists($class))
		{
			if(isset($doctags[$this->addon][strtolower($name)]))
			{
				return $doctags[$this->addon][strtolower($name)];
			}
			else
			{
				$this->EE->output->show_user_error('general', '\''.ucfirst($name).'\' is not a valid doctag');	
			}
		}	
		
		return new $class;
	}
	
	
	public function get_doctags($name = FALSE, $directory = '../doctags')
	{
		$doctags = array();
		
		foreach(directory_map($directory) as $file)
		{
			if(file_exists($directory.'/'.$file) && $file != 'Base_Doctag.php')
			{
				$doctags[] = $this->load(str_replace('.php', '', $file));
			}
		}
		
		if(!$name)
		{
			return $doctags;
		}
		else
		{
			return $doctags[$name];
		}
	}
	
	/*------------------------------------------
	 *	Methods
	/* -------------------------------------- */
	
	public function set_method(Base_Doctag_Method $method)
	{
		$this->parameters[] = $parameter;
	}
	
	public function set_methods(array $method)
	{
		$this->methods = array_merge($this->methods, $method);
	}
	
	public function get_methods()
	{
		return $this->methods;
	}
	
	public function get_method($index)
	{
		return $this->get('methods', $index);
	}
}

/*------------------------------------------
 *	Snippet
/* -------------------------------------- */

class Base_Doctag_Snippet extends Base_Doctag_Core {

	public function __construct($params = array())
	{
		parent::__construct($params);
	}
	
	public $value;
}

/*------------------------------------------
 *	Parameter
/* -------------------------------------- */

class Base_Doctag_Parameter extends Base_Doctag_Core {

	public function __construct($params = array())
	{
		parent::__construct($params);
	}
	
	public $type;
	public $default_value;
}

/*------------------------------------------
 *	Example
/* -------------------------------------- */

class Base_Doctag_Example extends Base_Doctag_Core {

	public function __construct($params = array())
	{
		parent::__construct($params);
	}
	
	public $code;
}

/*------------------------------------------
 *	Variable
/* -------------------------------------- */

class Base_Doctag_Variable extends Base_Doctag_Core {

	public function __construct($params = array())
	{
		parent::__construct($params);
	}
	
	public $code;
	public $return_type;
}
	
/*------------------------------------------
 *	Tag Pair
/* -------------------------------------- */
	
class Base_Doctag_Tag_Pair extends Base_Doctag_Core {

	public function __construct($params = array())
	{
		parent::__construct($params);
	}
	
	public $return_type;
	public $is_looping;
	public $code;
	protected $variables = array();
	protected $examples = array();
	
	/*------------------------------------------
	 *	Examples
	/* -------------------------------------- */
		
	public function set_example(Base_Doctag_Example $example)
	{
		$this->examples[] = $example;
	}
	
	public function set_examples(array $examples)
	{
		$this->examples = array_merge($this->examples, $examples);
	}
	
	public function get_examples()
	{
		return $this->examples;
	}
	
	public function get_example($index)
	{
		return $this->get('examples', $index);
	}
	
	/*------------------------------------------
	 *	Variables
	/* -------------------------------------- */
	
	public function set_variable(Base_Doctag_Variable $variables)
	{
		$this->variables[] = $variables;
	}
	
	public function set_variables(array $variables)
	{
		$this->variables = array_merge($this->variables, $variables);
	}
	
	public function get_variables()
	{
		return $this->variables;
	}
	
	public function get_variable($index)
	{
		return $this->get('variables', $index);
	}
}

/*------------------------------------------
 *	Method
/* -------------------------------------- */

class Base_Doctag_Method extends Base_Doctag_Core {

	public function __construct($params = array())
	{
		parent::__construct($params);
	}

	public $title;
	public $is_looping;
	public $return_type;
	protected $snippets   = array();
	protected $parameters = array();
	protected $examples   = array();
	protected $variables  = array();
	protected $tag_pairs  = array();
	
	/*------------------------------------------
	 *	Snippets
	/* -------------------------------------- */
	
	public function set_snippet(Base_Doctag_Snippet $snippet)
	{
		$this->snippets[] = $snippet;
	}
	
	public function set_snippets(array $snippets)
	{
		$this->snippets = array_merge($this->snippets, $snippets);
	}
	
	public function get_snippets()
	{
		return $this->snippets;
	}
	
	public function get_snippet($index)
	{
		return $this->get('snippets', $index);
	}
	
	/*------------------------------------------
	 *	Parameters
	/* -------------------------------------- */
	
	public function set_parameter(Base_Doctag_Parameter $parameter)
	{
		$this->parameters[] = $parameter;
	}
	
	public function set_parameters(array $parameters)
	{
		$this->parameters = array_merge($this->parameters, $parameters);
	}
	
	public function get_parameters()
	{
		return $this->parameters;
	}
	
	public function get_parameter($index)
	{
		return $this->get('parameters', $index);
	}
	
	/*------------------------------------------
	 *	Examples
	/* -------------------------------------- */
		
	public function set_example(Base_Doctag_Example $example)
	{
		$this->examples[] = $example;
	}
	
	public function set_examples(array $examples)
	{
		$this->examples = array_merge($this->examples, $examples);
	}
	
	public function get_examples()
	{
		return $this->examples;
	}
	
	public function get_example($index)
	{
		return $this->get('examples', $index);
	}
	
	/*------------------------------------------
	 *	Variables
	/* -------------------------------------- */
	
	public function set_variable(Base_Doctag_Variable $variables)
	{
		$this->variables[] = $variables;
	}
	
	public function set_variables(array $variables)
	{
		$this->variables = array_merge($this->variables, $variables);
	}
	
	public function get_variables()
	{
		return $this->variables;
	}
	
	public function get_variable($index)
	{
		return $this->get('variables', $index);
	}
	
	/*------------------------------------------
	 *	Tag Pairs
	/* -------------------------------------- */
	
	public function set_tag_pair(Base_Doctag_Tag_Pair $tag_pair)
	{
		$this->tag_pairs[] = $tag_pair;
	}
	
	public function set_tag_pairs(array $tag_pairs)
	{
		$this->tag_pairs = array_merge($this->tag_pairs, $tag_pairs);
	}
	
	public function get_tag_pairs()
	{
		return $this->tag_pairs;
	}
	
	public function get_tag_pair($index)
	{
		return $this->get('tag_pairs', $index);
	}
	
}
	
	
	/*
	
	protected function set($name, $value, $type = FALSE)
	{
		if($type)
		{
			if(!$this->validate($value, $type))
			{
				return FALSE;
			}
		}
		
		if(isset($this->$name))
		{
			$this->$name = $value;
		}
		else
		{
			return FALSE;	
		}
		
		return TRUE;
	}
	
	protected function gets()
	{
		if(isset($this->$name))
		{
			return $this->$name;
		}
		
		return FALSE;
	}
	
	protected function validate($value, $type)
	{
		if($type == 'array' && !is_array($value))
		{
			return FALSE;
		}
		
		if($type == 'object' && !is_object($value))
		{
			return FALSE;
		}
		
		if($type == 'string' && !is_string($value))
		{
			return FALSE;
		}
		
		if(($type == 'integer' || $type == 'int') && !is_integer($value))
		{
			return FALSE;
		}
		
		if(($type == 'boolean' || $type == 'bool') && !is_string($value))
		{
			return FALSE;
		}
		
		return TRUE;
	}*/
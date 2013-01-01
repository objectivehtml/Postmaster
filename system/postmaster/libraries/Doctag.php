<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if(!function_exists('Markdown'))
{
	require_once 'Markdown.php';
}

class Doctag {

	protected $EE, $base_path, $markdown;
	
	public function __construct($params = array())
	{
		foreach($params as $index => $value)
		{
			$this->$index = $value;
		}
		
		$this->EE =& get_instance();
		
		if($this->EE->input->get('doctag_action'))
		{		
			$file_name = $this->EE->input->get('file_name');
			$file_path = $this->EE->input->get('file_path');
		
			$snippet_name  = $this->EE->input->get('snippet_name');
			$snippet_value = $this->markdown($file_name, $file_path);
			
			$doctag_action = $this->EE->input->get('doctag_action');
			
			if(strtolower($doctag_action) == 'install')
			{
				$this->install_snippet($snippet_name, $snippet_value);
			}
			else
			{
				$this->uninstall_snippet($snippet_name);
				
				unset($_GET['file_path']);
				unset($_GET['file_name']);
				unset($_GET['snippet_name']);
				unset($_GET['doctag_action']);
			}
		}
	}
	
	public function markdown($str, $path = FALSE, $markdown = TRUE, $debug = FALSE)
	{
		if(!$path)
		{
			$path = $this->base_path;	
		}
		
		$str = $path . '/' . $str;
		
		$return = NULL;
		
		if(file_exists($str))
		{
			$return = file_get_contents($str);
		
			if(strstr($str, '.md') && $markdown)
			{
				$return = Markdown($return);	
			}
									
			$return = preg_replace('/'.LD.'THEME_URL'.RD.'/', $this->EE->config->item('theme_folder_url'), $return);
			
			$return = trim($return);
		}
		
		return $return;
	}
			
	public function directory_map($name = FALSE, $path = FALSE)
	{
		$this->EE->load->helper('directory');
				
		return directory_map($this->base_path($name));
	}
	
	public function base_path($append = FALSE)
	{
		$lang = config_item('language');
		
		$directory = rtrim($this->base_path, '/') . '/';
		
		if(!is_dir($directory.$lang))
		{
			$lang = 'english';
		}
		
		return $directory.$lang.($append ? '/' . $append : NULL);
	}
	
	public function page($name, $render = TRUE)
	{	
		$path = $this->base_path($name);
	
		if(!is_dir($path))
		{
			show_error('The \''.$name.'\' is not a valid page');
		}
		
		$page = new Doctag_Page($name, $path);
		
		if(!$render)
		{
			return $page;	
		}
		else
		{
			return $this->render_page($page);
		}
	}
	
	public function render_page(Doctag_Page $page)
	{
		$tag = $this->EE->input->get('tag');
		
		return $this->EE->load->view('doctag', array(
			'page' => $page,
			'tag'  => $tag,
			'index_url'  => $this->cp_url('doctag', FALSE, FALSE, array('tag')),
			'page_overview' => !$tag || strtolower($tag) == 'overview' ? $page->overview : NULL 
		), TRUE);
	}
	
	public function is_snippet_installed($name)
	{
		$this->EE->db->where('snippet_name', $name);
		$response = $this->EE->db->get('snippets');
		
		if($response->num_rows() == 0)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	public function install_snippet($name, $value)
	{
		if(!$this->is_snippet_installed($name))
		{
			$this->EE->db->insert('snippets', array(
				'site_id'          => config_item('site_id'),
				'snippet_name'     => $name,
				'snippet_contents' => $value
			));
		}
	}
	
	public function uninstall_snippet($name)
	{
		$this->EE->db->where('snippet_name', $name);
		$this->EE->db->delete('snippets');
	}
	
	public function table($directory = FALSE)
	{
		$this->EE->load->library('table');
		
		if(!$directory)
		{
			$directory = $this->base_path;
		}
		
		$map = $this->directory_map($directory);
		
		$this->EE->table->set_heading('Tag Name', 'test');
		
		foreach($map as $method_name => $method)
		{
			$this->EE->table->add_row($method_name, 'test');
		}
		
		return $this->EE->table->generate();
	}
	
	public function current_url($append = '', $value = '')
	{
		$http = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
		
		$port = $_SERVER['SERVER_PORT'] == '80' || $_SERVER['SERVER_PORT'] == '443' ? NULL : ':' . $_SERVER['SERVER_PORT'];
		
		if(!isset($_SERVER['SCRIPT_URI']))
		{				
			 $_SERVER['SCRIPT_URI'] = $http . $_SERVER['HTTP_HOST']. $_SERVER['REQUEST_URI'];
		}
		
		$base_url = $http . $_SERVER['HTTP_HOST'] . $this->cp_url();
		
		if(!empty($append))
		{
			$base_url .= '&'.$append.'='.$value;
		}
		
		return $base_url;
	}
	
	public function cp_url($module = FALSE, $method = FALSE, $useAmp = FALSE, $exclude = array())
	{
		if(!defined('BASE'))
		{
			define('BASE', '');
		}
		
		if(!$module)
		{
			$module = $this->EE->input->get('module');
		}
		
		if(!$method)
		{
			$method = $this->EE->input->get('method');
		}

		$amp  = !$useAmp ? AMP : '&';

		$file = substr(BASE, 0, strpos(BASE, '?'));
		$file = str_replace($file, '', $_SERVER['PHP_SELF']) . BASE;
		
		$url = $file;
		
		foreach($_GET as $index => $value)
		{
			if(!in_array($index, $exclude))
			{
				$url .= '&'.$index.'='.$value;
			}
		}

		return str_replace(AMP, $amp, $url);
	}	
}

class Doctag_Page extends Doctag {

	public $name;
	public $title;
	public $overview = NULL;
	public $methods  = array();
	public $snippets = array();
	
	protected $base_path;
	
	public function __construct($name, $page)
	{
		parent::__construct();
		
		if(!is_dir($page))
		{
			show_error('\''.$name.'\' is not a valid Doctag.');
		}
		
		$this->name      = $name;
		$this->title     = $name;	
		$this->base_path = $page;
		$this->overview  = $this->markdown('overview.md');
		$this->methods   = $this->get_methods($this->base_path);
	}
		
	public function get_methods($path = FALSE)
	{
		if(!$path)
		{
			$path = $this->base_path;	
		}
		
		$methods = array();
		
		foreach(directory_map($path) as $index => $file_name)
		{
			if(is_array($file_name))
			{
				$methods[$index] = new Doctag_Method($index, $path);
			}	
		}
		
		return $methods;
	}
}

class Doctag_Method extends Doctag {

	public $name;
	public $tag;
	public $documentation;
	public $examples;
	public $snippet_table;
	public $snippets = array();
	public $url;
	public $selected = FALSE;
	
	protected $base_path;
	
	public function __construct($name, $page)
	{
		parent::__construct();
		
		$this->EE->load->library('table');
		$this->EE->load->helper('text');
		
		if(!is_dir($page.'/'.$name))
		{
			show_error('\''.$name.'\' is not a valid method.');
		}
		
		$this->name          = $name;
		$this->title         = $name;	
		$this->base_path     = $page.'/'.$name;
		$this->tag           = $this->markdown('tag.md', FALSE, FALSE);
		$this->documentation = $this->markdown('documentation.md');
		//$this->examples    = $this->markdown('examples.md');
		$this->snippets      = $this->get_snippets();
		$this->url			 = $this->cp_url('doctag') . '&id='.$this->EE->input->get(
	'id') . '&tag='.$name;
		$this->selected		 = $this->EE->input->get('tag') == $name ? TRUE : FALSE;
	}
	
	public function get_snippets($path = FALSE)
	{
		$snippets = array();
		
		if(!$path)
		{
			$path = $this->base_path . '/snippets';
		}
		
		$this->EE->table->set_heading('Snippet', 'Contents', 'Action');
		
		if(is_dir($path))
		{
			foreach(directory_map($path) as $index => $file_name)
			{
				$snippet_name = preg_replace("/\\.[\\w]*/u", "", $file_name);
				$link_text    = $this->is_snippet_installed($snippet_name) ? 'Uninstall' : 'Install';
				$link         = $this->current_url() . '&doctag_action='.$link_text.'&snippet_name=' . $snippet_name.'&file_path='.$path.'&file_name='.$file_name;
				$snippets[$file_name] = $this->markdown($file_name, $this->base_path.'/snippets', FALSE, TRUE);
				$this->EE->table->add_row('<a href="#'.$snippet_name.'" class="reveal">'.$snippet_name.'</a>', character_limiter(strip_tags($snippets[$file_name]), 70), '<a href="'.$link.'">'.$link_text.'</a>');
			}
		}
		
		$this->snippet_table = count($this->EE->table->rows) > 0 ? $this->EE->table->generate() : NULL;
		
		return $snippets;
	}
}
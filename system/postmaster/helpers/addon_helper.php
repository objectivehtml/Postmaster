<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Addon Helper
 * 
 * @author		Justin Kimbrell
 * @copyright	Copyright (c) 2012, Objective HTML
 * @link 		http://www.objectivehtml.com/
 * @version		1.0.2
 * @build		20140723
 */

ee()->load->helper('url_helper');

/**
 * Site URL
 *
 * @return	string
 */
 
if(!function_exists('site_url'))
{
	function site_url()
	{
		return config_item('site_url');
	}
}

/**
 * Action URL
 *
 * @param	string 	Class name 
 * @param	string 	Method name 
 * @return	string
 */
 
if(!function_exists('action_url'))
{
	function action_url($class, $method, $current_url = TRUE)
	{
		$EE =& get_instance();
		
		$EE->db->where(array(
			'class'  => $class,
			'method' => $method
		));
		
		$action    = $EE->db->get('actions')->row();		
		$action_id = isset($action->action_id) ? $action->action_id : NULL;
		
		if($current_url)
		{
			$url = page_url(TRUE, FALSE);
		}
		else
		{
			$url = base_page(TRUE);
		}
		
		return $url . '?ACT='.$action_id;
	}
}


/**
 * Current URL
 *
 * @param	bool 	Return URI segments
 * @return	string
 */
 
if(!function_exists('page_url'))
{
	function page_url($uri_segments = FALSE, $append_get = TRUE, $use_config = FALSE)
	{
		$EE =& get_instance();
		
		$segments = $EE->uri->segment_array();
		$base_url = base_page($use_config);		
		$uri      = '';
		$get      = '';
		
		$port = $_SERVER['SERVER_PORT'] == '80' || $_SERVER['SERVER_PORT'] == '443' ? NULL : ':' . $_SERVER['SERVER_PORT'];
		
		if($uri_segments)
		{
			$uri = '/' . implode('/', $segments);
		}
		
		if(count($_GET) > 0 && $append_get)
		{
			$get = '?'.http_build_query($_GET);
		}

		return rtrim($base_url, '/') . $uri . $get;
	}
}


/**
 * Control Panel URL
 *
 * @param	string 	Module name 
 * @param	string 	Method name 
 * @param	bool 	URL encode ampersand's
 * @return	string
 */
 
if(!function_exists('cp_url'))
{
	function cp_url($module_name, $method = 'index', $encode_ampersand = TRUE)
	{
		$amp  = $encode_ampersand ? AMP : '&';

		$file = substr(BASE, 0, strpos(BASE, '?'));
		$file = str_replace($file, '', $_SERVER['PHP_SELF']) . BASE;

		$url  = $file .$amp. '&C=addons_modules' .$amp . 'M=show_module_cp' . $amp . 'module=' . $module_name . $amp . 'method=' . $method;

		return str_replace(AMP, $amp, $url);
	}
}


/**
 * Base URL
 *
 * @param 	string	Append a string to the end of the url
 * @return	string
 */
 
if(!function_exists('base_page'))
{
	function base_page($use_config = FALSE, $append = NULL)
	{
		$http = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
		
		if(!isset($_SERVER['SCRIPT_URI']))
		{				
			 $_SERVER['SCRIPT_URI'] = $http . $_SERVER['HTTP_HOST']. $_SERVER['REQUEST_URI'];
		}
		
		$segments = rtrim(str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']), '/');
		$base_url = rtrim($http . $_SERVER['HTTP_HOST'] . $segments . '/' . config_item('site_index'), '/');
		$base_url = str_replace(array('http://', 'https://'), '', $base_url);
		
		if(!$use_config)
		{
			$return = $http . $base_url . $append;
		}
		else
		{
			$return = config_item('site_url');
		}
		
		return $return;
	}
}

/**
 * Base URL
 *
 * Returns the "base_url" item from your config file
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists('base_url'))
{
	function base_url()
	{
		$CI =& get_instance();
		return $CI->config->slash_item('base_url');
	}
}

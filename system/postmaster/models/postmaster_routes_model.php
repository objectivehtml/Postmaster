<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Postmaster_routes_model extends CI_Model {
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function load($class, $path = FALSE)
	{
		if($path && !class_exists($class))
		{
			require_once $path;
		}		
		
		if(isset($this->$class))
		{
			$obj =& $this->$class;
		}
		else
		{
			$obj = new $class();
		}
		
		return $obj;
	}
	
	public function call($class, $method)
	{
		
	}
		
	public function get_routes($params = array())
	{
		$this->load->driver('channel_data');
		
		return $this->channel_data->get('postmaster_routes', $params);
	}
	
	public function get_routes_by_hook($hook)
	{
		return $this->get_routes(array(
			'where' => array(
				'hook' => $hook	
			)
		));
	}
	
	public function get_route($id)
	{
		return $this->get_routes(array(
			'where' => array(
				'id' => $id	
			)
		));
	}
	
	public function existing($class, $method, $hook, $file)
	{
		return $this->get_routes(array(
			'where' => array(
				'class'  => $class,
				'method' => $method,
				'hook'   => $hook,
				'file'   => $file
			)
		))->num_rows() == 0 ? FALSE : TRUE;
	}
	
	public function create($class, $method, $hook, $file)
	{
		if(!$this->existing($class, $method, $hook, $file))
		{
			$this->db->insert('postmaster_routes', array(
				'class'  => $class,
				'method' => $method,
				'hook'   => $hook,
				'file'   => $file
			));
		}
	}
	
	public function update($id, $data = array())
	{
		$this->db->where('id', $id);
		$this->db->update('postmaster_routes', $data);
	}
	
	public function delete_route($id, $data = array())
	{
		$this->db->where('id', $id);
		$this->db->delete('postmaster_routes');
	}
	
	public function delete($where = array())
	{
		$this->db->where($where);
		$this->db->delete('postmaster_routes');
	}
}
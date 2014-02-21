<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Postmaster_routes_model extends CI_Model {
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function load($class, $path = FALSE, $params = array())
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
			$obj = new $class($params);
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
	
	public function get_routes_by_type($hook, $type = FALSE, $id = FALSE)
	{
		$where = array(
			'hook' => $hook
		);

		if($type)
		{
			$where['type'] = $type;
		}

		if($id)
		{
			$where['obj_id'] = $id;
		}

		return $this->get_routes(array(
			'where' => $where
		));
	}

	public function get_routes_by_hook($hook, $id = FALSE)
	{
		return $this->get_routes_by_type($hook, 'hook', $id);
	}
	
	public function get_routes_by_task($hook, $id = FALSE)
	{
		
		return $this->get_routes_by_type($hook, 'task', $id);
	}

	public function get_route($id)
	{
		return $this->get_routes(array(
			'where' => array(
				'obj_id' => $id
			)
		));
	}

	public function delete_route_extensions($obj_id, $hook, $type)
	{
		$existing_routes = $this->postmaster_routes_model->get_routes(array(
			'where' => array(
				'obj_id' => '!= '.$obj_id,
				'hook'   => $hook,
				'type'   => $type
			)
		));

		if($existing_routes->num_rows() == 0)
		{
			$routes = $this->postmaster_routes_model->get_routes(array(
				'where' => array(
					'obj_id' => $obj_id,
					'hook'   => $hook,
					'type'   => $type
				)
			));

			foreach($routes->result() as $route)
			{
				$this->db->delete('extensions', array(
					'class'  => 'Postmaster_ext',
					'method' => 'trigger_task_hook',
					'hook'   => $route->hook
				));
			}
		}
	}

	public function needs_installed($class, $method, $hook, $file, $type = FALSE, $id = FALSE)
	{
		return $this->existing($class, $method, $hook, $file, $type, $id) ? FALSE : TRUE;
	}

	public function existing($class, $method, $hook, $file, $type = FALSE, $id = FALSE)
	{
		$data = array(
			'class'  => $class,
			'method' => $method,
			'hook'   => $hook,
			'file'   => $file,
		);

		if($type)
		{
			$data['type']   = $type;
		}

		if($id)
		{
			$data['obj_id'] = $id;
		}

		return $this->get_routes(array(
			'where' => $data
		))->num_rows() == 0 ? FALSE : TRUE;
	}

	public function existing_by_id($id, $type = FALSE)
	{
		$data = array();
		
		if($type)
		{
			$data['type']   = $type;
		}

		if($id)
		{
			$data['obj_id'] = $id;
		}

		return $this->get_routes(array(
			'where' => $data
		))->num_rows() == 0 ? FALSE : TRUE;
	}
	
	public function create($class, $method, $hook, $file, $type = 'hook', $id = FALSE)
	{
		if(!$this->existing($class, $method, $hook, $file, $type, $id))
		{
			$data = array(
				'class'  => $class,
				'method' => $method,
				'hook'   => $hook,
				'file'   => $file,
				'type'   => $type,
			);

			if($id)
			{
				$data['obj_id'] = $id;
			}

			$this->db->insert('postmaster_routes', $data);
		}
	}
	
	public function update($id, $data = array())
	{
		$this->db->where('id', $id);
		$this->db->update('postmaster_routes', $data);
	}
	
	/*
	public function delete_route($id, $data = array())
	{
		$this->db->where('id', $id);
		$this->db->delete('postmaster_routes');
	}
	*/

	public function delete($where = array())
	{
		$this->db->where($where);
		$this->db->delete('postmaster_routes');
	}
	
	public function delete_route($route_id, $type = FALSE)
	{
		$where = array(
			'obj_id' => $route_id
		);

		if(!$type)
		{
			$where['type'] = $type;
		}

		$hooks = $this->get_routes(array(
			'where' => $where
		));

		foreach($hooks->result() as $row)
		{	
			if($this->needs_installed($row->class, $row->method, $row->hook, $row->file, 'hook', $route_id))
			{
				$this->db->delete('extensions', array(
					'class'  => 'Postmaster_ext',
					'method' => 'trigger_hook',
					'hook'   => $row->hook
				));	
			}
		}

		$this->delete(array(
			'type'   => 'hook',
			'obj_id' => $route_id
		));
	}

	public function delete_task($task_id)
	{
		$hooks = $this->get_routes(array(
			'where' => array(
				'obj_id' => $task_id,
				'type'   => 'task'
			)
		));

		foreach($hooks->result() as $row)
		{		
			$this->delete_route_extensions($task_id, $row->hook, 'task');
		}

		$this->delete(array(
			'type'   => 'task',
			'obj_id' => $task_id
		));
	}
}
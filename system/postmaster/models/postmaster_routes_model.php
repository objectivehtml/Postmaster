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
	
	public function get_routes_by_task($task, $hook)
	{
		return $this->get_routes(array(
			'where' => array(
				'obj_id' => $task,
				'type'   => 'task',
				'hook'   => $hook	
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

	public function needs_installed($class, $method, $hook, $file, $type = FALSE, $id = FALSE)
	{
		if($id)
		{
			$this->db->where('obj_id !=', $id);
		}

		return $this->existing($class, $method, $hook, $file, $type) ? FALSE : TRUE;
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
			if($this->needs_installed($row->class, $row->method, $row->hook, $row->file, 'task', $task_id))
			{
				$this->db->delete('extensions', array(
					'class'  => 'Postmaster_ext',
					'method' => 'route_task_hook',
					'hook'   => $row->hook
				));	
			}
		}

		$this->delete(array(
			'type'   => 'task',
			'obj_id' => $task_id
		));
	}
}
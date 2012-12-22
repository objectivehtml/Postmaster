<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'Base_class.php';

class Postmaster_time extends Base_class {
	
	/**
	 * Calculcated Offset
	 *
	 * @var int
	 */
	
	private $offset;
	
	
	/**
	 * Current time minus the offset
	 *
	 * @var int
	 */
	 
	private $time;
	
	
	public function __construct($time = 0, $params = array())
	{
		$second = 1;
		$minute = $second * 60;
		$hour   = $minute * 60;
		$day    = $hour * 24;
		$week   = $day * 7;		
		$offset = 0;
		
		foreach($params as $index => $param)
		{
			$index = rtrim($index, 's');
			
			if(isset($$index) && !empty($param))
			{
				$offset += (int) $param * $$index;	
			}	
		}
		
		$this->offset = $offset;
		$this->time   = $time - $offset;
	}
	
	/**
	 * Determine if a timetamp is
	 *
	 * @access	public
	 * @param	int   	A valid timestamp
	 * @return	bool
	 */
	public function has_time_past($time)
	{
		if(!preg_match('/^\d*$/', $time))
		{
			$time = strtotime($time);
		}
		
		$time = (int) $time;
		
		if($time < $this->time)
		{
			return TRUE;
		}
		
		return FALSE;
	}
}
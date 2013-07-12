<?php
/** 
 * @package infra
 * @subpackage utils
 */
class myGenericContainer
{
	private $map = null;
	
	public function myGenericContainer ( $map )
	{
		$this->map = $map;
	}
	
	public function getRequestParameter( $param , $default_value = null  )
	{
		$val = (isset($this->map[$param]) ? $this->map[$param] : null);
		if ( $val == null )
			return $default_value;
		return $val;
	}
}

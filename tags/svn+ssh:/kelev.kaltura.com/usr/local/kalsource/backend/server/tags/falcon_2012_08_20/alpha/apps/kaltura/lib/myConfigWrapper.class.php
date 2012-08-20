<?php

/**
 * Wraps the sfConfig static methods so the prefix of the param paths are transparent
 */
class myConfigWrapper
{
	private $m_path_prefix = "";
	
	public function myConfigWrapper ( $path_prefix )
	{
		$this->m_path_prefix = $path_prefix;
	}
	
	public function get ( $param_name , $def_value = NULL )
	{
		return sfConfig::get( $this->m_path_prefix . $param_name , $def_value );
	}

	public function getList ( $param_name , $def_value = NULL )
	{
		$list = array();
		$index = 0;
		$indexed_param = $param_name . "_" . $index ;
		while ( $this->has ( $indexed_param ) )
		{
			$list[] = $this->get ( $indexed_param , $def_value );
			$index++;
			$indexed_param = $param_name . "_" . $index ;
		}
		return $list;
	}
	
	public function has ( $param_name )
	{
		return sfConfig::has( $this->m_path_prefix . $param_name );
	}
	
	
	// TODO - add wrapper methods if needed
}
?>
<?php
class myCustomData
{
	private $data;
	
	/**
	 * @param string $str
	 * @return myCustomData
	 */
	public static function fromString ( $str )
	{
		return new  myCustomData ( $str );
	}
	
	/**
	 * @param string $str
	 */
	private function  myCustomData ( $str )
	{
		if ( empty ( $str ) )
			$this->data = array();
		try
		{
			$this->data = @unserialize( $str );
			if ( $this->data == null )
			{
//				sfLogger::getInstance()->warning ( __METHOD__ . ", cannot init from string [$str]");
				$this->data = array();
			}			
		}
		catch ( Exception $ex )
		{
			// cannot initialize from $str 
			KalturaLog::log ( __METHOD__ . ", cannot init from string [$str]");
			$this->data = array();
		}
	}
	
	/**
	 * @param bool $null_if_empty
	 * @return string
	 */
	public function toString( $null_if_empty = true )
	{
		if ( $null_if_empty && ( $this->data == null || count ( $this->data ) == 0 ) )
			return null;
		return serialize( $this->data );
	}
	
	/**
	 * @param string $name
	 * @param string $value
	 * @param string $namespace = null
	 * @param bool $remove_if_empty
	 * @return string
	 */
	public function put ( $name , $value , $namespace = null  , $remove_if_empty=false )
	{
		if ( $namespace ) $name = $namespace . ":" . $name;
		if ( $remove_if_empty && empty ( $value ) )
		{
			unset ($this->data [$name]);
		}
		else
		{
			$this->data [$name] = $value;
		}
		
		return @$this->data [$name] ;
	}
	
	/**
	 * @param string $name
	 * @param string $namespace
	 * @return string
	 */
	public function get ( $name , $namespace = null  )
	{
		if ( $namespace ) $name = $namespace . ":" . $name;
		$res = null;
		if ( isset ($this->data[$name] ) ) 	$res =  @$this->data[$name];
		return $res;
	}
	
	/**
	 * @param string $name
	 * @param string $namespace
	 */
	public function remove ( $name , $namespace = null )
	{
		if ( $namespace ) $name = $namespace . ":" . $name;
		unset ($this->data [$name]);
	}
	
	/**
	 * Remove all data
	 */
	public function clearAll ()
	{
		unset ($this->data);
		$this->data = array();	
	}
	
	
	/**
	 * @param string $name
	 * @param int $delta
	 * @param string $namespace
	 * @return string
	 */
	public function inc ( $name , $delta = 1 , $namespace = null )
	{
		$val = $this->get ( $name , $namespace);
		if ( $val )
		{
			$val += $delta;
		}
		else
		{
			$val = $delta;
		}
		
		return $this->put ( $name , $val , $namespace );
	}
	
	/**
	 * @param string $name
	 * @param int $delta
	 * @param string $namespace
	 * @return string
	 */
	public function dec ( $name , $delta = 1 , $namespace = null )
	{
		return $this->inc ( $name , - $delta , $namespace );
	}
}

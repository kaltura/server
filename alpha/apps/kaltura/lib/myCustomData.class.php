<?php
class myCustomData
{
	// max custom_data size for later storing as a varchar column in the db, leaving a bit of spare space if we will actually hit this limit
	const MAX_TEXT_FIELD_SIZE = 65000;
	const MAX_MEDIUM_TEXT_FIELD_SIZE = 16770000;
	
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
	private function  __construct ( $str )
	{
		if ( empty ( $str ) )
			$this->data = array();
		try
		{
			$this->data = @unserialize( $str );
			if ( $this->data == null )
			{
				$this->data = array();
			}	
			else 
			{
				foreach($this->data as $name => $value)
				{
					if(strpos($name, ':') > 0)
					{
						list($namespace, $subName) = explode(':', $name, 2);
						unset($this->data[$name]);
						if(!isset($this->data[$namespace]))
							$this->data[$namespace] = array();
						$this->data[$namespace][$subName] = $value;
					}	
				}
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
		
		$s = serialize( $this->data );
		if (strlen($s) > self::MAX_TEXT_FIELD_SIZE)
			throw new kCoreException('Exceeded custom data max size', kCoreException::EXCEEDED_MAX_CUSTOM_DATA_SIZE);
			
		return $s; 
	}
	
	/**
	 * @return array
	 */
	public function toArray()
	{
		if(is_array($this->data))
			return $this->data;
		
		return array();
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
	if ( $namespace )
		{
			if ( $remove_if_empty && empty ( $value ) )
			{
				unset ($this->data [$namespace][$name]);
			}
			else
			{
				if(!isset($this->data[$namespace]))
					$this->data[$namespace] = array();
				$this->data[$namespace][$name] = $value;
			}
		}
		else
		{
			if ( $remove_if_empty && empty ( $value ) )
			{
				unset ($this->data [$name]);
			}
			else
			{
				$this->data [$name] = $value;
			}
		}
		
		if(isset($this->data [$name]))
			return $this->data [$name];
			
		return null;
	}
	
	/**
	 * @param string $name
	 * @param string $namespace
	 * @return string
	 */
	public function get ( $name , $namespace = null  )
	{
		if($namespace)
		{
			if (isset($name))
			{
				if (isset($this->data[$namespace][$name]))
					return $this->data[$namespace][$name];
			}
			else
			{
				if(isset($this->data[$namespace]))
					return $this->data[$namespace];
			}
		}
		elseif(isset($this->data[$name]))
		{ 	
			return $this->data[$name];
		}
			
		return null;
	}
	
	/**
	 * @param string $name
	 * @param string $namespace
	 */
	public function remove ( $name , $namespace = null )
	{
		if($namespace)
		{
			if ($name)
			{
				if(!isset($this->data[$namespace][$name]))
					return;
				unset($this->data[$namespace][$name]);
			}
			else
			{
				if(!isset($this->data[$namespace]))
					return;
				unset($this->data[$namespace]);
			}
		}
		else
		{
			if(!isset($this->data[$name]))
				return;
			unset ($this->data[$name]);
		}
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

<?php
class genericObjectWrapper  implements Iterator
{
	private $m_obj;
	private $m_is_array;
	private $m_recursive_wrapping ;
	private $m_ignore_null ;
	
	/**
	 * ignore null objects while extracting nested objects - if some object along the line is null, extracting of it's elements will return 
	 * 	empty nullWrappers which will return empty strings
	 */
	public function genericObjectWrapper ( $object , $recursive_wrapping = false , $ignore_null = false  )
	{
		$this->m_obj = $object;
		$this->m_is_array = is_array( $object );
		$this->m_recursive_wrapping = $recursive_wrapping;
		$this->m_ignore_null = $ignore_null;
/*		if ( $this->m_is_array )
		{
			reset ( $this->m_obj );
		}*/
	}

	public function getWrappedObj ()
	{
		return $this->m_obj;	
	}
	
	
	public function __get ( $field_name )
	{
		//echo ("__get:$field_name");
		
		$res = myBaseObject::envokeMethod ( $this->m_obj , $field_name );
		
		if ( $this->m_recursive_wrapping )
		{
			// wrap non-primative properties
			if ( is_object($res ) || is_array( $res ) )
			{
				$res =  new genericObjectWrapper( $res );
			}
		}

		if ( $res == null && $this->m_ignore_null ) 
		{
			return genericNullWrapper::getInstance();
		}
		return $res;
			
	}

	public function isArray()
	{
		return $this->m_is_array;
	}
	
	public function rewind()
	{
		if ( $this->m_obj == null ) return;
//		echo "rewind\n";
		$this->verifyIterator();
		reset($this->m_obj);
	}

	public function current()
	{
		//echo "current\n";
		$this->verifyIterator();
		return new genericObjectWrapper ( current($this->m_obj) );
	}

	public function key() 
	{
//		echo "key\n";
		$this->verifyIterator();
		return  key($this->m_obj);
	}

	public function next() 
	{
	//	echo "next\n";
		$this->verifyIterator();
		return new genericObjectWrapper ( next($this->m_obj) );
	}

	public function valid() 
	{
		if ( $this->m_obj == null ) return false;
//		echo "valid\n
		$this->verifyIterator();
		return current($this->m_obj) !== false;
	}

	protected function verifyIterator()
	{
		if ( $this->m_obj != null &&  ! $this->m_is_array ) throw new Exception ( "Cannot iterate an objec that is not an array" );
		
	}
}


class genericNullWrapper extends genericObjectWrapper 
{
	private static $s_instance = null;
	public static function getInstance ()
	{
		if ( self::$s_instance == null )
			self::$s_instance = new genericNullWrapper( null );
		return self::$s_instance;		
	}
	
	public function __get ( $field_name )
	{
		return self::getInstance(); // return self - incase of another bested field query
	}
	
	public function toString()
	{
		return "";
	}
}
?>
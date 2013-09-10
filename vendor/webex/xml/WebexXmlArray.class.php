<?php
require_once(__DIR__ . '/WebexXmlException.class.php');

class WebexXmlArray implements ArrayAccess, Iterator
{
	private $array = array();
	private $class = "";
	
	/**
	 * @var int $count
	 */	
	public $count;
	
	public function __construct($class)
	{
		$this->class = $class;
	}
	
	public function __toString()
	{
		$ret = '';
		foreach($this->array as $item)
			$ret .= strval($item);
		
		return $ret;
	}
	
	public function offsetExists($offset) 
	{
		return array_key_exists($offset, $this->array);
	}

	public function offsetGet($offset) 
	{
		return $this->array[$offset];
	}

	public function offsetSet($offset, $value) 
	{
		switch($this->class)
		{
			case 'string':
				if(!is_string($value))
					throw new WebexXmlException("'".get_class($value)."' is not an instance of '".$this->class."'");
				break;
				
			case 'int':
				if(!is_numeric($value))
					throw new WebexXmlException("'".get_class($value)."' is not an instance of '".$this->class."'");
				break;
				
			default:
				if (is_string($value) && is_string($offset))
				{
					$this->$offset = $value;
				}
				elseif (!($value instanceof $this->class))
				{
					throw new WebexXmlException("'".get_class($value)."' is not an instance of '".$this->class."'");
				}
		}
		
		if ($offset === null)
			$this->array[] = $value;
			
		$this->count = count ( $this->array );
	}

	
	public function offsetUnset($offset) 
	{
		
	}
	
	public function current() 
	{
		return current($this->array);
	}

	public function next() 
	{
		return next($this->array);
	}

	public function key() 
	{
		return key($this->array);
	}

	public function valid() 
	{
		return ($this->current() !== false);
	}

	public function rewind() 
	{
		reset($this->array);
	}
	
	public function getType()
	{
		return $this->class;
	}
	
	public function toArray()
	{
		return $this->array;
	}
}
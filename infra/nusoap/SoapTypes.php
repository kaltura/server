<?php

class SoapArray implements ArrayAccess, Iterator
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
					throw new Exception("'".get_class($value)."' is not an instance of '".$this->class."'");
				break;
				
			case 'int':
			case 'long':
				if(!is_numeric($value))
					throw new Exception("'".get_class($value)."' is not an instance of '".$this->class."'");
				break;
				
			default:
				if (!($value instanceof $this->class))
					throw new Exception("'".get_class($value)."' is not an instance of '".$this->class."'");
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
}
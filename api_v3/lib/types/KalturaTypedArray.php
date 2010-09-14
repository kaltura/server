<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaTypedArray extends KalturaObject implements ArrayAccess, Iterator
{
	private $array = array();
	private $class = "";
	private $dummyClass = null;
	
	/**
	 * @var int $count
	 */	
	public $count;
	
	public function __construct($class)
	{
		$this->class = $class;
		$this->dummyClass = new $class;		
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

		if (!($value instanceof $this->dummyClass)) {
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
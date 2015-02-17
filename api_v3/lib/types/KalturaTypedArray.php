<?php
/**
 * @package api
 * @subpackage objects
 */
abstract class KalturaTypedArray extends KalturaObject implements ArrayAccess, Iterator, Countable
{
	private $array = array();
	private $class = "";
	
	/**
	 * @var int $count
	 */	
	public $count;
	
	public function __construct($class = 'KalturaObject')
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
		if (!is_object($value) && is_subclass_of($value, $this->class))
			throw new Exception("'".get_class($value)."' is not an instance of '".$this->class."'");
		
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
	
	public function count()
	{
		return count($this->array);
	}
	
	public function toArray()
	{
		return $this->array;
	}
	
	public function toObjectsArray()
	{
		$array = array();
		foreach($this->array as $obj)
			$array[] = $obj->toObject();
		return $array;
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::loadRelatedObjects($responseProfile)
	 */
	public function loadRelatedObjects(KalturaResponseProfileBase $responseProfile)
	{
		foreach($this as &$item)
		{
			/* @var $item KalturaObject */
			$item->loadRelatedObjects($responseProfile);
		}
	}
}
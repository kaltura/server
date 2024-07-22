<?php
/**
 * @package api
 * @subpackage objects
 */
abstract class KalturaTypedArray extends KalturaObject implements ArrayAccess, Iterator, Countable
{
	protected $array = array();
	private $class = "";
	
	/**
	 * @var int $count
	 */	
	public $count;
	
	public function __construct($class = 'KalturaObject')
	{
		$this->class = $class;
	}
	
	public function offsetExists($offset): bool
	{
		return array_key_exists($offset, $this->array);
	}

	public function offsetGet($offset): mixed
	{
		return $this->array[$offset];
	}

	protected function validateType($value) 
	{
		if (!is_object($value) && is_subclass_of($value, $this->class))
			throw new Exception("'".get_class($value)."' is not an instance of '".$this->class."'");
	}

	public function offsetSet($offset, $value): void
	{
		$this->validateType($value);
		
		if ($offset === null)
		{
			$this->array[] = $value;
		}
			
		$this->count = count ( $this->array );
	}

	
	public function offsetUnset($offset): void
	{
		unset($this->array[$offset]);
	}
	
	public function current(): mixed
	{
		return current($this->array);
	}

	public function next(): void
	{
		next($this->array);
	}

	public function key(): mixed
	{
		return key($this->array);
	}

	public function valid(): bool
	{
		return ($this->current() !== false);
	}

	public function rewind(): void
	{
		reset($this->array);
	}
	
	public function getType()
	{
		return $this->class;
	}
	
	public function count(): int
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
		foreach($this->array as $key => $obj)
		{
			$coreObject = $obj->toObject();
			if($coreObject)
				$array[$key] = $coreObject;
		}
		return $array;
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::loadRelatedObjects($responseProfile)
	 */
	public function loadRelatedObjects(KalturaDetachedResponseProfile $responseProfile)
	{
		foreach($this as &$item)
		{
			/* @var $item KalturaObject */
			$item->loadRelatedObjects($responseProfile);
		}
	}
}
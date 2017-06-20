<?php
/**
 * An array of KalturaRange
 * 
 * @package api
 * @subpackage objects
 */
class KalturaRangeArray extends KalturaTypedArray
{

	public function __construct()
	{
		return parent::__construct("KalturaRange");
	}

	public static function fromDbArray(array $pairs = null)
	{
		return self::fromKeyValueArray($pairs);
	}
	
	public static function fromKeyValueArray(array $ranges = null)
	{
		$rangeArray = new KalturaRangeArray();
		if($ranges && is_array($ranges))
		{
			foreach($ranges as $start => $end)
			{
				$rangeObject = new KalturaRange();
				$rangeObject->start = $start;
				$rangeObject->end = $end;
				$rangeArray[] = $rangeObject;
			}
		}
		return $rangeArray;
	}

	public function toObjectsArray()
	{
		$ret = array();
		foreach ($this->toArray() as $keyValueObject)
		{
			/* @var $keyValueObject KalturaKeyValue */
			$ret[] = array($keyValueObject->start, $keyValueObject->end);
		}
		
		return $ret;
	}
}

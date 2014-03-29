<?php
/**
 * An array of KalturaKeyBooleanValue
 * 
 * @package api
 * @subpackage objects
 */
class KalturaKeyBooleanValueArray extends KalturaTypedArray
{
	public static function fromDbArray(array $pairs = null)
	{
		return self::fromKeyValueArray($pairs);
	}
	
	protected function appendFromArray(array $pairs, $prefix = '')
	{
		foreach($pairs as $key => $value)
		{
			if(is_array($value))
			{
				$this->appendFromArray($value, "$key.");
				continue;
			}
			
			$pairObject = new KalturaKeyBooleanValue();
			$pairObject->key = $prefix . $key;
			$pairObject->value = (bool)$value;
			$this[] = $pairObject;
		}
	}
	
	public static function fromKeyValueArray(array $pairs = null)
	{
		$pairsArray = new KalturaKeyBooleanValueArray();
		if($pairs && is_array($pairs))
		{
			foreach($pairs as $key => $value)
			{
				if(is_array($value))
				{
					$pairsArray->appendFromArray($value, "$key.");
					continue;
				}
				
				$pairObject = new KalturaKeyBooleanValue();
				$pairObject->key = $key;
				$pairObject->value = (bool)$value;
				$pairsArray[] = $pairObject;
			}
		}
		return $pairsArray;
	}
	
	public function __construct()
	{
		return parent::__construct("KalturaKeyBooleanValue");
	}
	
	public function toObjectsArray()
	{
		$ret = array();
		foreach ($this->toArray() as $keyValueObject)
		{
			/* @var $keyValueObject KalturaKeyBooleanValue */
			$ret[$keyValueObject->key] = $keyValueObject->value;
		}
		
		return $ret;
	}
}

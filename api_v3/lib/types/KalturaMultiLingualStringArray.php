<?php
/**
 * An array of KalturaMultiLingualString
 *
 * @package api
 * @subpackage objects
 */
class KalturaMultiLingualStringArray extends KalturaTypedArray
{
	public function __construct()
	{
		return parent::__construct("KalturaMultiLingualString");
	}
	
	public static function fromDbArray(array $pairs = null)
	{
		return self::fromMultiLingualStringArray($pairs);
	}
	
	protected function appendFromArray(array $pairs, $prefix = '')
	{
		foreach($pairs as $language => $value)
		{
			if(is_array($value))
			{
				$this->appendFromArray($value, "$language.");
				continue;
			}
			
			$pairObject = new KalturaMultiLingualString();
			$pairObject->$language = $prefix . $language;
			$pairObject->value = $value;
			$this[] = $pairObject;
		}
	}
	
	public static function fromMultiLingualStringArray(array $pairs = null)
	{
		$pairsArray = new KalturaMultiLingualStringArray();
		if($pairs && is_array($pairs))
		{
			foreach($pairs as $language => $value)
			{
				if(is_array($value))
				{
					$pairsArray->appendFromArray($value, "$language.");
					continue;
				}
				
				$pairObject = new KalturaMultiLingualString();
				$pairObject->language = $language;
				$pairObject->value = $value;
				$pairsArray[] = $pairObject;
			}
		}
		return $pairsArray;
	}
	
	public function toObjectsArray()
	{
		$ret = array();
		foreach ($this->toArray() as $multiLingualStringObject)
		{
			/* @var $multiLingualStringObject KalturaMultiLingualString */
			$ret[$multiLingualStringObject->language] = $multiLingualStringObject->value;
		}
		
		return $ret;
	}

	public function toObjectsArrayPurified($objectClass, $objectProp, $thisProp)
	{
		$ret = array();
		foreach ($this->toArray() as $multiLingualStringObject)
		{
			/* @var $multiLingualStringObject KalturaMultiLingualString */
			$value = $multiLingualStringObject->value;
			$value = $this->purifyObject($objectClass, $objectProp, $thisProp, $value);

			$ret[$multiLingualStringObject->language] = $value;
		}
		return $ret;
	}
}

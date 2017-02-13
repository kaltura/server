<?php
/**
 * An array of KalturaIntegerValue
 * 
 * @package api
 * @subpackage objects
 */
class KalturaIntegerValueArray extends KalturaTypedArray
{
	/**
	 * @param array<string|kIntegerValue> $strings
	 * @return KalturaIntegerValueArray
	 */
	public static function fromDbArray(array $ints = null, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$intArray = new KalturaIntegerValueArray();
		if($ints && is_array($ints))
		{
			foreach($ints as $int)
			{
				$intObject = new KalturaIntegerValue();
				
				if($int instanceof kValue)
				{
					$intObject->fromObject($int, $responseProfile);;
				}
				else
				{					
					$intObject->value = $int;
				}
				
				$intArray[] = $intObject;
			}
		}
		return $intArray;
	}
	
	public function __construct()
	{
		return parent::__construct("KalturaIntegerValue");
	}
}

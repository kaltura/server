<?php
/**
 * An array of KalturaBooleanValue
 * 
 * @package api
 * @subpackage objects
 */
class KalturaBooleanValueArray extends KalturaTypedArray
{
	/**
	 * @param array<string|kBooleanValue> $strings
	 * @return KalturaBooleanValueArray
	 */
	public static function fromDbArray(array $bools = null)
	{
		$boolArray = new KalturaBooleanValueArray();
		if($bools && is_array($bools))
		{
			foreach($bools as $bool)
			{
				$boolObject = new KalturaStringValue();
				
				if($bool instanceof kValue)
				{
					$boolObject->fromObject($bool);
				}
				else
				{					
					$boolObject->value = $bool;
				}
				
				$boolArray[] = $boolObject;
			}
		}
		return $boolArray;
	}
	
	public function __construct()
	{
		return parent::__construct("KalturaBooleanValue");
	}
}

<?php
/**
 * An array of KalturaStringValue
 * 
 * @package api
 * @subpackage objects
 */
class KalturaStringValueArray extends KalturaTypedArray
{
	/**
	 * @param array<string|kStringValue> $strings
	 * @return KalturaStringValueArray
	 */
	public static function fromDbArray(array $strings = null)
	{
		$stringArray = new KalturaStringValueArray();
		if($strings && is_array($strings))
		{
			foreach($strings as $string)
			{
				$stringObject = new KalturaStringValue();
				
				if($string instanceof kValue)
				{
					$stringObject->fromObject($string);
				}
				else
				{					
					$stringObject->value = $string;
				}
				
				$stringArray[] = $stringObject;
			}
		}
		return $stringArray;
	}
	
	public function __construct()
	{
		return parent::__construct("KalturaStringValue");
	}
}

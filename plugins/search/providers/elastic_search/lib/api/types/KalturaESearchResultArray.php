<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
abstract class KalturaESearchResultArray extends KalturaTypedArray {
	
	protected static function populateArray($outputArray, $objectType, $sourceArray, $responseProfile)
	{
		foreach ( $sourceArray as $obj )
		{
			$nObj = new $objectType();
			$nObj->fromObject($obj, $responseProfile);
			$outputArray[] = $nObj;
		}
		return $outputArray;
	}
}

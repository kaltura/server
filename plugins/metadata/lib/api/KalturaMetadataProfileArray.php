<?php
/**
 * @package plugins.metadata
 * @subpackage api.objects
 */
class KalturaMetadataProfileArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr, KalturaResponseProfileBase $responseProfile = null)
	{
		$newArr = new KalturaMetadataProfileArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
    		$nObj = new KalturaMetadataProfile();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaMetadataProfile");	
	}
}
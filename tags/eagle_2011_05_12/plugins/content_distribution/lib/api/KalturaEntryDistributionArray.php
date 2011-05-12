<?php
/**
 * @package plugins.contentDistribution
 * @subpackage api.objects
 */
class KalturaEntryDistributionArray extends KalturaTypedArray
{
	public static function fromDbArray($arr)
	{
		$newArr = new KalturaEntryDistributionArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
    		$nObj = new KalturaEntryDistribution();
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaEntryDistribution");	
	}
}
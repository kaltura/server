<?php
/**
 * @package plugins.exampleDistribution
 * @subpackage api.objects
 */
class KalturaExampleDistributionAssetPathArray extends KalturaTypedArray
{
	public static function fromArray($arr)
	{
		$newArr = new KalturaExampleDistributionAssetPathArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
    		$nObj = new KalturaExampleDistributionAssetPath();
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaExampleDistributionAssetPath");	
	}
}
<?php
/**
 * @package plugins.attUverseDistribution
 * @subpackage api.objects
 */
class KalturaAttUverseDistributionFileArray extends KalturaTypedArray
{
	public static function fromDbArray($arr)
	{
		$newArr = new KalturaAttUverseDistributionFileArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
			$nObj = new KalturaAttUverseDistributionFile();
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}

		return $newArr;
	}

	public function __construct()
	{
		parent::__construct("KalturaAttUverseDistributionFile");	
	}
}
<?php
/**
 * @package plugins.ftpDistribution
 * @subpackage api.objects
 */
class KalturaFtpDistributionFileArray extends KalturaTypedArray
{
	public static function fromDbArray($arr)
	{
		$newArr = new KalturaFtpDistributionFileArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
    		$nObj = new KalturaFtpDistributionFile();
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaFtpDistributionFile");	
	}
}
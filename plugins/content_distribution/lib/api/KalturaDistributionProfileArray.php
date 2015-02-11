<?php
/**
 * @package plugins.contentDistribution
 * @subpackage api.objects
 */
class KalturaDistributionProfileArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaResponseProfileBase $responseProfile = null)
	{
		$newArr = new KalturaDistributionProfileArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
    		$nObj = KalturaDistributionProfileFactory::createKalturaDistributionProfile($obj->getProviderType());
    		if(!$nObj)
    		{
    			KalturaLog::err("Distribution Profile Factory could not find matching profile type for provider [" . $obj->getProviderType() . "]");
    			continue;
    		}
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaDistributionProfile");	
	}
}
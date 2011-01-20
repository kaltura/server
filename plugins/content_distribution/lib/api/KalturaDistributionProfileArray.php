<?php

class KalturaDistributionProfileArray extends KalturaTypedArray
{
	public static function fromDbArray($arr)
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
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaDistributionProfile");	
	}
}
<?php
/**
 * @package plugins.businessProcessNotification
 * @subpackage api.objects
 */
class KalturaBusinessProcessServerArray extends KalturaTypedArray
{
	public static function fromDbArray($arr)
	{
		$newArr = new KalturaBusinessProcessServerArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
			/* @var $obj BusinessProcessServer */
    		$nObj = KalturaBusinessProcessServer::getInstanceByType($obj->getType());
    		if(!$nObj)
    		{
    			KalturaLog::err("Business-Process server could not find matching type for [" . $obj->getType() . "]");
    			continue;
    		}
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaBusinessProcessServer");	
	}
}
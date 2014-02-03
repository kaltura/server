<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaDeliveryArray extends KalturaTypedArray
{
	public static function fromDbArray($arr)
	{
		$newArr = new KalturaDeliveryArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
    		$nObj = KalturaDeliveryFactory::getDeliveryInstanceByType($obj->getType());
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaDelivery");	
	}
}
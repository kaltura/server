<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAccessControlActionArray extends KalturaTypedArray
{
	public static function fromDbArray($arr)
	{
		$newArr = new KalturaAccessControlActionArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
			$nObj = self::getInstanceByDbObject($obj);
			if(!$nObj)
				throw new kCoreException("No API object found for core object [" . get_class($obj) . "] with type [" . $obj->getType() . "]", kCoreException::OBJECT_API_TYPE_NOT_FOUND);
				
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}

	static function getInstanceByDbObject(kAccessControlAction $dbObject)
	{
		switch($dbObject->getType())
		{
			case accessControlActionType::BLOCK:
				return new KalturaAccessControlBlockAction();
			case accessControlActionType::PREVIEW:
				return new KalturaAccessControlPreviewAction();
			default:
				return KalturaPluginManager::loadObject('KalturaAccessControlAction', $dbObject->getType());
		}
	}
		
	public function __construct()
	{
		parent::__construct("KalturaAccessControlAction");	
	}
}
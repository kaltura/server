<?php
/**
 * @package api
 * @subpackage objects
 * @deprecated use KalturaRuleActionArray
 */
class KalturaAccessControlActionArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaAccessControlActionArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
			$nObj = self::getInstanceByDbObject($obj);
			if(!$nObj)
				throw new kCoreException("No API object found for core object [" . get_class($obj) . "] with type [" . $obj->getType() . "]", kCoreException::OBJECT_API_TYPE_NOT_FOUND);
				
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}

	static function getInstanceByDbObject(kRuleAction $dbObject)
	{
		switch($dbObject->getType())
		{
			case RuleActionType::BLOCK:
				return new KalturaAccessControlBlockAction();
			case RuleActionType::PREVIEW:
				return new KalturaAccessControlPreviewAction();
			case RuleActionType::LIMIT_FLAVORS:
				return new KalturaAccessControlLimitFlavorsAction();
			case RuleActionType::LIMIT_THUMBNAIL_CAPTURE:
				return new KalturaAccessControlLimitThumbnailCaptureAction();
			default:
				return KalturaPluginManager::loadObject('KalturaAccessControlAction', $dbObject->getType());
		}
	}
		
	public function __construct()
	{
		parent::__construct("KalturaAccessControlAction");	
	}
}
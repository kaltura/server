<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaRuleActionArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaRuleActionArray();
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
			case RuleActionType::ADD_TO_STORAGE:
				return new KalturaStorageAddAction();	
			case RuleActionType::LIMIT_DELIVERY_PROFILES:
				return new KalturaAccessControlLimitDeliveryProfilesAction();
			case RuleActionType::SERVE_FROM_REMOTE_SERVER:
				return new KalturaAccessControlServeRemoteEdgeServerAction();
			case RuleActionType::REQUEST_HOST_REGEX:
				return new KalturaAccessControlModifyRequestHostRegexAction();
			case RuleActionType::LIMIT_THUMBNAIL_CAPTURE:
				return new KalturaAccessControlLimitThumbnailCaptureAction();
			default:
				return KalturaPluginManager::loadObject('KalturaRuleAction', $dbObject->getType());
		}		
	}
		
	public function __construct()
	{
		parent::__construct("KalturaRuleAction");	
	}
}
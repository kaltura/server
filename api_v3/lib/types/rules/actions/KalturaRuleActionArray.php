<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaRuleActionArray extends KalturaTypedArray
{
	public static function fromDbArray($arr)
	{
		$newArr = new KalturaRuleActionArray();
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
			default:
				return KalturaPluginManager::loadObject('KalturaRuleAction', $dbObject->getType());
		}		
	}
		
	public function __construct()
	{
		parent::__construct("KalturaRuleAction");	
	}
}
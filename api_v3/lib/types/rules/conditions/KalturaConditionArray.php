<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaConditionArray extends KalturaTypedArray
{
	public static function fromDbArray($arr)
	{
		$newArr = new KalturaConditionArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
			$nObj = self::getInstanceByDbObject($obj);
			if(!$nObj)
			{
				KalturaLog::alert("Object [" . get_class($obj) . "] type [" . $obj->getType() . "] could not be translated to API object");
				continue;
			}
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}

	static function getInstanceByDbObject(kCondition $dbObject)
	{
		switch($dbObject->getType())
		{
			case ConditionType::AUTHENTICATED:
				return new KalturaAuthenticatedCondition();
			case ConditionType::COUNTRY:
				return new KalturaCountryCondition();
			case ConditionType::IP_ADDRESS:
				return new KalturaIpAddressCondition();
			case ConditionType::SITE:
				return new KalturaSiteCondition();
			case ConditionType::USER_AGENT:
				return new KalturaUserAgentCondition();
			case ConditionType::FIELD_COMPARE:
				return new KalturaFieldCompareCondition();
			case ConditionType::FIELD_MATCH:
				return new KalturaFieldMatchCondition();
			case ConditionType::ASSET_PROPERTIES_COMPARE:
				return new KalturaAssetPropertiesCompareCondition();
			case ConditionType::USER_ROLE:
				return new KalturaUserRoleCondition();
			default:
				return KalturaPluginManager::loadObject('KalturaCondition', $dbObject->getType());
		}
	}
		
	public function __construct()
	{
		parent::__construct("KalturaCondition");	
	}
}
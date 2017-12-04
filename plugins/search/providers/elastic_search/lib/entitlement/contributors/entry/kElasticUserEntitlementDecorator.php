<?php
/**
 * @package plugins.elasticSearch
 * @subpackage lib.entitlement
 */

class kElasticUserEntitlementDecorator implements IKalturaESearchEntryEntitlementDecorator
{

	public static function shouldContribute()
	{
		if(kEntryElasticEntitlement::$userEntitlement)
			return true;

		return false;
	}

	public static function getEntitlementCondition(array $params = array(), $fieldPrefix = '')
	{
		$conditions = array();

		$userEditPreFetchGroupCondition = new kESearchTermsQuery("{$fieldPrefix}entitled_kusers_edit",
			array('index' => ElasticIndexMap::ELASTIC_KUSER_INDEX,'type' => ElasticIndexMap::ELASTIC_KUSER_TYPE,
				'id' => $params['kuserId'],	'path' => 'group_ids'));
		$conditions[] = $userEditPreFetchGroupCondition;
		$userEditCondition = new kESearchTermQuery("{$fieldPrefix}entitled_kusers_edit",$params['kuserId']);
		$conditions[] = $userEditCondition;

		$userPublishPreFetchGroupCondition = new kESearchTermsQuery("{$fieldPrefix}entitled_kusers_publish",
			array('index' => ElasticIndexMap::ELASTIC_KUSER_INDEX,'type' => ElasticIndexMap::ELASTIC_KUSER_TYPE,
				'id' => $params['kuserId'],	'path' => 'group_ids'));
		$conditions[] = $userPublishPreFetchGroupCondition;
		$userPublishCondition = new kESearchTermQuery("{$fieldPrefix}entitled_kusers_publish",$params['kuserId']);
		$conditions[] = $userPublishCondition;

		$userCondition = new kESearchTermQuery("{$fieldPrefix}kuser_id",$params['kuserId']);
		$conditions[] = $userCondition;
		return $conditions;
	}

	public static function applyCondition(&$entryQuery, &$parentEntryQuery)
	{
		$kuserId = kEntryElasticEntitlement::$kuserId;
		if(!$kuserId)
		{
			KalturaLog::log('cannot add user entitlement to elastic without a kuserId - setting kuser id to -1');
			$kuserId = -1;
		}
		$params['kuserId'] = $kuserId;

		if($parentEntryQuery)
		{
			//add parent conditions
			$conditions = self::getEntitlementCondition($params, 'parent_entry.');
			foreach ($conditions as $condition)
			{
				$parentEntryQuery->addToShould($condition);
			}
		}
		$conditions = self::getEntitlementCondition($params);
		foreach ($conditions as $condition)
		{
			$entryQuery->addToShould($condition);
		}
	}
}

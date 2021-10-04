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

        $indexName = kBaseESearch::getElasticIndexNamePerPartner( ElasticIndexMap::ELASTIC_KUSER_INDEX, kCurrentContext::getCurrentPartnerId());
		$conditions[] = ESearchGroupUserItem::createGroupIdsTermsQuery("{$fieldPrefix}entitled_kusers_edit", $params['kuserId'], $indexName);
		$userEditCondition = new kESearchTermQuery("{$fieldPrefix}entitled_kusers_edit",$params['kuserId']);
		$conditions[] = $userEditCondition;

		$conditions[] = ESearchGroupUserItem::createGroupIdsTermsQuery("{$fieldPrefix}entitled_kusers_publish", $params['kuserId'], $indexName);
		$userPublishCondition = new kESearchTermQuery("{$fieldPrefix}entitled_kusers_publish",$params['kuserId']);
		$conditions[] = $userPublishCondition;

		$conditions[] = ESearchGroupUserItem::createGroupIdsTermsQuery("{$fieldPrefix}entitled_kusers_view", $params['kuserId'], $indexName);
		$userViewCondition = new kESearchTermQuery("{$fieldPrefix}entitled_kusers_view",$params['kuserId']);
		$conditions[] = $userViewCondition;

		$conditions[] = ESearchGroupUserItem::createGroupIdsTermsQuery("{$fieldPrefix}kuser_id", $params['kuserId'], $indexName);
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

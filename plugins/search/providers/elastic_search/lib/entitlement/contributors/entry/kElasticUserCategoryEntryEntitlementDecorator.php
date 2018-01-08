<?php
/**
 * @package plugins.elasticSearch
 * @subpackage lib.entitlement
 */

class kElasticUserCategoryEntryEntitlementDecorator implements IKalturaESearchEntryEntitlementDecorator
{
	const MAX_CATEGORIES = 512;

	public static function shouldContribute()
	{
		if(kEntryElasticEntitlement::$userCategoryToEntryEntitlement || kEntryElasticEntitlement::$entryInSomeCategoryNoPC)
			return true;

		return false;
	}

	public static function getEntitlementCondition(array $params = array(), $fieldPrefix = '')
	{
		$ids = self::getFormattedCategoryIds($params['category_ids']);
		$fieldName = ESearchBaseCategoryEntryItem::CATEGORY_IDS_MAPPING_FIELD;
		$condition = new kESearchTermsQuery("{$fieldPrefix}{$fieldName}", $ids);
		return $condition;
	}

	public static function applyCondition(&$entryQuery, &$parentEntryQuery)
	{
		$kuserId = kEntryElasticEntitlement::$kuserId;
		if(!$kuserId)
		{
			KalturaLog::log('cannot add userCategory to entry entitlement to elastic without a kuserId - setting kuser id to -1');
			$kuserId = -1;
		}
		//get category ids with $privacyContext
		$categories = self::getUserCategories($kuserId, kEntryElasticEntitlement::$privacyContext, kEntryElasticEntitlement::$privacy);
		if(count($categories) == 0)
			$categories = array(category::CATEGORY_ID_THAT_DOES_NOT_EXIST);

		$params['category_ids'] = $categories;

		if($parentEntryQuery)
		{
			$condition = self::getEntitlementCondition($params, 'parent_entry.');
			$parentEntryQuery->addToShould($condition);
		}

		$condition = self::getEntitlementCondition($params);
		$entryQuery->addToShould($condition);
	}

	private static function getUserCategories($kuserId, $privacyContext = null, $privacy = null)
	{
		$params = array(
			'index' => ElasticIndexMap::ELASTIC_CATEGORY_INDEX,
			'type' => ElasticIndexMap::ELASTIC_CATEGORY_TYPE,
			'size' => self::MAX_CATEGORIES
		);
		$body = array();
		$body['_source'] = false;

		$mainBool = new kESearchBoolQuery();
		$partnerStatus = elasticSearchUtils::formatPartnerStatus(kEntryElasticEntitlement::$partnerId, CategoryStatus::ACTIVE);
		$partnerStatusQuery = new kESearchTermQuery('partner_status', $partnerStatus);
		$mainBool->addToFilter($partnerStatusQuery);

		$conditionsBoolQuery = new kESearchBoolQuery();

		$userGroupsQuery = new kESearchTermsQuery('kuser_ids',array(
			'index' => ElasticIndexMap::ELASTIC_KUSER_INDEX,
			'type' => ElasticIndexMap::ELASTIC_KUSER_TYPE,
			'id' => $kuserId,
			'path' => 'group_ids'
		));
		$conditionsBoolQuery->addToShould($userGroupsQuery);
		$userQuery = new kESearchTermQuery('kuser_ids', $kuserId);
		$conditionsBoolQuery->addToShould($userQuery);

		if(kEntryElasticEntitlement::$entryInSomeCategoryNoPC)
		{
			$noPcQuery = new kESearchBoolQuery();
			$pcExistQuery = new kESearchExistsQuery('privacy_context');
			$noPcQuery->addToMustNot($pcExistQuery);
			$conditionsBoolQuery->addToShould($noPcQuery);
		}

		$privacyContexts = null;
		if (!$privacyContext || trim($privacyContext) == '')
			$privacyContexts = array(kEntitlementUtils::getDefaultContextString(kEntryElasticEntitlement::$partnerId));
		else
		{
			$privacyContexts = explode(',', $privacyContext);
			$privacyContexts = kEntitlementUtils::addPrivacyContextsPrefix( $privacyContexts, kEntryElasticEntitlement::$partnerId );
		}

		$privacyContexts = array_map('elasticSearchUtils::formatSearchTerm', $privacyContexts);
		$privacyContextsQuery = new kESearchTermsQuery('privacy_contexts',$privacyContexts);
		$mainBool->addToFilter($privacyContextsQuery);

		if($privacy) //privacy is an array
		{
			$privacy = array_map('elasticSearchUtils::formatSearchTerm', $privacy);
			$privacyQuery = new kESearchTermsQuery('privacy', $privacy);
			$conditionsBoolQuery->addToShould($privacyQuery);
		}

		$mainBool->addToFilter($conditionsBoolQuery);
		$body['query'] = $mainBool->getFinalQuery();
		$params['body'] = $body;
		$elasticClient = new elasticClient();
		$results = $elasticClient->search($params);
		$categories = $results['hits']['hits'];
		$categoryIds = array();

		foreach ($categories as $category)
		{
			$categoryIds[] = $category['_id'];
		}
		return $categoryIds;
	}

	private static function getFormattedCategoryIds($categoryIds)
	{
		$searchIds = array();
		foreach ($categoryIds as $categoryId)
		{
			$searchIds[] = elasticSearchUtils::formatCategoryIdStatus($categoryId, CategoryEntryStatus::ACTIVE);
			$searchIds[] = elasticSearchUtils::formatCategoryIdStatus($categoryId, CategoryEntryStatus::PENDING);
		}
		return $searchIds;
	}

}

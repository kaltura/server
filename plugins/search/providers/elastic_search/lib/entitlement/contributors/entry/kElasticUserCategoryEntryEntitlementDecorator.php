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
		$condition = new kESearchBoolQuery();
		//members
		$ids = self::getFormattedCategoryIds($params['category_ids']);
		$idsFieldName = ESearchBaseCategoryEntryItem::CATEGORY_IDS_MAPPING_FIELD;
		$idsCondition = new kESearchTermsQuery("{$fieldPrefix}{$idsFieldName}", $ids);
		$condition->addToShould($idsCondition);
		//privacy_by_contexts
		$privacyByContexts = array();
		$privacyContexts = self::getPrivacyContexts(kEntryElasticEntitlement::$privacyContext);
		foreach ($privacyContexts as $privacyContext)
		{
			foreach (kEntryElasticEntitlement::$privacy as $privacyValue)
			{
				$privacyByContexts[] = elasticSearchUtils::formatSearchTerm($privacyContext . kEntitlementUtils::TYPE_SEPERATOR . $privacyValue);
			}
		}
		$pcFieldName = ESearchEntryFieldName::PRIVACY_BY_CONTEXTS;
		$privacyByContextCondition = new kESearchTermsQuery("{$fieldPrefix}{$pcFieldName}", $privacyByContexts);
		$condition->addToShould($privacyByContextCondition);

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
		$categories = self::getUserCategories($kuserId, kEntryElasticEntitlement::$privacyContext);
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

	protected static function getPrivacyContexts($privacyContext)
	{
		$privacyContexts = null;
		if (!$privacyContext || trim($privacyContext) == '')
		{
			$privacyContexts = array(kEntitlementUtils::getDefaultContextString(kEntryElasticEntitlement::$partnerId));
		}
		else
		{
			$privacyContexts = explode(',', $privacyContext);
			$privacyContexts = kEntitlementUtils::addPrivacyContextsPrefix($privacyContexts, kEntryElasticEntitlement::$partnerId);
		}

		$privacyContexts = array_map('elasticSearchUtils::formatSearchTerm', $privacyContexts);
		return $privacyContexts;
	}

	protected static function getUserCategories($kuserId, $privacyContext = null)
	{
		$maxUserCategories = kConf::get('maxUserCategories', 'elastic', self::MAX_CATEGORIES);

        $indexName = kBaseESearch::getElasticIndexNamePerPartner( ElasticIndexMap::ELASTIC_CATEGORY_INDEX, kCurrentContext::getCurrentPartnerId());

        $params = array(
			'index' => $indexName,
			'type' => ElasticIndexMap::ELASTIC_CATEGORY_TYPE,
			'size' => $maxUserCategories
		);
		$body = array();
		$body['_source'] = false;

		$mainBool = new kESearchBoolQuery();
		$partnerStatus = elasticSearchUtils::formatPartnerStatus(kEntryElasticEntitlement::$partnerId, CategoryStatus::ACTIVE);
		$partnerStatusQuery = new kESearchTermQuery('partner_status', $partnerStatus);
		$mainBool->addToFilter($partnerStatusQuery);

		if (count(kEntryElasticEntitlement::$filteredCategoryIds))
		{
			$filteredCategoryIdsQuery = new kESearchTermsQuery('_id', kEntryElasticEntitlement::$filteredCategoryIds);
			$mainBool->addToFilter($filteredCategoryIdsQuery);
		}

		$conditionsBoolQuery = new kESearchBoolQuery();

        $indexName = kBaseESearch::getElasticIndexNamePerPartner( ElasticIndexMap::ELASTIC_KUSER_INDEX, kCurrentContext::getCurrentPartnerId());


        $userGroupsQuery = new kESearchTermsQuery(ESearchCategoryFieldName::KUSER_IDS,array(
			'index' => $indexName,
			'type' => ElasticIndexMap::ELASTIC_KUSER_TYPE,
			'id' => $kuserId,
			'path' => 'group_ids'
		));
		$conditionsBoolQuery->addToShould($userGroupsQuery);
		$userQuery = new kESearchTermQuery(ESearchCategoryFieldName::KUSER_IDS, $kuserId);
		$conditionsBoolQuery->addToShould($userQuery);

		if(kEntryElasticEntitlement::$entryInSomeCategoryNoPC)
		{
			$noPcQuery = new kESearchBoolQuery();
			$pcExistQuery = new kESearchExistsQuery('privacy_context');
			$noPcQuery->addToMustNot($pcExistQuery);
			$conditionsBoolQuery->addToShould($noPcQuery);
		}

		$privacyContexts = self::getPrivacyContexts($privacyContext);
		$privacyContextsQuery = new kESearchTermsQuery('privacy_contexts',$privacyContexts);
		$mainBool->addToFilter($privacyContextsQuery);

		//fetch only categories with privacy MEMBERS_ONLY
		//categories with privacy ALL/AUTHENTICATED_USERS will be handled with privacy_by_contexts
		$privacy = category::formatPrivacy(PrivacyType::MEMBERS_ONLY, kEntryElasticEntitlement::$partnerId);
		$privacyQuery = new kESearchTermQuery('privacy', elasticSearchUtils::formatSearchTerm($privacy));
		$mainBool->addToFilter($privacyQuery);

		$mainBool->addToFilter($conditionsBoolQuery);
		$body['query'] = $mainBool->getFinalQuery();
		$params['body'] = $body;
		//order categories by updated at
		$params['body']['sort'] = array('updated_at' => 'desc');
		$elasticClient = new elasticClient();
		$results = $elasticClient->search($params, true);
		$categories = $results['hits']['hits'];

		$categoriesCount = $results['hits']['total'];
		if ($categoriesCount > $maxUserCategories)
		{
			KalturaLog::debug("More then max user categories found. userId[$kuserId] count[$categoriesCount]");
		}

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

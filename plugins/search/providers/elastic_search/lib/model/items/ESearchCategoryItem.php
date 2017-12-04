<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
class ESearchCategoryItem extends ESearchItem
{

	/**
	 * @var ESearchCategoryFieldName
	 */
	protected $fieldName;

	/**
	 * @var string
	 */
	protected $searchTerm;

	private static $allowed_search_types_for_field = array(
		'name' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH),
		'full_name' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH),
		'description' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH),
		'privacy' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH),
		'privacy_context' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::EXISTS"=> ESearchItemType::EXISTS),
		'privacy_contexts' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::EXISTS"=> ESearchItemType::EXISTS),
		'kuser_ids' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, "ESearchItemType::EXISTS"=> ESearchItemType::EXISTS),
		'parent_id' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH,"ESearchItemType::EXISTS"=> ESearchItemType::EXISTS),
		'depth' => array('ESearchItemType::RANGE' => ESearchItemType::RANGE),
		'full_ids' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH),
		'tags' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::EXISTS"=> ESearchItemType::EXISTS),
		'display_in_search' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH),
		'inheritance_type' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH),
		'kuser_id' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH),
		'reference_id' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::EXISTS"=> ESearchItemType::EXISTS),
		'inherited_parent_id' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::EXISTS"=> ESearchItemType::EXISTS),
		'moderation' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, "ESearchItemType::EXISTS"=> ESearchItemType::EXISTS),
		'contribution_policy' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, "ESearchItemType::EXISTS"=> ESearchItemType::EXISTS),
		'entries_count' => array('ESearchItemType::RANGE' => ESearchItemType::RANGE),
		'direct_entries_count' => array('ESearchItemType::RANGE' => ESearchItemType::RANGE),
		'direct_sub_categories_count' => array('ESearchItemType::RANGE' => ESearchItemType::RANGE),
		'members_count' => array('ESearchItemType::RANGE' => ESearchItemType::RANGE),
		'pending_members_count' => array('ESearchItemType::RANGE' => ESearchItemType::RANGE),
		'pending_entries_count' => array('ESearchItemType::RANGE' => ESearchItemType::RANGE),
		'created_at' => array('ESearchItemType::RANGE' => ESearchItemType::RANGE),
		'updated_at' => array('ESearchItemType::RANGE' => ESearchItemType::RANGE),
	);

	private static $multiLanguageFields = array(
		ESearchCategoryFieldName::CATEGORY_NAME,
		ESearchCategoryFieldName::CATEGORY_DESCRIPTION,
	);

	/**
	 * @return ESearchCategoryFieldName
	 */
	public function getFieldName()
	{
		return $this->fieldName;
	}

	/**
	 * @param ESearchCategoryFieldName $fieldName
	 */
	public function setFieldName($fieldName)
	{
		$this->fieldName = $fieldName;
	}

	/**
	 * @return string
	 */
	public function getSearchTerm()
	{
		return $this->searchTerm;
	}

	/**
	 * @param string $searchTerm
	 */
	public function setSearchTerm($searchTerm)
	{
		$this->searchTerm = $searchTerm;
	}

	public static function getAllowedSearchTypesForField()
	{
		return array_merge(self::$allowed_search_types_for_field, parent::getAllowedSearchTypesForField());
	}

	/**
	 * @param $eSearchItemsArr
	 * @param $boolOperator
	 * @param ESearchQueryAttributes $queryAttributes
	 * @param null $eSearchOperatorType
	 * @return array
	 */
	public static function createSearchQuery($eSearchItemsArr, $boolOperator, &$queryAttributes, $eSearchOperatorType = null)
	{
		$categoryQuery = array();
		$queryAttributes->setScopeToGlobal();
		$allowedSearchTypes = ESearchCategoryItem::getAllowedSearchTypesForField();
		foreach ($eSearchItemsArr as $categorySearchItem)
		{
			self::createSingleItemSearchQuery($categorySearchItem, $categoryQuery, $allowedSearchTypes, $queryAttributes);
		}

		return $categoryQuery;
	}
	
	public static function createSingleItemSearchQuery($categorySearchItem, &$categoryQuery, $allowedSearchTypes, &$queryAttributes)
	{
		$categorySearchItem->validateItemInput();
		$categorySearchItem->translateSearchTerm();
		switch ($categorySearchItem->getItemType())
		{
			case ESearchItemType::EXACT_MATCH:
				$categoryQuery[] = kESearchQueryManager::getExactMatchQuery($categorySearchItem, $categorySearchItem->getFieldName(), $allowedSearchTypes, $queryAttributes);
				break;
			case ESearchItemType::PARTIAL:
				$categoryQuery[] = kESearchQueryManager::getPartialQuery($categorySearchItem, $categorySearchItem->getFieldName(), $queryAttributes);
				break;
			case ESearchItemType::STARTS_WITH:
				$categoryQuery[] = kESearchQueryManager::getPrefixQuery($categorySearchItem, $categorySearchItem->getFieldName(), $allowedSearchTypes, $queryAttributes);
				break;
			case ESearchItemType::EXISTS:
				$categoryQuery[] = kESearchQueryManager::getExistsQuery($categorySearchItem, $categorySearchItem->getFieldName(), $allowedSearchTypes, $queryAttributes);
				break;
			case ESearchItemType::RANGE:
				$categoryQuery[] = kESearchQueryManager::getRangeQuery($categorySearchItem,$categorySearchItem->getFieldName(), $allowedSearchTypes, $queryAttributes);
				break;
			default:
				KalturaLog::log("Undefined item type[".$categorySearchItem->getItemType()."]");
		}
	}

	protected function translateSearchTerm()
	{
		$fieldName = $this->getFieldName();
		switch ($fieldName)
		{
			case ESearchCategoryFieldName::CATEGORY_PRIVACY:
				$this->setSearchTerm(category::formatPrivacy($this->getSearchTerm(), kCategoryElasticEntitlement::$partnerId));
				break;
			case ESearchCategoryFieldName::CATEGORY_PRIVACY_CONTEXT:
			case ESearchCategoryFieldName::CATEGORY_PRIVACY_CONTEXTS:
				$this->setSearchTerm(kEntitlementUtils::getPartnerPrefix(kCategoryElasticEntitlement::$partnerId).$this->getSearchTerm());
				break;
			default:
				return;
		}
	}

	public function shouldAddLanguageSearch()
	{
		if(in_array($this->getFieldName(), self::$multiLanguageFields))
			return true;

		return false;
	}

	public function getItemMappingFieldsDelimiter()
	{
		return elasticSearchUtils::DOT_FIELD_DELIMITER;
	}

}
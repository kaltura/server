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
		'name' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::DOESNT_CONTAIN'=> ESearchItemType::DOESNT_CONTAIN, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH),
		'full_name' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::DOESNT_CONTAIN'=> ESearchItemType::DOESNT_CONTAIN, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH),
		'description' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::DOESNT_CONTAIN'=> ESearchItemType::DOESNT_CONTAIN, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH),
		'privacy' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH,'ESearchItemType::DOESNT_CONTAIN'=> ESearchItemType::DOESNT_CONTAIN),
		'privacy_context' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, 'ESearchItemType::DOESNT_CONTAIN'=> ESearchItemType::DOESNT_CONTAIN),
		'privacy_contexts' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, 'ESearchItemType::DOESNT_CONTAIN'=> ESearchItemType::DOESNT_CONTAIN),
		'kuser_ids' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::DOESNT_CONTAIN'=> ESearchItemType::DOESNT_CONTAIN),
		'parent_id' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH,'ESearchItemType::DOESNT_CONTAIN'=> ESearchItemType::DOESNT_CONTAIN),
		'depth' => array('ESearchItemType::RANGE' => ESearchItemType::RANGE),
		'full_ids' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::DOESNT_CONTAIN'=> ESearchItemType::DOESNT_CONTAIN),
		'tags' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, 'ESearchItemType::DOESNT_CONTAIN'=> ESearchItemType::DOESNT_CONTAIN),
		'display_in_search' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::DOESNT_CONTAIN'=> ESearchItemType::DOESNT_CONTAIN),
		'inheritance_type' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH,'ESearchItemType::DOESNT_CONTAIN'=> ESearchItemType::DOESNT_CONTAIN),
		'kuser_id' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH,'ESearchItemType::DOESNT_CONTAIN'=> ESearchItemType::DOESNT_CONTAIN),
		'reference_id' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, 'ESearchItemType::DOESNT_CONTAIN'=> ESearchItemType::DOESNT_CONTAIN),
		'inherited_parent_id' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, 'ESearchItemType::DOESNT_CONTAIN'=> ESearchItemType::DOESNT_CONTAIN),
		'moderation' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::DOESNT_CONTAIN'=> ESearchItemType::DOESNT_CONTAIN),
		'contribution_policy' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::DOESNT_CONTAIN'=> ESearchItemType::DOESNT_CONTAIN),
		'entries_count' => array('ESearchItemType::RANGE' => ESearchItemType::RANGE),
		'direct_entries_count' => array('ESearchItemType::RANGE' => ESearchItemType::RANGE),
		'direct_sub_categories_count' => array('ESearchItemType::RANGE' => ESearchItemType::RANGE),
		'members_count' => array('ESearchItemType::RANGE' => ESearchItemType::RANGE),
		'pending_members_count' => array('ESearchItemType::RANGE' => ESearchItemType::RANGE),
		'pending_entries_count' => array('ESearchItemType::RANGE' => ESearchItemType::RANGE),
		'created_at' => array('ESearchItemType::RANGE' => ESearchItemType::RANGE),
		'updated_at' => array('ESearchItemType::RANGE' => ESearchItemType::RANGE),
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

	public function getType()
	{
		return 'category';
	}

	public static function getAllowedSearchTypesForField()
	{
		return array_merge(self::$allowed_search_types_for_field, parent::getAllowedSearchTypesForField());
	}
	
	public static function createSearchQuery($eSearchItemsArr, $boolOperator, $eSearchOperatorType = null)
	{
		$categoryQuery = array();

		$allowedSearchTypes = ESearchCategoryItem::getAllowedSearchTypesForField();
		foreach ($eSearchItemsArr as $categorySearchItem)
		{
			self::createSingleItemSearchQuery($categorySearchItem, $categoryQuery, $allowedSearchTypes);
		}
		return $categoryQuery;
	}
	
	public static function createSingleItemSearchQuery($categorySearchItem, &$categoryQuery, $allowedSearchTypes)
	{
		$categorySearchItem->validateItemInput();
		$categorySearchItem->translateSearchTerm();
		switch ($categorySearchItem->getItemType())
		{
			case ESearchItemType::EXACT_MATCH:
				$categoryQuery[] = kESearchQueryManager::getExactMatchQuery($categorySearchItem, $categorySearchItem->getFieldName(), $allowedSearchTypes);
				break;
			case ESearchItemType::PARTIAL:
				$categoryQuery[] = kESearchQueryManager::getMultiMatchQuery($categorySearchItem, $categorySearchItem->getFieldName(), false);
				break;
			case ESearchItemType::STARTS_WITH:
				$categoryQuery[] = kESearchQueryManager::getPrefixQuery($categorySearchItem, $categorySearchItem->getFieldName(), $allowedSearchTypes);
				break;
			case ESearchItemType::EXISTS:
				$categoryQuery[] = kESearchQueryManager::getExistsQuery($categorySearchItem, $categorySearchItem->getFieldName(), $allowedSearchTypes);
				break;
			case ESearchItemType::RANGE:
				$categoryQuery[] = kESearchQueryManager::getRangeQuery($categorySearchItem,$categorySearchItem->getFieldName(), $allowedSearchTypes);
				break;
			default:
				KalturaLog::log("Undefined item type[".$categorySearchItem->getItemType()."]");
		}
	}

	protected function validateItemInput()
	{
		$allowedSearchTypes = self::getAllowedSearchTypesForField();
		$this->validateAllowedSearchTypes($allowedSearchTypes, $this->getFieldName());
		$this->validateEmptySearchTerm($this->getFieldName(), $this->getSearchTerm());
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
}
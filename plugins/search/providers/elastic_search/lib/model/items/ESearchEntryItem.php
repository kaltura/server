<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
class ESearchEntryItem extends ESearchItem
{

	/**
	 * @var ESearchEntryFieldName
	 */
	protected $fieldName;

	/**
	 * @var string
	 */
	protected $searchTerm;

	private static $allowed_search_types_for_field = array(
		'_id' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN, ESearchUnifiedItem::UNIFIED),
		'name' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN, ESearchUnifiedItem::UNIFIED),
		'description' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN, ESearchUnifiedItem::UNIFIED),
		'tags' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN, ESearchUnifiedItem::UNIFIED),
		'category_ids' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN),
		'puser_id' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN, ESearchUnifiedItem::UNIFIED),
		'creator_puser_id' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN, ESearchUnifiedItem::UNIFIED),
		'start_time' => array('ESearchItemType::RANGE'=>ESearchItemType::RANGE),
		'end_time' => array('ESearchItemType::RANGE'=>ESearchItemType::RANGE),
		'reference_id' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN, ESearchUnifiedItem::UNIFIED),
		'conversion_profile_id' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN, ESearchUnifiedItem::UNIFIED),
		'redirect_entry_id' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN, ESearchUnifiedItem::UNIFIED),
		'entitled_kusers_edit' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN),
		'entitled_kusers_publish' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN),
		'template_entry_id' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH,"ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN, ESearchUnifiedItem::UNIFIED),
		'display_in_search' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN),
		'parent_id' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN, ESearchUnifiedItem::UNIFIED),
		'media_type' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN),
		'source_type' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN),
		'recorded_entry_id' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH,"ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN, ESearchUnifiedItem::UNIFIED),
		'push_publish' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN),
		'length_in_msecs' => array('ESearchItemType::RANGE' => ESearchItemType::RANGE),
		'created_at' => array('ESearchItemType::RANGE' => ESearchItemType::RANGE),
		'updated_at' => array('ESearchItemType::RANGE' => ESearchItemType::RANGE),
		'moderation_status' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN),
	);

	/**
	 * @return ESearchEntryFieldName
	 */
	public function getFieldName()
	{
		return $this->fieldName;
	}

	/**
	 * @param ESearchEntryFieldName $fieldName
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
		return 'entry';
	}

	public static function getAllowedSearchTypesForField()
	{
		return array_merge(self::$allowed_search_types_for_field, parent::getAllowedSearchTypesForField());
	}

	public static function createSearchQuery(array $eSearchItemsArr, $boolOperator, $eSearchOperatorType = null)
	{
		$entryQuery = array();
		$allowedSearchTypes = ESearchEntryItem::getAllowedSearchTypesForField();
		foreach ($eSearchItemsArr as $entrySearchItem)
		{
			self::getSingleItemSearchQuery($entrySearchItem, $entryQuery, $allowedSearchTypes);
		}
		return $entryQuery;
	}

	public static function getSingleItemSearchQuery($entrySearchItem, &$entryQuery, $allowedSearchTypes)
	{
		$entrySearchItem->validateItemInput();
		switch ($entrySearchItem->getItemType())
		{
			case ESearchItemType::EXACT_MATCH:
				$entryQuery[] = kESearchQueryManager::getExactMatchQuery($entrySearchItem, $entrySearchItem->getFieldName(), $allowedSearchTypes);
				break;
			case ESearchItemType::PARTIAL:
				$entryQuery[] = kESearchQueryManager::getMultiMatchQuery($entrySearchItem, $entrySearchItem->getFieldName(), false);
				break;
			case ESearchItemType::STARTS_WITH:
				$entryQuery[] = kESearchQueryManager::getPrefixQuery($entrySearchItem, $entrySearchItem->getFieldName(), $allowedSearchTypes);
				break;
			case ESearchItemType::DOESNT_CONTAIN:
				$entryQuery[]['bool']['must_not'][] = kESearchQueryManager::getDoesntContainQuery($entrySearchItem, $entrySearchItem->getFieldName(), $allowedSearchTypes);
				break;
			case ESearchItemType::RANGE:
				$entryQuery[] = kESearchQueryManager::getRangeQuery($entrySearchItem, $entrySearchItem->getFieldName(), $allowedSearchTypes);
				break;
			default:
				KalturaLog::log("Undefined item type[".$entrySearchItem->getItemType()."]");
		}
	}
	
	protected function validateItemInput()
	{
		$allowedSearchTypes = self::getAllowedSearchTypesForField();
		$this->validateAllowedSearchTypes($allowedSearchTypes, $this->getFieldName());
		$this->validateEmptySearchTerm($this->getFieldName(), $this->getSearchTerm());
	}
	
}

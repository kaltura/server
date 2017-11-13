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
		'_id' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, ESearchUnifiedItem::UNIFIED),
		'name' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, ESearchUnifiedItem::UNIFIED),
		'description' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, 'ESearchItemType::EXISTS'=> ESearchItemType::EXISTS, ESearchUnifiedItem::UNIFIED),
		'tags' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, 'ESearchItemType::EXISTS'=> ESearchItemType::EXISTS, ESearchUnifiedItem::UNIFIED),
		'category_ids' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::EXISTS'=> ESearchItemType::EXISTS),
		'puser_id' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, ESearchUnifiedItem::UNIFIED),
		'creator_puser_id' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, ESearchUnifiedItem::UNIFIED),
		'start_date' => array('ESearchItemType::RANGE'=>ESearchItemType::RANGE, 'ESearchItemType::EXISTS'=> ESearchItemType::EXISTS),
		'end_date' => array('ESearchItemType::RANGE'=>ESearchItemType::RANGE, 'ESearchItemType::EXISTS'=> ESearchItemType::EXISTS),
		'reference_id' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::EXISTS' => ESearchItemType::EXISTS, ESearchUnifiedItem::UNIFIED),
		'conversion_profile_id' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, ESearchUnifiedItem::UNIFIED),
		'redirect_entry_id' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::EXISTS' => ESearchItemType::EXISTS, ESearchUnifiedItem::UNIFIED),
		'entitled_pusers_edit' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::EXISTS' => ESearchItemType::EXISTS),
		'entitled_pusers_publish' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::EXISTS' => ESearchItemType::EXISTS),
		'template_entry_id' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::EXISTS' => ESearchItemType::EXISTS, ESearchUnifiedItem::UNIFIED),
		'display_in_search' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::EXISTS' => ESearchItemType::EXISTS),
		'parent_id' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::EXISTS' => ESearchItemType::EXISTS, ESearchUnifiedItem::UNIFIED),
		'media_type' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH),
		'source_type' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH),
		'recorded_entry_id' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::EXISTS' => ESearchItemType::EXISTS, ESearchUnifiedItem::UNIFIED),
		'push_publish' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::EXISTS' => ESearchItemType::EXISTS),
		'length_in_msecs' => array('ESearchItemType::RANGE' => ESearchItemType::RANGE),
		'created_at' => array('ESearchItemType::RANGE' => ESearchItemType::RANGE),
		'updated_at' => array('ESearchItemType::RANGE' => ESearchItemType::RANGE),
		'moderation_status' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::EXISTS' => ESearchItemType::EXISTS),
		'entry_type' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH),
		'categories' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH),
		'admin_tags' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::EXISTS' => ESearchItemType::EXISTS),
		'credit' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::EXISTS' => ESearchItemType::EXISTS),
		'site_url' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::EXISTS' => ESearchItemType::EXISTS),
		'access_control_id' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::EXISTS' => ESearchItemType::EXISTS),
	);

	private static $multiLanguageFields = array(
		ESearchEntryFieldName::ENTRY_NAME,
		ESearchEntryFieldName::ENTRY_DESCRIPTION,
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

	public static function createSearchQuery($eSearchItemsArr, $boolOperator, &$queryAttributes, $eSearchOperatorType = null)
	{
		$entryQuery = array();
		$allowedSearchTypes = ESearchEntryItem::getAllowedSearchTypesForField();
		foreach ($eSearchItemsArr as $entrySearchItem)
		{
			self::getSingleItemSearchQuery($entrySearchItem, $entryQuery, $allowedSearchTypes, $queryAttributes);
		}
		return $entryQuery;
	}

	public static function getSingleItemSearchQuery($entrySearchItem, &$entryQuery, $allowedSearchTypes, &$queryAttributes)
	{
		$entrySearchItem->validateItemInput();
		switch ($entrySearchItem->getItemType())
		{
			case ESearchItemType::EXACT_MATCH:
				$entryQuery[] = kESearchQueryManager::getExactMatchQuery($entrySearchItem, $entrySearchItem->getFieldName(), $allowedSearchTypes);
				break;
			case ESearchItemType::PARTIAL:
				$entryQuery[] = kESearchQueryManager::getMultiMatchQuery($entrySearchItem, $entrySearchItem->getFieldName(), $queryAttributes);
				break;
			case ESearchItemType::STARTS_WITH:
				$entryQuery[] = kESearchQueryManager::getPrefixQuery($entrySearchItem, $entrySearchItem->getFieldName(), $allowedSearchTypes);
				break;
			case ESearchItemType::EXISTS:
				$entryQuery[] = kESearchQueryManager::getExistsQuery($entrySearchItem, $entrySearchItem->getFieldName(), $allowedSearchTypes);
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

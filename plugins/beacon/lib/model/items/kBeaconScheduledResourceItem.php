<?php
/**
 * @package plugins.beacon
 * @subpackage model.items
 */
class kBeaconScheduledResourceItem extends ESearchItem
{
	/**
	 * @var BeaconScheduledResourceFieldName
	 */
	protected $fieldName;

	/**
	 * @var string
	 */
	protected $searchTerm;

	private static $allowed_search_types_for_field = array(
		BeaconScheduledResourceFieldName::EVENT_TYPE => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH),
		BeaconScheduledResourceFieldName::OBJECT_ID => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH),
		BeaconScheduledResourceFieldName::IS_LOG => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH),
		BeaconScheduledResourceFieldName::STATUS => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH),
		BeaconScheduledResourceFieldName::RECORDING => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::EXISTS' => ESearchItemType::EXISTS),
		BeaconScheduledResourceFieldName::RESOURCE_NAME => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH),
		BeaconScheduledResourceFieldName::UPDATED_AT => array('ESearchItemType::RANGE'=>ESearchItemType::RANGE, 'ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH),
	);

	protected static $field_boost_values = array();

	protected static $searchHistoryFields = array();

	protected static $booleanFields = array(
		BeaconScheduledResourceFieldName::IS_LOG,
	);

	/**
	 * @return BeaconScheduledResourceFieldName
	 */
	public function getFieldName()
	{
		return $this->fieldName;
	}

	/**
	 * @param BeaconScheduledResourceFieldName $fieldName
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
		$scheduledResourceQuery = array();
		$allowedSearchTypes = kBeaconScheduledResourceItem::getAllowedSearchTypesForField();
		foreach ($eSearchItemsArr as $scheduledResourceSearchItem)
		{
			$scheduledResourceSearchItem->getSingleItemSearchQuery($scheduledResourceQuery, $allowedSearchTypes, $queryAttributes);
		}

		return $scheduledResourceQuery;
	}

	/**
	 * @param $scheduledResourceQuery
	 * @param $allowedSearchTypes
	 * @param $queryAttributes ESearchQueryAttributes
	 */
	public function getSingleItemSearchQuery(&$scheduledResourceQuery, $allowedSearchTypes, &$queryAttributes)
	{
		$this->validateItemInput();
		$subQuery = null;
		switch ($this->getItemType())
		{
			case ESearchItemType::EXACT_MATCH:
				$subQuery = kESearchQueryManager::getExactMatchQuery($this, $this->getFieldName(), $allowedSearchTypes, $queryAttributes);
				break;
			case ESearchItemType::PARTIAL:
				$subQuery = kESearchQueryManager::getPartialQuery($this, $this->getFieldName(), $queryAttributes);
				break;
			case ESearchItemType::STARTS_WITH:
				$subQuery = kESearchQueryManager::getPrefixQuery($this, $this->getFieldName(), $allowedSearchTypes, $queryAttributes);
				break;
			case ESearchItemType::EXISTS:
				$subQuery = kESearchQueryManager::getExistsQuery($this, $this->getFieldName(), $allowedSearchTypes, $queryAttributes);
				break;
			case ESearchItemType::RANGE:
				$subQuery = kESearchQueryManager::getRangeQuery($this, $this->getFieldName(), $allowedSearchTypes, $queryAttributes);
				break;
			default:
				KalturaLog::log("Undefined item type[" . $this->getItemType() . "]");
				$data = array('itemType' => $this->getItemType(), 'fieldName' => $this->getFieldName());
				throw new kESearchException('Type of search [' . $this->getItemType() . '] not allowed on specific field 
				[' . $this->getFieldName() . ']', kESearchException::SEARCH_TYPE_NOT_ALLOWED_ON_FIELD, $data);
		}

		if($subQuery)
		{
			$scheduledResourceQuery[] = $subQuery;
		}
	}

	public function getItemMappingFieldsDelimiter()
	{
		return elasticSearchUtils::DOT_FIELD_DELIMITER;
	}

	public function shouldAddLanguageSearch()
	{
		return false;
	}
}

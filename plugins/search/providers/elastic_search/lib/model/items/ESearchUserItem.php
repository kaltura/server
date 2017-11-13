<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
class ESearchUserItem extends ESearchItem
{

	/**
	 * @var ESearchUserFieldName
	 */
	protected $fieldName;

	/**
	 * @var string
	 */
	protected $searchTerm;

	private static $allowed_search_types_for_field = array(
		'kuser_type' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH),
		'role_ids' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH),
		'permission_names' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH),
		'last_name' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH),
		'first_name' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH),
		'screen_name' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH),
		'email' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH),
		'tags' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::EXISTS"=> ESearchItemType::EXISTS),
		'group_ids' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH),
		'updated_at' => array('ESearchItemType::RANGE'=>ESearchItemType::RANGE),
		'created_at' => array('ESearchItemType::RANGE'=>ESearchItemType::RANGE),
	);

	private static $multiLanguageFields = array();

	/**
	 * @return ESearchUserFieldName
	 */
	public function getFieldName()
	{
		return $this->fieldName;
	}

	/**
	 * @param ESearchUserFieldName $fieldName
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
		return 'user';
	}

	public static function getAllowedSearchTypesForField()
	{
		return array_merge(self::$allowed_search_types_for_field, parent::getAllowedSearchTypesForField());
	}

	public static function createSearchQuery($eSearchItemsArr, $boolOperator, &$queryAttributes, $eSearchOperatorType = null)
	{
		$userQuery = array();
		$allowedSearchTypes = ESearchUserItem::getAllowedSearchTypesForField();
		foreach ($eSearchItemsArr as $userSearchItem)
		{
			self::getSingleItemSearchQuery($userSearchItem, $userQuery, $allowedSearchTypes, $queryAttributes);
		}
		return $userQuery;
	}

	private static function getSingleItemSearchQuery($userSearchItem, &$userQuery, $allowedSearchTypes, &$queryAttributes)
	{
		switch ($userSearchItem->getItemType())
		{
			case ESearchItemType::EXACT_MATCH:
				$userQuery[] = kESearchQueryManager::getExactMatchQuery($userSearchItem, $userSearchItem->getFieldName(), $allowedSearchTypes);
				break;
			case ESearchItemType::PARTIAL:
				$userQuery[] = kESearchQueryManager::getMultiMatchQuery($userSearchItem, $userSearchItem->getFieldName(), $queryAttributes);
				break;
			case ESearchItemType::STARTS_WITH:
				$userQuery[] = kESearchQueryManager::getPrefixQuery($userSearchItem, $userSearchItem->getFieldName(), $allowedSearchTypes);
				break;
			case ESearchItemType::EXISTS:
				$userQuery[] = kESearchQueryManager::getExistsQuery($userSearchItem, $userSearchItem->getFieldName(), $allowedSearchTypes);
				break;
			case ESearchItemType::RANGE:
				$userQuery[] = kESearchQueryManager::getRangeQuery($userSearchItem, $userSearchItem->getFieldName(), $allowedSearchTypes);
				break;
			default:
				KalturaLog::log("Undefined item type[".$userSearchItem->getItemType()."]");
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

	}

}
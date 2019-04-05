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

	/**
	 * @var GroupUserCreationMode
	 */
	protected $creationMode;

	protected static $searchHistoryFields = array(
		ESearchUserFieldName::SCREEN_NAME,
		ESearchUserFieldName::FIRST_NAME,
		ESearchUserFieldName::LAST_NAME,
		ESearchUserFieldName::TAGS,
		ESearchUserFieldName::PUSER_ID,
	);

	private static $allowed_search_types_for_field = array(
		ESearchUserFieldName::TYPE => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH),
		ESearchUserFieldName::ROLE_IDS => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH),
		ESearchUserFieldName::PERMISSION_NAMES => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH),
		ESearchUserFieldName::LAST_NAME => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH),
		ESearchUserFieldName::FIRST_NAME => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH),
		ESearchUserFieldName::SCREEN_NAME => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH),
		ESearchUserFieldName::EMAIL => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH),
		ESearchUserFieldName::TAGS => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::EXISTS"=> ESearchItemType::EXISTS),
		ESearchUserFieldName::GROUP_IDS => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH),
		ESearchUserFieldName::UPDATED_AT => array('ESearchItemType::RANGE'=>ESearchItemType::RANGE),
		ESearchUserFieldName::CREATED_AT => array('ESearchItemType::RANGE'=>ESearchItemType::RANGE),
		ESearchUserFieldName::PUSER_ID => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH),
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

	public static function getAllowedSearchTypesForField()
	{
		return array_merge(self::$allowed_search_types_for_field, parent::getAllowedSearchTypesForField());
	}

	/**
	 * @param $eSearchItemsArr
	 * @param $boolOperator
	 * @param $queryAttributes
	 * @param null $eSearchOperatorType
	 * @return array
	 */
	public static function createSearchQuery($eSearchItemsArr, $boolOperator, &$queryAttributes, $eSearchOperatorType = null)
	{
		$userQuery = array();
		$allowedSearchTypes = ESearchUserItem::getAllowedSearchTypesForField();
		$queryAttributes->getQueryHighlightsAttributes()->setScopeToGlobal();
		foreach ($eSearchItemsArr as $userSearchItem)
		{
			$userSearchItem->getSingleItemSearchQuery($userQuery, $allowedSearchTypes, $queryAttributes);
		}

		return $userQuery;
	}

	public function getSingleItemSearchQuery(&$userQuery, $allowedSearchTypes, &$queryAttributes)
	{
		$this->validateItemInput();
		$subQuery = null;
		switch ($this->getItemType())
		{
			case ESearchItemType::EXACT_MATCH:
				$subQuery = $this->getUserExactMatchQuery($allowedSearchTypes, $queryAttributes);
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
				KalturaLog::log("Undefined item type[".$this->getItemType()."]");
		}

		if($subQuery)
			$userQuery[] = $subQuery;
	}


	protected function getUserExactMatchQuery($allowedSearchTypes, &$queryAttributes)
	{
		if($this->shouldAddCreationModeSearch())
		{
			return $this->getGroupIdExactMatchWithCreationMode($allowedSearchTypes, $queryAttributes);
		}

		return kESearchQueryManager::getExactMatchQuery($this, $this->getFieldName(), $allowedSearchTypes, $queryAttributes);
	}

	private function shouldAddCreationModeSearch()
	{
		$creationMode = $this->getCreationMode();
		if(in_array($this->getFieldName(), array(ESearchUserFieldName::GROUP_IDS)) &&  isset($creationMode))
		{
			return true;
		}

		return false;
	}

	private function getGroupIdExactMatchWithCreationMode($allowedSearchTypes, &$queryAttributes)
	{
		$originalTerm = $this->getSearchTerm();
		$boolQuery = new kESearchBoolQuery();
		$creationMode = $this->getCreationMode();

		if(!is_null($creationMode))
		{
			$this->setSearchTerm(elasticSearchUtils::formatCreationMode($originalTerm, $creationMode));
			$creationModeQuery = kESearchQueryManager::getExactMatchQuery($this, "creation_modes", $allowedSearchTypes, $queryAttributes);
			$boolQuery->addToFilter($creationModeQuery);
		}

		$this->setSearchTerm($originalTerm);

		return $boolQuery;
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

	/**
	 * @return GroupUserCreationMode
	 */
	public function getCreationMode()
	{
		return $this->creationMode;
	}

	/**
	 * @param GroupUserCreationMode $creationMode
	 */
	public function setCreationMode($creationMode)
	{
		$this->creationMode = $creationMode;
	}


}
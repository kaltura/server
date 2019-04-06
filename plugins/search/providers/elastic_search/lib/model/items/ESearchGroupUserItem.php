<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
class ESearchGroupUserItem extends ESearchItem
{
	/**
	 * @var ESearchGroupUserFieldName
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

	private static $allowed_search_types_for_field = array(
		ESearchGroupUserFieldName::GROUP_USER_DATA => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH),
	);

	/**
	 * @return ESearchGroupUserFieldName
	 */
	public function getFieldName()
	{
		return $this->fieldName;
	}

	/**
	 * @param ESearchGroupUserFieldName $fieldName
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

	public static function getAllowedSearchTypesForField()
	{
		return array_merge(self::$allowed_search_types_for_field, parent::getAllowedSearchTypesForField());
	}

	public static function createSearchQuery($eSearchItemsArr, $boolOperator, &$queryAttributes, $eSearchOperatorType = null)
	{
		$groupUserQuery = array();
		$allowedSearchTypes = ESearchGroupUserItem::getAllowedSearchTypesForField();
		$queryAttributes->getQueryHighlightsAttributes()->setScopeToGlobal();
		foreach ($eSearchItemsArr as $groupUserSearchItem)
		{
			$groupUserSearchItem->getSingleItemSearchQuery($groupUserQuery, $allowedSearchTypes, $queryAttributes);
		}

		return $groupUserQuery;
	}

	public function getSingleItemSearchQuery(&$groupUserQuery, $allowedSearchTypes, &$queryAttributes)
	{
		$this->validateItemInput();
		switch ($this->getItemType())
		{
			case ESearchItemType::EXACT_MATCH:
				$groupUserQuery[] = $this->getUserExactMatchQuery($allowedSearchTypes, $queryAttributes);
				break;
			default:
				KalturaLog::log("Undefined item type[".$this->getItemType()."]");
		}
	}

	protected function getUserExactMatchQuery($allowedSearchTypes, &$queryAttributes)
	{
		if($this->shouldAddCreationModeSearch())
		{
			return $this->getGroupIdExactMatchWithCreationMode($allowedSearchTypes, $queryAttributes);
		}

		return $this->getGroupIdExactMatchWithoutCreationMode($allowedSearchTypes, $queryAttributes);
	}

	private function shouldAddCreationModeSearch()
	{
		$creationMode = $this->getCreationMode();
		if(in_array($this->getFieldName(), array(ESearchGroupUserFieldName::GROUP_USER_DATA)) &&  isset($creationMode))
		{
			return true;
		}

		return false;
	}

	private function getGroupIdExactMatchWithCreationMode($allowedSearchTypes, &$queryAttributes)
	{
		$originalTerm = $this->getSearchTerm();
		$creationMode = $this->getCreationMode();

		$this->setSearchTerm(elasticSearchUtils::formatGroupIdCreationMode($originalTerm, $creationMode));
		$creationModeQuery = kESearchQueryManager::getExactMatchQuery($this,  $this->getFieldName(), $allowedSearchTypes, $queryAttributes);
		$this->setSearchTerm($originalTerm);

		return $creationModeQuery;
	}

	private function getGroupIdExactMatchWithoutCreationMode($allowedSearchTypes, &$queryAttributes)
	{
		$originalTerm = $this->getSearchTerm();
		$boolQuery = new kESearchBoolQuery();

		$this->setSearchTerm(elasticSearchUtils::formatGroupIdCreationMode($originalTerm, GroupUserCreationMode::MANUAL));
		$creationModeManualQuery = kESearchQueryManager::getExactMatchQuery($this,  $this->getFieldName(), $allowedSearchTypes, $queryAttributes);
		$boolQuery->addToShould($creationModeManualQuery);

		$this->setSearchTerm(elasticSearchUtils::formatGroupIdCreationMode($originalTerm, GroupUserCreationMode::AUTOMATIC));
		$creationModeAutomaticQuery = kESearchQueryManager::getExactMatchQuery($this,  $this->getFieldName(), $allowedSearchTypes, $queryAttributes);
		$boolQuery->addToShould($creationModeAutomaticQuery);

		$this->setSearchTerm($originalTerm);

		return $boolQuery;
	}

	public function shouldAddLanguageSearch()
	{
		return false;
	}

	public function getItemMappingFieldsDelimiter()
	{

	}



}
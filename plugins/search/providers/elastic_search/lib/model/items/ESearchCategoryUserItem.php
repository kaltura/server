<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
class ESearchCategoryUserItem extends ESearchItem
{

	/**
	 * @var ESearchCategoryUserFieldName
	 */
	protected $fieldName;

	/**
	 * @var string
	 */
	protected $searchTerm;

	/**
	 * @var CategoryKuserPermissionLevel
	 */
	protected $permissionLevel;

	/**
	 * @var string
	 */
	protected $permissionName;

	private static $allowed_search_types_for_field = array(
		ESearchCategoryUserFieldName::USER_ID => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH),
	);

	/**
	 * @return ESearchCategoryUserFieldName
	 */
	public function getFieldName()
	{
		return $this->fieldName;
	}

	/**
	 * @param ESearchCategoryUserFieldName $fieldName
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
	 * @return CategoryKuserPermissionLevel
	 */
	public function getPermissionLevel()
	{
		return $this->permissionLevel;
	}

	/**
	 * @param CategoryKuserPermissionLevel $permissionLevel
	 */
	public function setPermissionLevel($permissionLevel)
	{
		$this->permissionLevel = $permissionLevel;
	}

	/**
	 * @return string
	 */
	public function getPermissionName()
	{
		return $this->permissionName;
	}

	/**
	 * @param string $permissionName
	 */
	public function setPermissionName($permissionName)
	{
		$this->permissionName = $permissionName;
	}

	public static function getAllowedSearchTypesForField()
	{
		return array_merge(self::$allowed_search_types_for_field, parent::getAllowedSearchTypesForField());
	}

	public static function createSearchQuery($eSearchItemsArr, $boolOperator, &$queryAttributes, $eSearchOperatorType = null)
	{
		$categoryUserQuery = array();
		$allowedSearchTypes = ESearchCategoryUserItem::getAllowedSearchTypesForField();
		$queryAttributes->getQueryHighlightsAttributes()->setScopeToGlobal();
		foreach ($eSearchItemsArr as $categoryUserSearchItem)
		{
			$categoryUserSearchItem->getSingleItemSearchQuery($categoryUserQuery, $allowedSearchTypes, $queryAttributes);
		}

		return $categoryUserQuery;
	}

	public function getSingleItemSearchQuery(&$categoryUserQuery, $allowedSearchTypes, &$queryAttributes)
	{
		$this->validateItemInput();
		switch ($this->getItemType())
		{
			case ESearchItemType::EXACT_MATCH:
				$categoryUserQuery[] = $this->getCategoryUserExactMatchQuery($allowedSearchTypes, $queryAttributes);
				break;
			default:
				KalturaLog::log("Undefined item type[".$this->getItemType()."]");
		}
	}

	protected function getCategoryUserExactMatchQuery($allowedSearchTypes, &$queryAttributes)
	{
		if($this->shouldAddPermissionsSearch())
			return $this->getUserIdExactMatchWithPermissions($allowedSearchTypes, $queryAttributes);

		return $this->getExactMatchQuery($allowedSearchTypes, $queryAttributes);
	}


	protected function getExactMatchQuery($allowedSearchTypes, &$queryAttributes)
	{
		$exactQuery = kESearchQueryManager::getExactMatchQuery($this, $this->getFieldName(), $allowedSearchTypes, $queryAttributes);

		if ($this->getFieldName()  ==  ESearchCategoryUserFieldName::USER_ID)
		{
			$preFixGroups = new kESearchTermsQuery($this->getFieldName(),
				array('index' => ElasticIndexMap::ELASTIC_KUSER_INDEX,
					'type' => ElasticIndexMap::ELASTIC_KUSER_TYPE,
					'id' => $this->getSearchTerm(),
					'path' => ESearchUserFieldName::GROUP_IDS));
			$boolQuery = new kESearchBoolQuery();
			$boolQuery->addToShould($exactQuery);
			$boolQuery->addToShould($preFixGroups);
			return $boolQuery;
		}
		return $exactQuery;
	}



	private function shouldAddPermissionsSearch()
	{
		$permissionLevel = $this->getPermissionLevel();
		if(in_array($this->getFieldName(), array(ESearchCategoryUserFieldName::USER_ID)) &&
			(isset($permissionLevel) || $this->getPermissionName()))
			return true;
		return false;
	}

	private function getUserIdExactMatchWithPermissions($allowedSearchTypes, &$queryAttributes)
	{

		$params = array(
			elasticClient::ELASTIC_INDEX_KEY => ElasticIndexMap::ELASTIC_KUSER_INDEX,
			elasticClient::ELASTIC_TYPE_KEY => ElasticIndexMap::ELASTIC_KUSER_TYPE,
			elasticClient::ELASTIC_ID_KEY => $this->getSearchTerm()
		);

		$elasticClient = new elasticClient();
		$elasticResults = $elasticClient->get($params);
		$userGroupIds  = $elasticResults[kESearchCoreAdapter::SOURCE][kESearchCoreAdapter::GROUP_IDS];
		$userGroupIds[] = $this->getSearchTerm(); //adding the kuser itself

		$boolQuery = new kESearchBoolQuery();
		foreach ($userGroupIds as $originalTerm)
		{
			$permissionLevel = $this->getPermissionLevel();
			$permissionName = $this->getPermissionName();

			if (!is_null($permissionLevel))
			{
				$this->setSearchTerm(elasticSearchUtils::formatCategoryUserPermissionLevel($originalTerm, $permissionLevel));
				$permissionLevelQuery = kESearchQueryManager::getExactMatchQuery($this, $this->getFieldName(), $allowedSearchTypes, $queryAttributes);
				$boolQuery->addToShould($permissionLevelQuery);
			}

			if ($permissionName)
			{
				$this->setSearchTerm(elasticSearchUtils::formatCategoryUserPermissionName($originalTerm, $permissionName));
				$permissionNameQuery = kESearchQueryManager::getExactMatchQuery($this, $this->getFieldName(), $allowedSearchTypes, $queryAttributes);
				$boolQuery->addToShould($permissionNameQuery);
			}
		}
		$this->setSearchTerm($this->getSearchTerm());

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

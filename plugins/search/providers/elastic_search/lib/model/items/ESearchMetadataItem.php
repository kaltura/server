<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
class ESearchMetadataItem extends ESearchNestedObjectItem
{

	const INNER_HITS_CONFIG_KEY = 'metadataInnerHitsSize';
	const NESTED_QUERY_PATH = 'metadata';
	const HIGHLIGHT_CONFIG_KEY = 'metadataMaxNumberOfFragments';

	private static $allowed_search_types_for_field = array(
		ESearchMetadataFieldName::VALUE_TEXT => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, "ESearchItemType::EXISTS"=> ESearchItemType::EXISTS, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, ESearchUnifiedItem::UNIFIED),
		ESearchMetadataFieldName::VALUE_INT => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, "ESearchItemType::EXISTS"=> ESearchItemType::EXISTS, 'ESearchItemType::RANGE'=>ESearchItemType::RANGE, ESearchUnifiedItem::UNIFIED),
	);

	protected static $field_boost_values = array(
		ESearchMetadataFieldName::VALUE_TEXT => 100,
		ESearchMetadataFieldName::VALUE_INT => 100,
	);

	/**
	 * @var string
	 */
	protected $searchTerm;

	/**
	 * @var string
	 */
	protected $xpath;

	/**
	 * @var int
	 */
	protected $metadataProfileId;

	/**
	 * @var int
	 */
	protected $metadataFieldId;

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
	 * @return string
	 */
	public function getXpath()
	{
		return $this->xpath;
	}

	/**
	 * @param string $xpath
	 */
	public function setXpath($xpath)
	{
		$this->xpath = $xpath;
	}

	/**
	 * @return int
	 */
	public function getMetadataProfileId()
	{
		return $this->metadataProfileId;
	}

	/**
	 * @param int $metadataProfileId
	 */
	public function setMetadataProfileId($metadataProfileId)
	{
		$this->metadataProfileId = $metadataProfileId;
	}

	/**
	 * @return int
	 */
	public function getMetadataFieldId()
	{
		return $this->metadataFieldId;
	}

	/**
	 * @param int $metadataFieldId
	 */
	public function setMetadataFieldId($metadataFieldId)
	{
		$this->metadataFieldId = $metadataFieldId;
	}

	public static function getAllowedSearchTypesForField()
	{
		return array_merge(self::$allowed_search_types_for_field, parent::getAllowedSearchTypesForField());
	}

	public function createSingleItemSearchQuery($boolOperator, &$metadataBoolQuery, $allowedSearchTypes, &$queryAttributes)
	{
		$this->validateItemInput();
		switch ($this->getItemType())
		{
			case ESearchItemType::EXACT_MATCH:
				$query = $this->getMetadataExactMatchQuery($allowedSearchTypes, $queryAttributes);
				break;
			case ESearchItemType::PARTIAL:
				$query = $this->getMetadataPartialQuery($queryAttributes);
				break;
			case ESearchItemType::STARTS_WITH:
				$query = $this->getMetadataPrefixQuery($allowedSearchTypes, $queryAttributes);
				break;
			case ESearchItemType::EXISTS:
				$query = $this->getMetadataExistQuery($allowedSearchTypes, $queryAttributes);
				break;
			case ESearchItemType::RANGE:
				$query = $this->getMetadataRangeQuery($allowedSearchTypes, $queryAttributes);
				break;
			default:
				KalturaLog::log("Undefined item type[".$this->getItemType()."]");
		}

		$metadataBoolQuery->addByOperatorType($boolOperator, $query);
	}

	protected function getMetadataExactMatchQuery($allowedSearchTypes, &$queryAttributes)
	{
		if(ctype_digit($this->getSearchTerm()))
		{
			$metadataExactMatch = new kESearchBoolQuery();
			$textExactMatch = kESearchQueryManager::getExactMatchQuery($this, ESearchMetadataFieldName::VALUE_TEXT, $allowedSearchTypes, $queryAttributes);
			$metadataExactMatch->addToShould($textExactMatch);
			$intExactMatch = kESearchQueryManager::getExactMatchQuery($this, ESearchMetadataFieldName::VALUE_INT, $allowedSearchTypes, $queryAttributes);
			$metadataExactMatch->addToShould($intExactMatch);
		}
		else if($this->getXpath() || $this->getMetadataProfileId() || $this->getMetadataFieldId())
		{
			$metadataExactMatch = new kESearchBoolQuery();
			$textExactMatch = kESearchQueryManager::getExactMatchQuery($this, ESearchMetadataFieldName::VALUE_TEXT, $allowedSearchTypes, $queryAttributes);
			$metadataExactMatch->addToMust($textExactMatch);
		}
		else
		{
			return kESearchQueryManager::getExactMatchQuery($this, ESearchMetadataFieldName::VALUE_TEXT, $allowedSearchTypes, $queryAttributes);
		}

		if($this->getXpath())
			$metadataExactMatch->addToFilter($this->getXPathQuery());

		if($this->getMetadataProfileId())
			$metadataExactMatch->addToFilter($this->getMetadataProfileIdQuery());

		if($this->getMetadataFieldId())
			$metadataExactMatch->addToFilter($this->getMetadataFieldIdQuery());

		return $metadataExactMatch;
	}

	protected function getMetadataPartialQuery(&$queryAttributes)
	{
		/**@var kESearchBoolQuery $metadataMultiMatch*/
		$metadataPartialQuery = kESearchQueryManager::getPartialQuery($this, ESearchMetadataFieldName::VALUE_TEXT, $queryAttributes);
		if(ctype_digit($this->getSearchTerm()))//add metadata.value_int
		{
			$partialShouldQueries = $metadataPartialQuery->getShouldQueries();
			foreach($partialShouldQueries as $partialShouldQuery)
			{
				if($partialShouldQuery instanceof kESearchMultiMatchQuery)
				{
					$partialShouldQuery->addToFields(ESearchMetadataFieldName::VALUE_INT.'^'.kESearchQueryManager::RAW_FIELD_BOOST_FACTOR);
					break;
				}
			}
		}

		if($this->getXpath())
			$metadataPartialQuery->addToFilter($this->getXPathQuery());

		if($this->getMetadataProfileId())
			$metadataPartialQuery->addToFilter($this->getMetadataProfileIdQuery());

		if($this->getMetadataFieldId())
			$metadataPartialQuery->addToFilter($this->getMetadataFieldIdQuery());

		return $metadataPartialQuery;
	}

	protected function getMetadataPrefixQuery($allowedSearchTypes, &$queryAttributes)
	{
		$metaDataPrefix = kESearchQueryManager::getPrefixQuery($this, ESearchMetadataFieldName::VALUE_TEXT, $allowedSearchTypes, $queryAttributes);

		if($this->getXpath() || $this->getMetadataProfileId() || $this->getMetadataFieldId())
		{
			$metaDataPrefixQuery = new kESearchBoolQuery();
			$metaDataPrefixQuery->addToMust($metaDataPrefix);

			if($this->getXpath())
				$metaDataPrefixQuery->addToFilter($this->getXPathQuery());

			if($this->getMetadataProfileId())
				$metaDataPrefixQuery->addToFilter($this->getMetadataProfileIdQuery());

			if($this->getMetadataFieldId())
				$metaDataPrefixQuery->addToFilter($this->getMetadataFieldIdQuery());

			$metaDataPrefix = $metaDataPrefixQuery;
		}

		return $metaDataPrefix;
	}

	protected function getMetadataExistQuery($allowedSearchTypes, &$queryAttributes)
	{
		$metadataExist = new kESearchBoolQuery();
		$metadataTextExist = kESearchQueryManager::getExistsQuery(null, ESearchMetadataFieldName::VALUE_TEXT, $allowedSearchTypes, $queryAttributes);
		$metadataExist->addToShould($metadataTextExist);
		$metadataIntExist = kESearchQueryManager::getExistsQuery(null, ESearchMetadataFieldName::VALUE_INT, $allowedSearchTypes, $queryAttributes);
		$metadataExist->addToShould($metadataIntExist);

		if($this->getXpath())
			$metadataExist->addToFilter($this->getXPathQuery());

		if($this->getMetadataProfileId())
			$metadataExist->addToFilter($this->getMetadataProfileIdQuery());

		if($this->getMetadataFieldId())
			$metadataExist->addToFilter($this->getMetadataFieldIdQuery());

		return $metadataExist;
	}

	protected function getMetadataRangeQuery($allowedSearchTypes, &$queryAttributes)
	{
		$metadataRange = kESearchQueryManager::getRangeQuery($this, ESearchMetadataFieldName::VALUE_INT, $allowedSearchTypes, $queryAttributes);

		if($this->getXpath() || $this->getMetadataProfileId() || $this->getMetadataFieldId())
		{
			$metadataRangeQuery = new kESearchBoolQuery();
			$metadataRangeQuery->addToMust($metadataRange);

			if($this->getXpath())
				$metadataRangeQuery->addToFilter($this->getXPathQuery());

			if($this->getMetadataProfileId())
				$metadataRangeQuery->addToFilter($this->getMetadataProfileIdQuery());

			if($this->getMetadataFieldId())
				$metadataRangeQuery->addToFilter($this->getMetadataFieldIdQuery());

			$metadataRange = $metadataRangeQuery;
		}

		return $metadataRange;
	}

	protected function getXPathQuery()
	{
		$xpath = elasticSearchUtils::formatSearchTerm($this->getXpath());
		$xpathQuery = new kESearchTermQuery(ESearchMetadataFieldName::XPATH, $xpath);

		return $xpathQuery;
	}

	protected function getMetadataProfileIdQuery()
	{
		$profileId = elasticSearchUtils::formatSearchTerm($this->getMetadataProfileId());
		$metadataProfileIdQuery = new kESearchTermQuery(ESearchMetadataFieldName::PROFILE_ID, $profileId);

		return $metadataProfileIdQuery;
	}

	protected function getMetadataFieldIdQuery()
	{
		$fieldId = elasticSearchUtils::formatSearchTerm($this->getMetadataFieldId());
		$metadataFieldIdQuery = new kESearchTermQuery(ESearchMetadataFieldName::FIELD_ID, $fieldId);

		return $metadataFieldIdQuery;
	}

	protected function validateItemInput()
	{
		$allowedSearchTypes = self::getAllowedSearchTypesForField();
		$allowedOnValueText = in_array($this->getItemType(), $allowedSearchTypes[ESearchMetadataFieldName::VALUE_TEXT]);
		$allowedOnValueInt = in_array($this->getItemType(), $allowedSearchTypes[ESearchMetadataFieldName::VALUE_INT]);
		if(!$allowedOnValueText && !$allowedOnValueInt)
		{
			$data = array();
			$fieldName = 'metadata.value';
			$data['itemType'] = $this->getItemType();
			$data['fieldName'] = $fieldName;
			throw new kESearchException('Type of search ['.$this->getItemType().'] not allowed on specific field ['. $fieldName.']', kESearchException::SEARCH_TYPE_NOT_ALLOWED_ON_FIELD, $data);
		}

		$this->validateEmptySearchTerm('metadata.value', $this->getSearchTerm());
	}

	public function shouldAddLanguageSearch()
	{
		return false;
	}

	public function getItemMappingFieldsDelimiter()
	{

	}

	public function getNestedQueryName(&$queryAttributes)
	{
		return ESearchItemDataType::METADATA.self::QUERY_NAME_DELIMITER.self::DEFAULT_GROUP_NAME.self::QUERY_NAME_DELIMITER.$queryAttributes->getNestedQueryNameIndex();;
	}

}

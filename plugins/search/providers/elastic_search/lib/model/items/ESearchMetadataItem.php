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

	/**
	 * @param $eSearchItemsArr
	 * @param $boolOperator
	 * @param $queryAttributes
	 * @param null $eSearchOperatorType
	 * @return array
	 */
	public static function createSearchQuery($eSearchItemsArr, $boolOperator, &$queryAttributes, $eSearchOperatorType = null)
	{
		return self::createNestedQueryForItems($eSearchItemsArr, $boolOperator, $queryAttributes);
	}

	public static function createSingleItemSearchQuery($metadataESearchItem, $boolOperator, &$metadataBoolQuery, $allowedSearchTypes, &$queryAttributes)
	{
		$metadataESearchItem->validateItemInput();
		switch ($metadataESearchItem->getItemType())
		{
			case ESearchItemType::EXACT_MATCH:
				$query = self::getMetadataExactMatchQuery($metadataESearchItem, $allowedSearchTypes, $queryAttributes);
				break;
			case ESearchItemType::PARTIAL:
				$query = self::getMetadataPartialQuery($metadataESearchItem, $queryAttributes);
				break;
			case ESearchItemType::STARTS_WITH:
				$query = self::getMetadataPrefixQuery($metadataESearchItem, $allowedSearchTypes, $queryAttributes);
				break;
			case ESearchItemType::EXISTS:
				$query = self::getMetadataExistQuery($metadataESearchItem, $allowedSearchTypes, $queryAttributes);
				break;
			case ESearchItemType::RANGE:
				$query = self::getMetadataRangeQuery($metadataESearchItem, $allowedSearchTypes, $queryAttributes);
				break;
			default:
				KalturaLog::log("Undefined item type[".$metadataESearchItem->getItemType()."]");
		}

		$metadataBoolQuery->addByOperatorType($boolOperator, $query);
	}

	protected static function getMetadataExactMatchQuery($searchItem, $allowedSearchTypes, &$queryAttributes)
	{
		if(ctype_digit($searchItem->getSearchTerm()))
		{
			$metadataExactMatch = new kESearchBoolQuery();
			$textExactMatch = kESearchQueryManager::getExactMatchQuery($searchItem, ESearchMetadataFieldName::VALUE_TEXT, $allowedSearchTypes, $queryAttributes);
			$metadataExactMatch->addToShould($textExactMatch);
			$intExactMatch = kESearchQueryManager::getExactMatchQuery($searchItem, ESearchMetadataFieldName::VALUE_INT, $allowedSearchTypes, $queryAttributes);
			$metadataExactMatch->addToShould($intExactMatch);
		}
		else if($searchItem->getXpath() || $searchItem->getMetadataProfileId() || $searchItem->getMetadataFieldId())
		{
			$metadataExactMatch = new kESearchBoolQuery();
			$textExactMatch = kESearchQueryManager::getExactMatchQuery($searchItem, ESearchMetadataFieldName::VALUE_TEXT, $allowedSearchTypes, $queryAttributes);
			$metadataExactMatch->addToMust($textExactMatch);
		}
		else
		{
			return kESearchQueryManager::getExactMatchQuery($searchItem, ESearchMetadataFieldName::VALUE_TEXT, $allowedSearchTypes, $queryAttributes);
		}

		if($searchItem->getXpath())
			$metadataExactMatch->addToFilter(self::getXPathQuery($searchItem));

		if($searchItem->getMetadataProfileId())
			$metadataExactMatch->addToFilter(self::getMetadataProfileIdQuery($searchItem));

		if($searchItem->getMetadataFieldId())
			$metadataExactMatch->addToFilter(self::getMetadataFieldIdQuery($searchItem));

		return $metadataExactMatch;
	}

	protected static function getMetadataPartialQuery($searchItem, &$queryAttributes)
	{
		/**@var kESearchBoolQuery $metadataMultiMatch*/
		$metadataPartialQuery = kESearchQueryManager::getPartialQuery($searchItem, ESearchMetadataFieldName::VALUE_TEXT, $queryAttributes);
		if(ctype_digit($searchItem->getSearchTerm()))//add metadata.value_int
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

		if($searchItem->getXpath())
			$metadataPartialQuery->addToFilter(self::getXPathQuery($searchItem));

		if($searchItem->getMetadataProfileId())
			$metadataPartialQuery->addToFilter(self::getMetadataProfileIdQuery($searchItem));

		if($searchItem->getMetadataFieldId())
			$metadataPartialQuery->addToFilter(self::getMetadataFieldIdQuery($searchItem));

		return $metadataPartialQuery;
	}

	protected static function getMetadataPrefixQuery($searchItem, $allowedSearchTypes, &$queryAttributes)
	{
		$metaDataPrefix = kESearchQueryManager::getPrefixQuery($searchItem, ESearchMetadataFieldName::VALUE_TEXT, $allowedSearchTypes, $queryAttributes);

		if($searchItem->getXpath() || $searchItem->getMetadataProfileId() || $searchItem->getMetadataFieldId())
		{
			$metaDataPrefixQuery = new kESearchBoolQuery();
			$metaDataPrefixQuery->addToMust($metaDataPrefix);

			if($searchItem->getXpath())
				$metaDataPrefixQuery->addToFilter(self::getXPathQuery($searchItem));

			if($searchItem->getMetadataProfileId())
				$metaDataPrefixQuery->addToFilter(self::getMetadataProfileIdQuery($searchItem));

			if($searchItem->getMetadataFieldId())
				$metaDataPrefixQuery->addToFilter(self::getMetadataFieldIdQuery($searchItem));

			$metaDataPrefix = $metaDataPrefixQuery;
		}

		return $metaDataPrefix;
	}

	protected static function getMetadataExistQuery($searchItem, $allowedSearchTypes, &$queryAttributes)
	{
		$metadataExist = new kESearchBoolQuery();
		$metadataTextExist = kESearchQueryManager::getExistsQuery(null, ESearchMetadataFieldName::VALUE_TEXT, $allowedSearchTypes, $queryAttributes);
		$metadataExist->addToShould($metadataTextExist);
		$metadataIntExist = kESearchQueryManager::getExistsQuery(null, ESearchMetadataFieldName::VALUE_INT, $allowedSearchTypes, $queryAttributes);
		$metadataExist->addToShould($metadataIntExist);

		if($searchItem->getXpath())
			$metadataExist->addToFilter(self::getXPathQuery($searchItem));

		if($searchItem->getMetadataProfileId())
			$metadataExist->addToFilter(self::getMetadataProfileIdQuery($searchItem));

		if($searchItem->getMetadataFieldId())
			$metadataExist->addToFilter(self::getMetadataFieldIdQuery($searchItem));

		return $metadataExist;
	}

	protected static function getMetadataRangeQuery($searchItem, $allowedSearchTypes, &$queryAttributes)
	{
		$metadataRange = kESearchQueryManager::getRangeQuery($searchItem, ESearchMetadataFieldName::VALUE_INT, $allowedSearchTypes, $queryAttributes);

		if($searchItem->getXpath() || $searchItem->getMetadataProfileId() || $searchItem->getMetadataFieldId())
		{
			$metadataRangeQuery = new kESearchBoolQuery();
			$metadataRangeQuery->addToMust($metadataRange);

			if($searchItem->getXpath())
				$metadataRangeQuery->addToFilter(self::getXPathQuery($searchItem));

			if($searchItem->getMetadataProfileId())
				$metadataRangeQuery->addToFilter(self::getMetadataProfileIdQuery($searchItem));

			if($searchItem->getMetadataFieldId())
				$metadataRangeQuery->addToFilter(self::getMetadataFieldIdQuery($searchItem));

			$metadataRange = $metadataRangeQuery;
		}

		return $metadataRange;
	}

	protected static function getXPathQuery($metadataESearchItem)
	{
		$xpath = elasticSearchUtils::formatSearchTerm($metadataESearchItem->getXpath());
		$xpathQuery = new kESearchTermQuery(ESearchMetadataFieldName::XPATH, $xpath);

		return $xpathQuery;
	}

	protected static function getMetadataProfileIdQuery($metadataESearchItem)
	{
		$profileId = elasticSearchUtils::formatSearchTerm($metadataESearchItem->getMetadataProfileId());
		$metadataProfileIdQuery = new kESearchTermQuery(ESearchMetadataFieldName::PROFILE_ID, $profileId);

		return $metadataProfileIdQuery;
	}

	protected static function getMetadataFieldIdQuery($metadataESearchItem)
	{
		$fieldId = elasticSearchUtils::formatSearchTerm($metadataESearchItem->getMetadataFieldId());
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

	public function getNestedQueryNames()
	{
		if($this->getXpath())
			return array(ESearchItemDataType::METADATA.self::QUERY_NAME_DELIMITER.self::DEFAULT_GROUP_NAME.self::getMetadataProfileId().self::XPATH_DELIMITER.md5($this->getXpath()));
		return array(ESearchItemDataType::METADATA.self::QUERY_NAME_DELIMITER.self::DEFAULT_GROUP_NAME);
	}

}
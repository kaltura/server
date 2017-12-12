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
		'metadata.value_text' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, "ESearchItemType::EXISTS"=> ESearchItemType::EXISTS, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, ESearchUnifiedItem::UNIFIED),
		'metadata.value_int' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, "ESearchItemType::EXISTS"=> ESearchItemType::EXISTS, 'ESearchItemType::RANGE'=>ESearchItemType::RANGE, ESearchUnifiedItem::UNIFIED),
	);

	protected static $field_boost_values = array(
		'metadata.value_text' => 100,
		'metadata.value_int' => 100,
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
	 * @param ESearchQueryAttributes $queryAttributes
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

	/**
	 * @param $searchItem
	 * @param $allowedSearchTypes
	 * @param ESearchQueryAttributes $queryAttributes
	 * @return array
	 */
	protected static function getMetadataExactMatchQuery($searchItem, $allowedSearchTypes, &$queryAttributes)
	{
		if(ctype_digit($searchItem->getSearchTerm()))
		{
			$metadataExactMatch = new kESearchBoolQuery();
			$textExactMatch = kESearchQueryManager::getExactMatchQuery($searchItem, 'metadata.value_text', $allowedSearchTypes, $queryAttributes);
			$metadataExactMatch->addToShould($textExactMatch);
			$intExactMatch = kESearchQueryManager::getExactMatchQuery($searchItem, 'metadata.value_int', $allowedSearchTypes, $queryAttributes);
			$metadataExactMatch->addToShould($intExactMatch);
		}
		else if($searchItem->getXpath() || $searchItem->getMetadataProfileId() || $searchItem->getMetadataFieldId())
		{
			$metadataExactMatch = new kESearchBoolQuery();
			$textExactMatch = kESearchQueryManager::getExactMatchQuery($searchItem, 'metadata.value_text', $allowedSearchTypes, $queryAttributes);
			$metadataExactMatch->addToMust($textExactMatch);
		}
		else
		{
			return kESearchQueryManager::getExactMatchQuery($searchItem, 'metadata.value_text', $allowedSearchTypes, $queryAttributes);
		}

		if($searchItem->getXpath())
			$metadataExactMatch->addToFilter(self::getXPathQuery($searchItem));

		if($searchItem->getMetadataProfileId())
			$metadataExactMatch->addToFilter(self::getMetadataProfileIdQuery($searchItem));

		if($searchItem->getMetadataFieldId())
			$metadataExactMatch->addToFilter(self::getMetadataFieldIdQuery($searchItem));

		return $metadataExactMatch;
	}

	/**
	 * @param $searchItem
	 * @param ESearchQueryAttributes $queryAttributes
	 * @return array|null
	 */
	protected static function getMetadataPartialQuery($searchItem, &$queryAttributes)
	{
		/**@var kESearchBoolQuery $metadataMultiMatch*/
		$metadataPartialQuery = kESearchQueryManager::getPartialQuery($searchItem, 'metadata.value_text', $queryAttributes);
		if(ctype_digit($searchItem->getSearchTerm()))//add metadata.value_int
		{
			$partialShouldQueries = $metadataPartialQuery->getShouldQueries();
			foreach($partialShouldQueries as $partialShouldQuery)
			{
				if($partialShouldQuery instanceof kESearchMultiMatchQuery)
				{
					$partialShouldQuery->addToFields('metadata.value_int^'.kESearchQueryManager::RAW_FIELD_BOOST_FACTOR);
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

	/**
	 * @param $searchItem
	 * @param $allowedSearchTypes
	 * @param ESearchQueryAttributes $queryAttributes
	 * @return array|null
	 */
	protected static function getMetadataPrefixQuery($searchItem, $allowedSearchTypes, &$queryAttributes)
	{
		$metaDataPrefix = kESearchQueryManager::getPrefixQuery($searchItem, 'metadata.value_text', $allowedSearchTypes, $queryAttributes);

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

	/**
	 * @param $searchItem
	 * @param $allowedSearchTypes
	 * @param ESearchQueryAttributes $queryAttributes
	 * @return array
	 */
	protected static function getMetadataExistQuery($searchItem, $allowedSearchTypes, &$queryAttributes)
	{
		$metadataExist = new kESearchBoolQuery();
		$metadataTextExist = kESearchQueryManager::getExistsQuery(null, 'metadata.value_text', $allowedSearchTypes, $queryAttributes);
		$metadataExist->addToShould($metadataTextExist);
		$metadataIntExist = kESearchQueryManager::getExistsQuery(null, 'metadata.value_int', $allowedSearchTypes, $queryAttributes);
		$metadataExist->addToShould($metadataIntExist);

		if($searchItem->getXpath())
			$metadataExist->addToFilter(self::getXPathQuery($searchItem));

		if($searchItem->getMetadataProfileId())
			$metadataExist->addToFilter(self::getMetadataProfileIdQuery($searchItem));

		if($searchItem->getMetadataFieldId())
			$metadataExist->addToFilter(self::getMetadataFieldIdQuery($searchItem));

		return $metadataExist;
	}

	/**
	 * @param $searchItem
	 * @param ESearchQueryAttributes $allowedSearchTypes
	 * @return array|null
	 */
	protected static function getMetadataRangeQuery($searchItem, $allowedSearchTypes, &$queryAttributes)
	{
		$metadataRange = kESearchQueryManager::getRangeQuery($searchItem, 'metadata.value_int', $allowedSearchTypes, $queryAttributes);

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
		$xpathQuery = new kESearchTermQuery('metadata.xpath', $xpath);

		return $xpathQuery;
	}

	protected static function getMetadataProfileIdQuery($metadataESearchItem)
	{
		$profileId = elasticSearchUtils::formatSearchTerm($metadataESearchItem->getMetadataProfileId());
		$metadataProfileIdQuery = new kESearchTermQuery('metadata.metadata_profile_id', $profileId);

		return $metadataProfileIdQuery;
	}

	protected static function getMetadataFieldIdQuery($metadataESearchItem)
	{
		$fieldId = elasticSearchUtils::formatSearchTerm($metadataESearchItem->getMetadataFieldId());
		$metadataFieldIdQuery = new kESearchTermQuery('metadata.metadata_field_id', $fieldId);

		return $metadataFieldIdQuery;
	}

	protected function validateItemInput()
	{
		$allowedSearchTypes = self::getAllowedSearchTypesForField();
		$allowedOnValueText = in_array($this->getItemType(), $allowedSearchTypes['metadata.value_text']);
		$allowedOnValueInt = in_array($this->getItemType(), $allowedSearchTypes['metadata.value_int']);
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

	public function getNestedQueryName()
	{
		if($this->getXpath())
			return ESearchItemDataType::METADATA.self::QUERY_NAME_DELIMITER.self::DEFAULT_GROUP_NAME.md5($this->getXpath());

		return ESearchItemDataType::METADATA.self::QUERY_NAME_DELIMITER.self::DEFAULT_GROUP_NAME;
	}

}
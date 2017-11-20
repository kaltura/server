<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
class ESearchMetadataItem extends ESearchItem
{
	const DEFAULT_INNER_HITS_SIZE = 10;
	const INNER_HITS_CONFIG_KEY = 'metadataInnerHitsSize';

	private static $allowed_search_types_for_field = array(
		'metadata.value_text' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, "ESearchItemType::EXISTS"=> ESearchItemType::EXISTS, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, ESearchUnifiedItem::UNIFIED),
		'metadata.value_int' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, "ESearchItemType::EXISTS"=> ESearchItemType::EXISTS, 'ESearchItemType::RANGE'=>ESearchItemType::RANGE, ESearchUnifiedItem::UNIFIED),
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

	public function getType()
	{
		return 'metadata';
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

	public static function createSearchQuery($eSearchItemsArr, $boolOperator, &$queryAttributes, $eSearchOperatorType = null)
	{
		$innerHitsSize = self::initializeInnerHitsSize($queryAttributes);
		$metadataQuery['nested']['path'] = 'metadata';
		$metadataQuery['nested']['inner_hits'] = array('size' => $innerHitsSize, '_source' => true);
		$allowedSearchTypes = ESearchMetadataItem::getAllowedSearchTypesForField();

		foreach ($eSearchItemsArr as $metadataESearchItem)
		{
			self::createSingleItemSearchQuery($metadataESearchItem, $boolOperator, $metadataQuery, $allowedSearchTypes, $queryAttributes);
		}
		return array($metadataQuery);
	}

	public static function createSingleItemSearchQuery($metadataESearchItem, $boolOperator, &$metadataQuery, $allowedSearchTypes, &$queryAttributes)
	{
		switch ($metadataESearchItem->getItemType())
		{
			case ESearchItemType::EXACT_MATCH:
				$metadataQuery['nested']['query']['bool'][$boolOperator][] =
					self::getMetadataExactMatchQuery($metadataESearchItem, $allowedSearchTypes);
				break;
			case ESearchItemType::PARTIAL:
				$metadataQuery['nested']['query']['bool'][$boolOperator][] =
					self::getMetadataMultiMatchQuery($metadataESearchItem, $queryAttributes);
				break;
			case ESearchItemType::STARTS_WITH:
				$metadataQuery['nested']['query']['bool'][$boolOperator][] =
					self::getMetadataPrefixQuery($metadataESearchItem, $allowedSearchTypes);
				break;
			case ESearchItemType::EXISTS:
				$metadataQuery['nested']['query']['bool'][$boolOperator][] =
					self::getMetadataExistQuery($metadataESearchItem, $allowedSearchTypes);
				break;
			case ESearchItemType::RANGE:
				$metadataQuery['nested']['query']['bool'][$boolOperator][] =
					self::getMetadataRangeQuery($metadataESearchItem, $allowedSearchTypes);
				break;
			default:
				KalturaLog::log("Undefined item type[".$metadataESearchItem->getItemType()."]");
		}

		if($boolOperator == 'should')
			$metadataQuery['nested']['query']['bool']['minimum_should_match'] = 1;

	}

	protected static function getMetadataExactMatchQuery($searchItem, $allowedSearchTypes)
	{
		$metadataExactMatch = array();
		if(ctype_digit($searchItem->getSearchTerm()))
		{
			$metadataExactMatch['bool']['should'][] = kESearchQueryManager::getExactMatchQuery($searchItem, 'metadata.value_text', $allowedSearchTypes);
			$metadataExactMatch['bool']['should'][] = kESearchQueryManager::getExactMatchQuery($searchItem, 'metadata.value_int', $allowedSearchTypes);
			$metadataExactMatch['bool']['minimum_should_match'] = 1;
		}
		else if($searchItem->getXpath() || $searchItem->getMetadataProfileId() || $searchItem->getMetadataFieldId())
		{
			$metadataExactMatch['bool']['must'][] = kESearchQueryManager::getExactMatchQuery($searchItem, 'metadata.value_text', $allowedSearchTypes);

			if($searchItem->getXpath())
				$metadataExactMatch['bool']['must'][] = self::getXPathQuery($searchItem);

			if($searchItem->getMetadataProfileId())
				$metadataExactMatch['bool']['must'][] = self::getMetadataProfileIdQuery($searchItem);

			if($searchItem->getMetadataFieldId())
				$metadataExactMatch['bool']['must'][] = self::getMetadataFieldIdQuery($searchItem);
		}
		else
			$metadataExactMatch = kESearchQueryManager::getExactMatchQuery($searchItem, 'metadata.value_text', $allowedSearchTypes);

		return $metadataExactMatch;
	}

	protected static function getMetadataMultiMatchQuery($searchItem, &$queryAttributes)
	{
		$metadataMultiMatch = kESearchQueryManager::getMultiMatchQuery($searchItem, 'metadata.value_text', $queryAttributes);

		if(ctype_digit($searchItem->getSearchTerm()))//add metadata.value_int
			$metadataMultiMatch['bool']['should'][0]['multi_match']['fields'][] = 'metadata.value_int^3';

		if($searchItem->getXpath() || $searchItem->getMetadataProfileId() || $searchItem->getMetadataFieldId())
		{
			$metadataMultiMatchQuery = $metadataMultiMatch;
			$metadataMultiMatch = null;

			$metadataMultiMatch['bool']['must'][] = $metadataMultiMatchQuery;

			if($searchItem->getXpath())
				$metadataMultiMatch['bool']['must'][] = self::getXPathQuery($searchItem);

			if($searchItem->getMetadataProfileId())
				$metadataMultiMatch['bool']['must'][] = self::getMetadataProfileIdQuery($searchItem);

			if($searchItem->getMetadataFieldId())
				$metadataMultiMatch['bool']['must'][] = self::getMetadataFieldIdQuery($searchItem);
		}

		return $metadataMultiMatch;
	}

	protected static function getMetadataPrefixQuery($searchItem, $allowedSearchTypes)
	{
		$metaDataPrefix = kESearchQueryManager::getPrefixQuery($searchItem, 'metadata.value_text', $allowedSearchTypes);

		if($searchItem->getXpath() || $searchItem->getMetadataProfileId() || $searchItem->getMetadataFieldId())
		{
			$metaDataPrefixQuery = $metaDataPrefix;
			$metaDataPrefix = null;

			$metaDataPrefix['bool']['must'][] = $metaDataPrefixQuery;

			if($searchItem->getXpath())
				$metaDataPrefix['bool']['must'][] = self::getXPathQuery($searchItem);

			if($searchItem->getMetadataProfileId())
				$metaDataPrefix['bool']['must'][] = self::getMetadataProfileIdQuery($searchItem);

			if($searchItem->getMetadataFieldId())
				$metaDataPrefix['bool']['must'][] = self::getMetadataFieldIdQuery($searchItem);
		}

		return $metaDataPrefix;
	}

	protected static function getMetadataExistQuery($searchItem, $allowedSearchTypes)
	{
		$metadataExist = array();

		$metadataExist['bool']['should'][] = kESearchQueryManager::getExistsQuery(null, 'metadata.value_text', $allowedSearchTypes);
		$metadataExist['bool']['should'][] = kESearchQueryManager::getExistsQuery(null, 'metadata.value_int', $allowedSearchTypes);
		$metadataExist['bool']['minimum_should_match'] = 1;

		if($searchItem->getXpath())
			$metadataExist['bool']['must'][] = self::getXPathQuery($searchItem);

		if($searchItem->getMetadataProfileId())
			$metadataExist['bool']['must'][] = self::getMetadataProfileIdQuery($searchItem);

		if($searchItem->getMetadataFieldId())
			$metadataExist['bool']['must'][] = self::getMetadataFieldIdQuery($searchItem);

		return $metadataExist;
	}

	protected static function getMetadataRangeQuery($searchItem, $allowedSearchTypes)
	{
		$metadataRange = kESearchQueryManager::getRangeQuery($searchItem, 'metadata.value_int', $allowedSearchTypes);

		if($searchItem->getXpath() || $searchItem->getMetadataProfileId() || $searchItem->getMetadataFieldId())
		{
			$metadataRangeQuery = $metadataRange;
			$metadataRange = null;

			$metadataRange['bool']['must'][] = $metadataRangeQuery;

			if($searchItem->getXpath())
				$metadataRange['bool']['must'][] = self::getXPathQuery($searchItem);

			if($searchItem->getMetadataProfileId())
				$metadataRange['bool']['must'][] = self::getMetadataProfileIdQuery($searchItem);

			if($searchItem->getMetadataFieldId())
				$metadataRange['bool']['must'][] = self::getMetadataFieldIdQuery($searchItem);
		}
		return $metadataRange;
	}

	protected static function getXPathQuery($metadataESearchItem)
	{
		$xpathQuery = array(
			'term' => array(
				'metadata.xpath' => elasticSearchUtils::formatSearchTerm($metadataESearchItem->getXpath())
			)
		);

		return $xpathQuery;
	}

	protected static function getMetadataProfileIdQuery($metadataESearchItem)
	{
		$metadataProfileIdQuery = array(
			'term' => array(
				'metadata.metadata_profile_id' => elasticSearchUtils::formatSearchTerm($metadataESearchItem->getMetadataProfileId())
			)
		);

		return $metadataProfileIdQuery;
	}

	protected static function getMetadataFieldIdQuery($metadataESearchItem)
	{
		$metadataFieldIdQuery = array(
			'term' => array(
				'metadata.metadata_field_id' => elasticSearchUtils::formatSearchTerm($metadataESearchItem->getMetadataFieldId())
			)
		);

		return $metadataFieldIdQuery;
	}

	public function shouldAddLanguageSearch()
	{
		return false;
	}

	public function getItemMappingFieldsDelimiter()
	{

	}

}
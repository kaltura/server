<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
class ESearchCaptionItem extends ESearchNestedObjectItem
{

	const INNER_HITS_CONFIG_KEY = 'captionInnerHitsSize';
	const NESTED_QUERY_PATH = 'caption_assets.lines';
	const HIGHLIGHT_CONFIG_KEY = 'captionMaxNumberOfFragments';

	private static $allowed_search_types_for_field = array(
		'caption_assets.lines.content' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH,'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::EXISTS"=> ESearchItemType::EXISTS, ESearchUnifiedItem::UNIFIED),
		'caption_assets.lines.start_time' => array('ESearchItemType::RANGE'=>ESearchItemType::RANGE),
		'caption_assets.lines.end_time' => array('ESearchItemType::RANGE'=>ESearchItemType::RANGE),
	);

	private static $multiLanguageFields = array(
		ESearchCaptionFieldName::CAPTION_CONTENT,
	);

	protected static $field_boost_values = array(
		'caption_assets.lines.content' => 10,
	);

	/**
	 * @var string
	 */
	protected $searchTerm;

	/**
	 * @var ESearchCaptionFieldName
	 */
	protected $fieldName;

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
	 * @return ESearchCaptionFieldName
	 */
	public function getFieldName()
	{
		return $this->fieldName;
	}

	/**
	 * @param ESearchCaptionFieldName $fieldName
	 */
	public function setFieldName($fieldName)
	{
		$this->fieldName = $fieldName;
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

	public static function createSingleItemSearchQuery($eSearchCaptionItem, $boolOperator, &$captionQuery, $allowedSearchTypes, &$queryAttributes)
	{
		$eSearchCaptionItem->validateItemInput();
		switch ($eSearchCaptionItem->getItemType())
		{
			case ESearchItemType::EXACT_MATCH:
				$captionQuery['nested']['query']['bool'][$boolOperator][] =
					kESearchQueryManager::getExactMatchQuery($eSearchCaptionItem, $eSearchCaptionItem->getFieldName(), $allowedSearchTypes, $queryAttributes);
				break;
			case ESearchItemType::PARTIAL:
				$captionQuery['nested']['query']['bool'][$boolOperator][] =
					kESearchQueryManager::getMultiMatchQuery($eSearchCaptionItem, $eSearchCaptionItem->getFieldName(), $queryAttributes);
				break;
			case ESearchItemType::STARTS_WITH:
				$captionQuery['nested']['query']['bool'][$boolOperator][] =
					kESearchQueryManager::getPrefixQuery($eSearchCaptionItem, $eSearchCaptionItem->getFieldName(), $allowedSearchTypes, $queryAttributes);
				break;
			case ESearchItemType::EXISTS:
				$captionQuery['nested']['query']['bool'][$boolOperator][] =
					kESearchQueryManager::getExistsQuery($eSearchCaptionItem, $eSearchCaptionItem->getFieldName(), $allowedSearchTypes, $queryAttributes);
				break;
			case ESearchItemType::RANGE:
				$captionQuery['nested']['query']['bool'][$boolOperator][] =
					kESearchQueryManager::getRangeQuery($eSearchCaptionItem, $eSearchCaptionItem->getFieldName(), $allowedSearchTypes, $queryAttributes);
				break;
			default:
				KalturaLog::log("Undefined item type[".$eSearchCaptionItem->getItemType()."]");
		}

		if($boolOperator == 'should')
			$captionQuery['nested']['query']['bool']['minimum_should_match'] = 1;
	}

	public function shouldAddLanguageSearch()
	{
		if(in_array($this->getFieldName(), self::$multiLanguageFields))
			return true;

		return false;
	}

	public function getItemMappingFieldsDelimiter()
	{
		return elasticSearchUtils::UNDERSCORE_FIELD_DELIMITER;
	}

}
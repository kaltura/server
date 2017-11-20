<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
class ESearchCaptionItem extends ESearchItem
{

	const DEFAULT_INNER_HITS_SIZE = 10;

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

	public function getType()
	{
		return 'caption';
	}

	public static function getAllowedSearchTypesForField()
	{
		return array_merge(self::$allowed_search_types_for_field, parent::getAllowedSearchTypesForField());
	}

	public static function createSearchQuery($eSearchItemsArr, $boolOperator, &$queryAttributes, $eSearchOperatorType = null)
	{
		$captionFinalQuery = array();
		$innerHitsConfig = kConf::get('innerHits', 'elastic');
		$innerHitsSize = isset($innerHitsConfig['captionInnerHitsSize']) ? $innerHitsConfig['captionInnerHitsSize'] : self::DEFAULT_INNER_HITS_SIZE;
		$allowedSearchTypes = ESearchCaptionItem::getAllowedSearchTypesForField();

		//don't group to a single query if the operator is AND
		if($boolOperator == 'must')
		{
			foreach ($eSearchItemsArr as $eSearchCaptionItem)
			{
				$captionQuery = null;
				$captionQuery['nested']['path'] = 'caption_assets.lines';
				$captionQuery['nested']['inner_hits'] = array('size' => $innerHitsSize);
				self::createSingleItemSearchQuery($eSearchCaptionItem, $boolOperator, $captionQuery, $allowedSearchTypes, $queryAttributes);
				$captionFinalQuery[] = $captionQuery;
			}
		}
		else
		{
			$captionQuery['nested']['path'] = 'caption_assets.lines';
			$captionQuery['nested']['inner_hits'] = array('size' => $innerHitsSize);
			foreach ($eSearchItemsArr as $eSearchCaptionItem)
			{
				self::createSingleItemSearchQuery($eSearchCaptionItem, $boolOperator, $captionQuery, $allowedSearchTypes, $queryAttributes);
			}
			$captionFinalQuery[] = $captionQuery;
		}

		return $captionFinalQuery;
	}

	public static function createSingleItemSearchQuery($eSearchCaptionItem, $boolOperator, &$captionQuery, $allowedSearchTypes, &$queryAttributes)
	{
		$eSearchCaptionItem->validateItemInput();
		switch ($eSearchCaptionItem->getItemType())
		{
			case ESearchItemType::EXACT_MATCH:
				$captionQuery['nested']['query']['bool'][$boolOperator][] =
					kESearchQueryManager::getExactMatchQuery($eSearchCaptionItem, $eSearchCaptionItem->getFieldName(), $allowedSearchTypes);
				break;
			case ESearchItemType::PARTIAL:
				$captionQuery['nested']['query']['bool'][$boolOperator][] =
					kESearchQueryManager::getMultiMatchQuery($eSearchCaptionItem, $eSearchCaptionItem->getFieldName(), $queryAttributes);
				break;
			case ESearchItemType::STARTS_WITH:
				$captionQuery['nested']['query']['bool'][$boolOperator][] =
					kESearchQueryManager::getPrefixQuery($eSearchCaptionItem, $eSearchCaptionItem->getFieldName(), $allowedSearchTypes);
				break;
			case ESearchItemType::EXISTS:
				$captionQuery['nested']['query']['bool'][$boolOperator][] =
					kESearchQueryManager::getExistsQuery($eSearchCaptionItem, $eSearchCaptionItem->getFieldName(), $allowedSearchTypes);
				break;
			case ESearchItemType::RANGE:
				$captionQuery['nested']['query']['bool'][$boolOperator][] =
					kESearchQueryManager::getRangeQuery($eSearchCaptionItem, $eSearchCaptionItem->getFieldName(), $allowedSearchTypes);
				break;
			default:
				KalturaLog::log("Undefined item type[".$eSearchCaptionItem->getItemType()."]");
		}

		if($boolOperator == 'should')
			$captionQuery['nested']['query']['bool']['minimum_should_match'] = 1;
	}

	protected function validateItemInput()
	{
		$allowedSearchTypes = self::getAllowedSearchTypesForField();
		$this->validateAllowedSearchTypes($allowedSearchTypes, $this->getFieldName());
		$this->validateEmptySearchTerm($this->getFieldName(), $this->getSearchTerm());
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
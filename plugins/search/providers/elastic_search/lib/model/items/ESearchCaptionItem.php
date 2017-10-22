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

	public static function createSearchQuery($eSearchItemsArr, $boolOperator, $eSearchOperatorType = null)
	{
		$innerHitsConfig = kConf::get('innerHits', 'elastic');
		$innerHitsSize = isset($innerHitsConfig['captionInnerHitsSize']) ? $innerHitsConfig['captionInnerHitsSize'] : self::DEFAULT_INNER_HITS_SIZE;
		$captionQuery['nested']['path'] = 'caption_assets.lines';
		$captionQuery['nested']['inner_hits'] = array('size' => $innerHitsSize);
		$allowedSearchTypes = ESearchCaptionItem::getAllowedSearchTypesForField();
		foreach ($eSearchItemsArr as $eSearchCaptionItem)
		{
			self::createSingleItemSearchQuery($eSearchCaptionItem, $boolOperator, $captionQuery, $allowedSearchTypes);
		}
		
		return array($captionQuery);
	}

	public static function createSingleItemSearchQuery($eSearchCaptionItem, $boolOperator, &$captionQuery, $allowedSearchTypes)
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
					kESearchQueryManager::getMultiMatchQuery($eSearchCaptionItem, $eSearchCaptionItem->getFieldName(), true);
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

}
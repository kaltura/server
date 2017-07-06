<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
class ESearchCaptionItem extends ESearchItem
{
	//todo
	private static $allowed_search_types_for_field = array(
		'caption_assets.lines.content' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH,'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN),
		'caption_assets.lines.start_time' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::RANGE'),
		'caption_assets.lines.end_time' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH,'ESearchItemType::RANGE'),
	);

	/**
	 * @var string
	 */
	protected $searchTerm;

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


	public function getFieldName()
	{
		return 'caption_assets.lines.content';
	}


	public function getType()
	{
		return 'caption';
	}

	public static function getAllowedSearchTypesForField() //todo
	{
		return array_merge(self::$allowed_search_types_for_field, parent::getAllowedSearchTypesForField());
	}

	public static function createSearchQuery(array $eSearchItemsArr, $boolOperator, $eSearchOperatorType = null)
	{
		$captionQuery['nested']['path'] = 'caption_assets';
		$captionQuery['nested']['query']['nested']['inner_hits'] = array('size' => 10); //TODO: get this parameter from config
		$captionQuery['nested']['inner_hits'] = array('size' => 10, '_source' => false);
		$captionQuery['nested']['query']['nested']['path'] = "caption_assets.lines";
		$allowedSearchTypes = ESearchCaptionItem::getAllowedSearchTypesForField();
		foreach ($eSearchItemsArr as $eSearchCaptionItem)
		{
			self::createSingleItemSearchQuery($boolOperator, $eSearchCaptionItem, $captionQuery, $allowedSearchTypes);
			foreach ($eSearchCaptionItem->getRanges() as $range)
			{
				$captionQuery['nested']['query']['nested']['query']['bool'][$boolOperator][] = array('range' => array('caption_assets.lines.start_time' => array('lte' => $range[0])));
				$captionQuery['nested']['query']['nested']['query']['bool'][$boolOperator][] = array('range' => array('caption_assets.lines.end_time' => array('gte' => $range[1])));
			}
		}
		
		return $captionQuery;
	}

	public static function createSingleItemSearchQuery($boolOperator, $eSearchCaptionItem, &$captionQuery, $allowedSearchTypes)
	{
		/* @var ESearchCaptionItem $eSearchCaptionItem */
		switch ($eSearchCaptionItem->getItemType())
		{
			case ESearchItemType::EXACT_MATCH:
				$captionQuery['nested']['query']['nested']['query']['bool'][$boolOperator][] =
					kESearchQueryManager::getExactMatchQuery($eSearchCaptionItem, 'caption_assets.lines.content', $allowedSearchTypes);
				break;
			case ESearchItemType::PARTIAL:
				$captionQuery['nested']['query']['nested']['query']['bool'][$boolOperator][] =
					kESearchQueryManager::getMultiMatchQuery($eSearchCaptionItem, 'caption_assets.lines.content', true);
				break;
			case ESearchItemType::STARTS_WITH:
				$captionQuery['nested']['query']['nested']['query']['bool'][$boolOperator][] =
					kESearchQueryManager::getPrefixQuery($eSearchCaptionItem, 'caption_assets.lines.content', true);
				break;
			case ESearchItemType::DOESNT_CONTAIN:
				$captionQuery['nested']['query']['nested']['query']['bool']['must_not'][] =
					kESearchQueryManager::getDoesntContainQuery($eSearchCaptionItem, 'caption_assets.lines.content', true);
		}
	}

}
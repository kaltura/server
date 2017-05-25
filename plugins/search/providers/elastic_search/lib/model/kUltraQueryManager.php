<?php


class kUltraQueryManager
{
	/**
	 * Uses visitor/visited design pattern in order to create a search query

	 * @param UltraSearchItem $ultraSearchItem
	 * @return array - represents a sub elastic query.
	 */
	public static function createSearchQuery(UltraSearchItem $ultraSearchItem)
	{
		return $ultraSearchItem->createSearchQuery();
	}

	public static function createOperatorSearchQuery(UltraSearchOperator $ultraSearchOperator, $boolOperator = UltraSearchOperatorType::AND_OP)
	{
		if (!count($ultraSearchOperator->getSearchItems()))
		{
			return array();
		}
		$additionalParams = array();
		switch ($ultraSearchOperator->getOperator())
		{
			case UltraSearchOperatorType::AND_OP:
				$boolOpeartor = 'must';
				break;
			case UltraSearchOperatorType::OR_OP:
				$boolOpeartor = 'should';
				$additionalParams['minimum_should_match'] = 1;
				break;
			default:
				KalturaLog::crit('unknown operator type');
				return null;
		}
		$outQuery = array();
		foreach ($ultraSearchOperator->getSearchItems() as $searchItem)
		{
			/**
			 * @var UltraSearchItem $searchItem
			 */
			$outQuery['bool'][$boolOpeartor] = self::createSearchQuery($searchItem);
			foreach ($additionalParams as $addParamKey => $addParamVal)
			{
				$outQuery['bool'][$addParamKey] = $addParamVal;
			}
		}

		return $outQuery;
	}

	public static function createEntrySearchQuery(UltraSearchEntryItem $ultraEntrySearchItem, $boolOperator)
	{
		$queryVerb = $ultraEntrySearchItem->getQueryVerb();
		$queryVal = array($ultraEntrySearchItem->getFieldName() => strtolower($ultraEntrySearchItem->getSearchTerm()));
		return array($queryVerb => $queryVal);
	}

	public static function createCaptionSearchQuery(UltraSearchCaptionItem $ultraSearchCaptionItem, $boolOperator)
	{
		$captionQuery['has_child']['type'] = ElasticIndexMap::ELASTIC_CAPTION_TYPE;
		$captionQuery['has_child']['query']['nested']['path'] = 'lines';
		$captionQuery['has_child']['query']['nested']['inner_hits'] = array('size' => 10); //TODO: get this parameter from config
		$captionQuery['has_child']['inner_hits'] = array('size' => 10, '_source' => false);
		switch ($ultraSearchCaptionItem->getItemType())
		{
			case UltraSearchItemType::EXACT_MATCH:

				$captionQuery['has_child']['query']['nested']['query']['bool']['must'][] = array(
					'term' => array(
						'lines.content' => strtolower($ultraSearchCaptionItem->getSearchTerm())
					)
				);
				break;
			case UltraSearchItemType::PARTIAL:
				$captionQuery['has_child']['query']['nested']['query']['bool']['must'][] = array(
					'multi_match'=> array(
						'query'=> strtolower($ultraSearchCaptionItem->getSearchTerm()),
						'fields'=> array(
							'lines.content',
							'lines.content_*' //todo change here if we want to choose the language to search
						),
						'type'=> 'most_fields'
					)
				);
				break;
			case UltraSearchItemType::STARTS_WITH:
				$captionQuery['has_child']['query']['nested']['query']['bool']['must'][] = array(
					'prefix' => array(
						'lines.content' => strtolower($ultraSearchCaptionItem->getSearchTerm())
					)
				);
				break;
			case UltraSearchItemType::DOESNT_CONTAIN:
				$captionQuery['has_child']['query']['nested']['query']['bool']['must_not'][] = array(
					'term' => array(
						'lines.content' => strtolower($ultraSearchCaptionItem->getSearchTerm())
					)
				);
				break;
		}
		return $captionQuery;
	}
}


?>




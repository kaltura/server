<?php


class kEQueryManager
{
	/**
	 * Uses visitor/visited design pattern in order to create a search query

	 * @param ESearchItem $eSearchItem
	 * @return array - represents a sub elastic query.
	 */
	public static function createSearchQuery(ESearchItem $eSearchItem)
	{
		return $eSearchItem->createSearchQuery();
	}

	public static function createOperatorSearchQuery(ESearchOperator $eSearchOperator, $boolOperator = ESearchOperatorType::AND_OP)
	{
		if (!count($eSearchOperator->getSearchItems()))
		{
			return array();
		}
		$additionalParams = array();
		switch ($eSearchOperator->getOperator())
		{
			case ESearchOperatorType::AND_OP:
				$boolOpeartor = 'must';
				break;
			case ESearchOperatorType::OR_OP:
				$boolOpeartor = 'should';
				$additionalParams['minimum_should_match'] = 1;
				break;
			default:
				KalturaLog::crit('unknown operator type');
				return null;
		}
		$outQuery = array();
		foreach ($eSearchOperator->getSearchItems() as $searchItem)
		{
			/**
			 * @var ESearchItem $searchItem
			 */
			$outQuery['bool'][$boolOpeartor] = self::createSearchQuery($searchItem);
			foreach ($additionalParams as $addParamKey => $addParamVal)
			{
				$outQuery['bool'][$addParamKey] = $addParamVal;
			}
		}

		return $outQuery;
	}

	public static function createEntrySearchQuery(ESearchEntryItem $eEntrySearchItem, $boolOperator)
	{
		$queryVerb = $eEntrySearchItem->getQueryVerb();
		$queryVal = array($eEntrySearchItem->getFieldName() => strtolower($eEntrySearchItem->getSearchTerm()));
		return array($queryVerb => $queryVal);
	}

	public static function createCaptionSearchQuery(ESearchCaptionItem $eSearchCaptionItem, $boolOperator)
	{
		$captionQuery['has_child']['type'] = ElasticIndexMap::ELASTIC_CAPTION_TYPE;
		$captionQuery['has_child']['query']['nested']['path'] = 'lines';
		$captionQuery['has_child']['query']['nested']['inner_hits'] = array('size' => 10); //TODO: get this parameter from config
		$captionQuery['has_child']['inner_hits'] = array('size' => 10, '_source' => false);
		switch ($eSearchCaptionItem->getItemType())
		{
			case ESearchItemType::EXACT_MATCH:

				$captionQuery['has_child']['query']['nested']['query']['bool']['must'][] = array(
					'term' => array(
						'lines.content' => strtolower($eSearchCaptionItem->getSearchTerm())
					)
				);
				break;
			case ESearchItemType::PARTIAL:
				$captionQuery['has_child']['query']['nested']['query']['bool']['must'][] = array(
					'multi_match'=> array(
						'query'=> strtolower($eSearchCaptionItem->getSearchTerm()),
						'fields'=> array(
							'lines.content',
							'lines.content_*' //todo change here if we want to choose the language to search
						),
						'type'=> 'most_fields'
					)
				);
				break;
			case ESearchItemType::STARTS_WITH:
				$captionQuery['has_child']['query']['nested']['query']['bool']['must'][] = array(
					'prefix' => array(
						'lines.content' => strtolower($eSearchCaptionItem->getSearchTerm())
					)
				);
				break;
			case ESearchItemType::DOESNT_CONTAIN:
				$captionQuery['has_child']['query']['nested']['query']['bool']['must_not'][] = array(
					'term' => array(
						'lines.content' => strtolower($eSearchCaptionItem->getSearchTerm())
					)
				);
				break;
		}
		return $captionQuery;
	}
}


?>




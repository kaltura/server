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

	public static function createOperatorSearchQuery(ESearchOperator $eSearchOperator)
	{
		if (!count($eSearchOperator->getSearchItems()))
		{
			return array();
		}
		$additionalParams = array();
		switch ($eSearchOperator->getOperator())
		{
			case ESearchOperatorType::AND_OP:
				$boolOperator = 'must';
				break;
			case ESearchOperatorType::OR_OP:
				$boolOperator = 'should';
				$additionalParams['minimum_should_match'] = 1;
				break;
			default:
				KalturaLog::crit('unknown operator type');
				return null;
		}
		$outQuery = array();
		$categorizedSearchItems = array();
		//categorize each different search item by type.
		foreach ($eSearchOperator->getSearchItems() as $searchItem)
		{
			/**
			 * @var ESearchItem $searchItem
			 */
			if (!isset($categorizedSearchItems[$searchItem->getType()."SearchItems"]))
				$categorizedSearchItems[$searchItem->getType()."SearchItems"] = array();
			$categorizedSearchItems[$searchItem->getType()."SearchItems"][] = $searchItem;
		}

		if (isset($categorizedSearchItems['captionSearchItems']))
			$outQuery['bool']['must'][] = self::createCaptionSearchQuery($categorizedSearchItems['captionSearchItems'], $boolOperator, $additionalParams);

		if (isset($categorizedSearchItems['entrySearchItems']))
		{
			//TODO: partial won't work on most of these since they are not indexed as ngram
			 $entrySubQuery = self::createEntrySearchQuery($categorizedSearchItems['entrySearchItems'], $boolOperator);
			foreach($entrySubQuery as $queryVerb => $queryVal)
				$outQuery['bool'][$queryVerb][] = $queryVal;
		}

		if (isset($categorizedSearchItems['operatorSearchItems']))
		{
			foreach ($categorizedSearchItems['operatorSearchItems'] as $operatorSearchItem)
			{
				$outQuery['bool']['must'][] = self::createOperatorSearchQuery($operatorSearchItem);
			}
		}

		return $outQuery;
	}

	public static function createEntrySearchQuery(array $eEntrySearchItemsArr, $boolOperator, $additionalParams = array())
	{
		$queryOut = array();
		foreach ($eEntrySearchItemsArr as $entrySearchItem)
		{
			/**
			 * @var ESearchEntryItem $entrySearchItem
			 */
			$queryVerbs = $entrySearchItem->getQueryVerbs();
			$queryOut[$queryVerbs[0]][$queryVerbs[1]] = array($entrySearchItem->getFieldName() => strtolower($entrySearchItem->getSearchTerm()));
		}
		return $queryOut;
	}

	public static function createCaptionSearchQuery(array $eSearchCaptionItemsArr, $boolOperator, $additionalParams = null)
	{
		$captionQuery['has_child']['type'] = ElasticIndexMap::ELASTIC_CAPTION_TYPE;
		$captionQuery['has_child']['query']['nested']['path'] = 'lines';
		$captionQuery['has_child']['query']['nested']['inner_hits'] = array('size' => 10); //TODO: get this parameter from config
		$captionQuery['has_child']['inner_hits'] = array('size' => 10, '_source' => false);
		foreach ($eSearchCaptionItemsArr as $eSearchCaptionItem)
		{
			switch ($eSearchCaptionItem->getItemType())
			{
				case ESearchItemType::EXACT_MATCH:
					$captionQuery['has_child']['query']['nested']['query']['bool'][$boolOperator][] = array(
							'term' => array(
								'lines.content' => strtolower($eSearchCaptionItem->getSearchTerm())
							)
						);
					break;
				case ESearchItemType::PARTIAL:
					$captionQuery['has_child']['query']['nested']['query']['bool'][$boolOperator][] = array(
						'multi_match' => array(
							'query' => strtolower($eSearchCaptionItem->getSearchTerm()),
							'fields' => array(
								'lines.content',
								'lines.content_*' //todo change here if we want to choose the language to search
							),
							'type' => 'most_fields'
						)
					);
					break;
				case ESearchItemType::STARTS_WITH:
					$captionQuery['has_child']['query']['nested']['query']['bool'][$boolOperator][] = array(
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
		}
		foreach ($additionalParams as $addParamKey => $addParamVal)
		{
			$captionQuery['has_child']['query']['nested']['query']['bool'][$addParamKey] = $addParamVal;
		}
		return $captionQuery;
	}
}


?>




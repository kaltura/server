<?php


class kESearchQueryManager
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


		if (isset($categorizedSearchItems['metadataSearchItems']))
			$outQuery['bool']['must'][] = self::createMetadataSearchQuery($categorizedSearchItems['metadataSearchItems'], $boolOperator, $additionalParams);

		if (isset($categorizedSearchItems['cuepointSearchItems']))
			$outQuery['bool']['must'][] = self::createCuePointSearchQuery($categorizedSearchItems['cuepointSearchItems'], $boolOperator, $additionalParams);

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
			if ($entrySearchItem->getItemType() == ESearchItemType::PARTIAL)
			{
				$queryOut[$queryVerbs[0]]['multi_match']['query'] = strtolower($entrySearchItem->getSearchTerm());
				$queryOut[$queryVerbs[0]]['multi_match']['fields'] = array($eEntrySearchItemsArr->getFieldName()."^2", $entrySearchItem->getFieldName() . ".raw^2", $entrySearchItem->getFieldName() . ".trigrams");
				$queryOut[$queryVerbs[0]]['multi_match']['type'] = 'most_fields';
			}
			else
				$queryOut[$queryVerbs[0]][$queryVerbs[1]] = array($entrySearchItem->getFieldName() => strtolower($entrySearchItem->getSearchTerm()));
		}
		return $queryOut;
	}

	public static function createCaptionSearchQuery(array $eSearchCaptionItemsArr, $boolOperator, $additionalParams = null)
	{
		$captionQuery['nested']['path'] = 'caption_assets';
		$captionQuery['nested']['query']['nested']['inner_hits'] = array('size' => 10); //TODO: get this parameter from config
		$captionQuery['nested']['inner_hits'] = array('size' => 10, '_source' => false);
		$captionQuery['nested']['query']['nested']['path'] = "caption_assets.lines";
		foreach ($eSearchCaptionItemsArr as $eSearchCaptionItem)
		{
			/* @var ESearchCaptionItem $eSearchCaptionItem */
			switch ($eSearchCaptionItem->getItemType())
			{
				case ESearchItemType::EXACT_MATCH:
					$captionQuery['nested']['query']['nested']['query']['bool'][$boolOperator][] = array(
							'term' => array(
								'caption_assets.lines.content' => strtolower($eSearchCaptionItem->getSearchTerm())
							)
						);
					break;
				case ESearchItemType::PARTIAL:
					$captionQuery['nested']['query']['nested']['query']['bool'][$boolOperator][] = array(
						'multi_match' => array(
							'query' => strtolower($eSearchCaptionItem->getSearchTerm()),
							'fields' => array(
								'caption_assets.lines.content.trigrams',
								'caption_assets.lines.content.raw^3',
								'caption_assets.lines.content^2',
								'caption_assets.lines.content_*^2',
							),
							'type' => 'most_fields'
						)
					);
					break;
				case ESearchItemType::STARTS_WITH:
					$captionQuery['nested']['query']['nested']['query']['bool'][$boolOperator][] = array(
						'prefix' => array(
							'caption_assets.lines.content' => strtolower($eSearchCaptionItem->getSearchTerm())
						)
					);
					break;
				case ESearchItemType::DOESNT_CONTAIN:
					$captionQuery['has_child']['query']['nested']['query']['bool']['must_not'][] = array(
						'term' => array(
							'caption_assets.lines.content' => strtolower($eSearchCaptionItem->getSearchTerm())
						)
					);
					break;
			}

			if (!is_null($eSearchCaptionItem->getStartTimeInVideo()))
			{
				$captionQuery['has_child']['query']['nested']['query']['bool'][$boolOperator][] = array('range' => array('lines.start_time' => array('gte' => $eSearchCaptionItem->getStartTimeInVideo())));
			}
			if (!is_null($eSearchCaptionItem->getEndTimeInVideo()))
			{
				$captionQuery['has_child']['query']['nested']['query']['bool'][$boolOperator][] = array('range' => array('lines.end_time' => array('gte' => $eSearchCaptionItem->getEndTimeInVideo())));
			}

		}
		foreach ($additionalParams as $addParamKey => $addParamVal)
		{
			$captionQuery['has_child']['query']['nested']['query']['bool'][$addParamKey] = $addParamVal;
		}
		return $captionQuery;
	}

	public static function createMetadataSearchQuery(array $eSearchMetadataItemsArr, $boolOperator, $additionalParams = null)
	{
		$metadataQuery['nested']['path'] = 'metadata';
		$metadataQuery['nested']['inner_hits'] = array('size' => 10, '_source' => true);
		foreach ($eSearchMetadataItemsArr as $metadataESearchItem)
		{
			/* @var ESearchMetadataItem $metadataESearchItem */
			switch ($metadataESearchItem->getItemType())
			{
				case ESearchItemType::EXACT_MATCH:
					$metadataQuery['nested']['query']['bool'][$boolOperator][] = array(
						'term' => array(
							'metadata.value_text.raw' => strtolower($metadataESearchItem->getSearchTerm())
						)
					);
					break;
				case ESearchItemType::PARTIAL:
					$captionQuery['nested']['query']['bool'][$boolOperator][] = array(
						'multi_match' => array(
							'query' => strtolower($metadataESearchItem->getSearchTerm()),
							'fields' => array(
								'metadata.value_text.trigram',
								'metadata.value_text',
							),
							'type' => 'most_fields'
						)
					);
					break;
				case ESearchItemType::STARTS_WITH:
					$captionQuery['nested']['query']['bool'][$boolOperator][] = array(
						'prefix' => array(
							'metadata.value_text.raw' => strtolower($eSearchMetadataItemsArr->getSearchTerm())
						)
					);
					break;
				case ESearchItemType::DOESNT_CONTAIN:
					$captionQuery['nested']['query']['bool'][$boolOperator][] = array(
						'term' => array(
							'metadata.value_text.raw' => strtolower($eSearchMetadataItemsArr->getSearchTerm())
						)
					);
			}
			if ($metadataESearchItem->getXpath())
			{
				$metadataQuery['nested']['query']['bool'][$boolOperator][] = array(
					'term' => array(
						'metadata.xpath' => strtolower($metadataESearchItem->getXpath())
					)
				);
			}
			if ($metadataESearchItem->getMetadataProfileId())
			{
				$metadataQuery['nested']['query']['bool'][$boolOperator][] = array(
					'term' => array(
						'metadata.metadata_profile_id' => strtolower($metadataESearchItem->getMetadataProfileId())
					)
				);
			}
		}
		return $metadataQuery;
	}

	public static function createCuePointSearchQuery(array $eSearchCuePointItemsArr, $boolOperator, $additionalParams = null)
	{
		$cuePointQuery['nested']['path'] = 'cue_points';
		$cuePointQuery['nested']['inner_hits'] = array('size' => 10, '_source' => true);
		foreach ($eSearchCuePointItemsArr as $cuePointSearchItem)
		{
			/**
			 * @var ESearchEntryItem $cuePointSearchItem
			 */
			$queryVerbs = $cuePointSearchItem->getQueryVerbs();
			if ($eSearchCuePointItemsArr->getItemType() == ESearchItemType::PARTIAL)
			{
				$cuePointQuery['nested']['query']['bool'][$queryVerbs[0]]['multi_match']['query'] = strtolower($cuePointSearchItem->getSearchTerm());
				$cuePointQuery['nested']['query']['bool'][$queryVerbs[0]]['multi_match']['fields'] = array($cuePointSearchItem->getFieldName()."^2", $cuePointSearchItem->getFieldName() . "raw^2", $cuePointSearchItem->getFieldName() . "trigrams");
				$queryOut[$queryVerbs[0]]['multi_match']['type'] = 'most_fields';
			}
			else
				$cuePointQuery['nested']['query']['bool'][$queryVerbs[0]][$queryVerbs[1]] = array($cuePointSearchItem->getFieldName() => strtolower($cuePointSearchItem->getSearchTerm()));
		}
		return $cuePointQuery;
	}
}


?>




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
				foreach ($queryVal as $internalName => $internalVal)
					$outQuery['bool'][$queryVerb][][$internalName] = $internalVal;
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
		$allowedSearchTypes = ESearchEntryItem::getAallowedSearchTypesForField();
		foreach ($eEntrySearchItemsArr as $entrySearchItem)
		{
			/**
			 * @var ESearchEntryItem $entrySearchItem
			 */
			$queryVerbs = $entrySearchItem->getQueryVerbs();
			$searchTerm = $entrySearchItem->getSearchTerm();
			if (!empty($searchTerm))
			{
				if ($entrySearchItem->getItemType() == ESearchItemType::PARTIAL)
				{
					$queryOut[$queryVerbs[0]]['multi_match']['query'] = strtolower($entrySearchItem->getSearchTerm());
					$queryOut[$queryVerbs[0]]['multi_match']['fields'] = array($entrySearchItem->getFieldName() . "^2", $entrySearchItem->getFieldName() . ".raw^2", $entrySearchItem->getFieldName() . ".trigrams");
					$queryOut[$queryVerbs[0]]['multi_match']['type'] = 'most_fields';
				} else
				{
					$fieldNameAddition = '';
					if ($entrySearchItem->getItemType() == ESearchItemType::EXACT_MATCH && in_array(ESearchItemType::PARTIAL, $allowedSearchTypes[$entrySearchItem->getFieldName()]))
					{
						$fieldNameAddition = '.raw';
					}
					$queryOut[$queryVerbs[0]][$queryVerbs[1]] = array($entrySearchItem->getFieldName().$fieldNameAddition => strtolower($searchTerm));
				}
			}
			if (in_array('Range', $allowedSearchTypes[$entrySearchItem->getFieldName()]))
			{
				foreach ($entrySearchItem->getRanges() as $range)
				{
					$queryOut[$queryVerbs[0]]['range'] = array($entrySearchItem->getFieldName() => array('gte' => $range[0], 'lte' => $range[1]));
				}
			}
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
								'caption_assets.lines.content.raw' => strtolower($eSearchCaptionItem->getSearchTerm())
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

			foreach ($eSearchCaptionItem->getRanges() as $range)
			{
				$captionQuery['nested']['query']['nested']['query']['bool'][$boolOperator][] = array('range' => array('caption_assets.lines.start_time' => array('lte' => $range[0])));
				$captionQuery['nested']['query']['nested']['query']['bool'][$boolOperator][] = array('range' => array('caption_assets.lines.end_time' => array('gte' => $range[1])));
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
		$allowedSearchTypes = ESearchCuePointItem::getAallowedSearchTypesForField();
		foreach ($eSearchCuePointItemsArr as $cuePointSearchItem)
		{
			/**
			 * @var ESearchEntryItem $cuePointSearchItem
			 */
			$queryVerbs = $cuePointSearchItem->getQueryVerbs();
			$searchTerm = $cuePointSearchItem->getSearchTerm();
			if (!empty($searchTerm))
			{
				if ($cuePointSearchItem->getItemType() == ESearchItemType::PARTIAL)
				{
					$cuePointQuery['nested']['query']['bool'][$queryVerbs[0]]['multi_match']['query'] = strtolower($cuePointSearchItem->getSearchTerm());
					$cuePointQuery['nested']['query']['bool'][$queryVerbs[0]]['multi_match']['fields'] = array($cuePointSearchItem->getFieldName() . "^2", $cuePointSearchItem->getFieldName() . "raw^2", $cuePointSearchItem->getFieldName() . "trigrams");
					$queryOut[$queryVerbs[0]]['multi_match']['type'] = 'most_fields';
				} else
				{
					$fieldNameAddition = '';
					if ($cuePointSearchItem->getItemType() == ESearchItemType::EXACT_MATCH && in_array(ESearchItemType::PARTIAL, $allowedSearchTypes[$cuePointSearchItem->getFieldName()]))
					{
						$fieldNameAddition = '.raw';
					}
					$cuePointQuery['nested']['query']['bool'][$queryVerbs[0]][$queryVerbs[1]] = array($cuePointSearchItem->getFieldName() . $fieldNameAddition => strtolower($cuePointSearchItem->getSearchTerm()));
				}
			}
			if (in_array('Range', $allowedSearchTypes[$cuePointSearchItem->getFieldName()]))
			{
				foreach ($cuePointSearchItem->getRanges() as $range)
				{
					$queryOut[$queryVerbs[0]]['range'] = array($cuePointSearchItem->getFieldName() => array('gte' => $range[0], 'lte' => $range[1]));
				}
			}
		}
		return $cuePointQuery;
	}
}


?>




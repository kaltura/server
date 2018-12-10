<?php

class ESearchCaptionQueryFromFilter extends ESearchQueryFromFilter
{
	protected $entryIdEqual = false;

	const ITEMS = 'items';
	const TOTAL_COUNT = 'totalCount';

	protected $nonSupportedSearchFields = array(
		ESearchCaptionAssetItemFilterFields::PARTNER_ID,
		ESearchCaptionAssetItemFilterFields::FORMAT,
		ESearchCaptionAssetItemFilterFields::STATUS,
		ESearchCaptionAssetItemFilterFields::SIZE,
		ESearchCaptionAssetItemFilterFields::TAGS,
		ESearchCaptionAssetItemFilterFields::PARTNER_DESCRIPTION,
		ESearchCaptionAssetItemFilterFields::ID,
		ESearchCaptionAssetItemFilterFields::DELETED_AT,
		ESearchCaptionAssetItemFilterFields::FLAVOR_PARAMS_ID);

	protected function getNonSupportedFields()
	{
		return $this->nonSupportedSearchFields;
	}

	protected function getSphinxToElasticFieldName($field)
	{
		$fieldsMap = array(
			ESearchCaptionAssetItemFilterFields::CAPTION_ASSET_ID => ESearchCaptionFieldName::CAPTION_ASSET_ID,
			ESearchCaptionAssetItemFilterFields::CONTENT => ESearchCaptionFieldName::CONTENT,
			ESearchCaptionAssetItemFilterFields::START_TIME => ESearchCaptionFieldName::START_TIME,
			ESearchCaptionAssetItemFilterFields::END_TIME => ESearchCaptionFieldName::END_TIME,
			ESearchCaptionAssetItemFilterFields::LANGUAGE => ESearchCaptionFieldName::LANGUAGE,
			ESearchCaptionAssetItemFilterFields::LABEL => ESearchCaptionFieldName::LABEL,
			ESearchCaptionAssetItemFilterFields::ENTRY_ID => ESearchEntryFieldName::ID,
			ESearchCaptionAssetItemFilterFields::CREATED_AT => ESearchEntryFieldName::CREATED_AT ,
			ESearchCaptionAssetItemFilterFields::UPDATED_AT => ESearchEntryFieldName::UPDATED_AT ,
		);

		if(array_key_exists($field, $fieldsMap))
		{
			return $fieldsMap[$field];
		}
		else
		{
			return null;
		}
	}

	protected function getSphinxToElasticSearchItemType($operator)
	{
		$operatorsMap = array(
			baseObjectFilter::EQ => ESearchFilterItemType::EXACT_MATCH,
			baseObjectFilter::IN => ESearchFilterItemType::EXACT_MATCH_MULTI_OR,
			baseObjectFilter::NOT_IN => ESearchFilterItemType::EXACT_MATCH_NOT,
			baseObjectFilter::GTE => ESearchFilterItemType::RANGE_GTE,
			baseObjectFilter::LTE => ESearchFilterItemType::RANGE_LTE,
			baseObjectFilter::LIKE => ESearchFilterItemType::PARTIAL,
			baseObjectFilter::MULTI_LIKE_OR => ESearchFilterItemType::PARTIAL_MULTI_OR,
			baseObjectFilter::MULTI_LIKE_AND => ESearchFilterItemType::PARTIAL_MULTI_AND);

		if(array_key_exists($operator, $operatorsMap))
		{
			return $operatorsMap[$operator];
		}
		else
		{
			return null;
		}
	}

	protected function createSearchItemByFieldType($elasticFieldName)
	{
		$captionFields = array(	ESearchCaptionFieldName::CONTENT,
			ESearchCaptionFieldName::START_TIME,
			ESearchCaptionFieldName::END_TIME,
			ESearchCaptionFieldName::LANGUAGE,
			ESearchCaptionFieldName::LABEL,
			ESearchCaptionFieldName::CAPTION_ASSET_ID);

		if(in_array($elasticFieldName, $captionFields))
		{
			return new ESearchCaptionItem();
		}
		return new ESearchEntryItem();
	}

	public function retrieveElasticQueryCaptions(baseObjectFilter $filter, kPager $pager, $filterOnEntryIds)
	{
		$entrySearch = new kEntrySearch();
		$entrySearch->setFilterOnlyContext();

		if(!$filterOnEntryIds)
		{
			list ($currEntryIds, $count) = $this->retrieveElasticQueryEntryIds($filter, $pager);
			$filter->setEntryIdIn($currEntryIds);

			// when filtering on entry ids the number of ids will be at most the page size and we will want to go
			// over all of them so page index most be 1
			$pager->setPageIndex(1);
		}

		$query = $this->createElasticQueryFromFilter($filter);
		$entrySearch->setForceInnerHitsSizeOverride();
		$captionsPager = clone ($pager);

		// when we have entryIdEqual we will want to get back only this entry and run with original pager inside the captions
		if($this->entryIdEqual)
		{
			$pager->setPageSize(1);
			$pager->setPageIndex(1);
		}

		$elasticResults = $entrySearch->doSearch($query, array(), null, $pager, null);
		list($coreResults, $objectOrder, $objectCount, $objectHighlight) = kESearchCoreAdapter::getElasticResultAsArray($elasticResults,
			$entrySearch->getQueryAttributes()->getQueryHighlightsAttributes());

		return $this->getCaptionAssetItemsArray($coreResults, $captionsPager, $filter, $filterOnEntryIds);
	}

	protected function getCaptionAssetItemsArray($coreResults, kPager $captionsPager, baseObjectFilter $filter, $filterOnEntryIds)
	{
		$captionAssetItemArray = array();
		$totalCount = 0;
		foreach ($coreResults as $entryId => $captionsResults)
		{
			foreach($captionsResults as $captionGroup)
			{
				$totalCount += $captionGroup[self::TOTAL_COUNT];
				$items = $captionGroup[self::ITEMS];
				list($startIndex, $endIndex) = $this->getCaptionsIndexesFromPager($captionsPager, $filter, sizeof($items), $filterOnEntryIds);
				for ($i = $startIndex; $i < $endIndex; $i++)
				{
					$captionAssetItemArray[] = $this->createCaptionAssetItem($entryId, $items[$i]);
				}
			}
		}
		return array ($captionAssetItemArray, $totalCount);
	}


	protected function createCaptionAssetItem($entryId, $elasticCaptionItem)
	{
		$captionItem = new CaptionAssetItem();
		$captionItem->setEntryId($entryId);
		$captionItem->setCaptionAssetId($elasticCaptionItem->getCaptionAssetId());
		$captionItem->setContent($elasticCaptionItem->getLine());
		$captionItem->setStartTime($elasticCaptionItem->getStartsAt());
		$captionItem->setEndTime($elasticCaptionItem->getEndsAt());
		return $captionItem;
	}

	/*
	 *  if we got on the request a single entry id we will return caption results based on the pager sizes that were set,
	 *  else pager sizes will be used only to set the number of entries returned from elastic query and not inner number of captions
	 */
	protected function getCaptionsIndexesFromPager(kPager $pager, baseObjectFilter $filter, $itemsNum, $filterOnEntryIds)
	{
		$startIndex = 0;
		$endIndex = $itemsNum;

		if($filterOnEntryIds && $this->entryIdEqual)
		{
			$startIndex = min($pager->calcOffset(), $itemsNum);
			$endIndex = min($startIndex + $pager->getPageSize(), $itemsNum);
		}

		return array ($startIndex, $endIndex);
	}

	public function setEntryIdEqual()
	{
		$this->entryIdEqual = true;
	}

}
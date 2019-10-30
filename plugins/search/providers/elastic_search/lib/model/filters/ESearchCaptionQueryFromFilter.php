<?php

class ESearchCaptionQueryFromFilter extends ESearchQueryFromFilter
{
	protected $entryIdEqual = false;

	const ITEMS = 'items';
	const TOTAL_COUNT = 'totalCount';

	protected static $supportedSearchFields = array(
		ESearchCaptionAssetItemFilterFields::CAPTION_ASSET_ID,
		ESearchCaptionAssetItemFilterFields::ENTRY_ID,
		ESearchCaptionAssetItemFilterFields::CREATED_AT,
		ESearchCaptionAssetItemFilterFields::UPDATED_AT,
		ESearchCaptionAssetItemFilterFields::CONTENT,
		ESearchCaptionAssetItemFilterFields::LANGUAGE,
		ESearchCaptionAssetItemFilterFields::LABEL,
		ESearchCaptionAssetItemFilterFields::START_TIME,
		ESearchCaptionAssetItemFilterFields::END_TIME);


	protected static $captionNestedFields = array(
		ESearchCaptionFieldName::CONTENT,
		ESearchCaptionFieldName::START_TIME,
		ESearchCaptionFieldName::END_TIME,
		ESearchCaptionFieldName::LANGUAGE,
		ESearchCaptionFieldName::LABEL,
		ESearchCaptionFieldName::CAPTION_ASSET_ID);

	protected static $timeFields = array(
		ESearchCaptionAssetItemFilterFields::CREATED_AT,
		ESearchCaptionAssetItemFilterFields::UPDATED_AT,
	);

	public function __construct()
	{
		parent::__construct();
		$this->entryIdEqual = false;
	}

	protected static function getSupportedFields()
	{
		return self::$supportedSearchFields;
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

	protected function createSearchItemByFieldType($elasticFieldName)
	{
		$captionFields = $this->getNestedQueryFields();

		if(in_array($elasticFieldName, $captionFields))
		{
			return new ESearchCaptionItem();
		}
		return new ESearchEntryItem();
	}

	public function retrieveElasticQueryCaptions(baseObjectFilter $filter, kPager $entryPager, $filterOnEntryIds)
	{
		$entrySearch = new kEntrySearch();
		$entrySearch->setFilterOnlyContext();
		$entrySearch->setForceInnerHitsSizeOverride();

		if(!$filterOnEntryIds)
		{
			list ($currEntryIds, $count) = $this->retrieveElasticQueryEntryIds($filter, $entryPager);
			if($currEntryIds)
			{
				$filter->setEntryIdIn($currEntryIds);
				$this->updateEntryPager($entryPager, $filterOnEntryIds);
			}
			else
			{
				return array(array(), 0);
			}
		}

		$captionsPager = clone ($entryPager);
		$this->updateEntryPager($entryPager, $filterOnEntryIds);

		list($query, $kEsearchOrderBy )  = $this->createElasticQueryFromFilter($filter);

		$elasticResults = $entrySearch->doSearch($query, $entryPager, self::$validStatuses);

		list($coreResults, $objectOrder, $objectCount, $objectHighlight) = kESearchCoreAdapter::getElasticResultAsArray($elasticResults,
			$entrySearch->getQueryAttributes()->getQueryHighlightsAttributes());

		return $this->getCaptionAssetItemsArray($coreResults, $captionsPager, $filter, $filterOnEntryIds);
	}

	protected function updateEntryPager(&$entryPager, $filterOnEntryIds)
	{
		// when we have entryIdEqual we will want to get back only this entry and run with original pager inside the captions
		// so we retrieve a single entry and then use the caption pager for the inner hits
		if($this->entryIdEqual)
		{
			$entryPager->setPageSize(1);
			$entryPager->setPageIndex(1);
		}

		// when filtering on entry ids that we extracted on a previous elastic query with specific pager,
		// on the next query when greater number of inner hits is wanted we will need to retrieve only the first page as all the entries will be returned in it
		if(!$filterOnEntryIds)
		{
			$entryPager->setPageIndex(1);
		}
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
				list($startIndex, $endIndex) = $this->getCaptionsIndexesFromPager($captionsPager, sizeof($items));
				for ($i = $startIndex; $i < $endIndex; $i++)
				{
					if(sizeof($captionAssetItemArray) < $captionsPager->getPageSize())
					{
						$captionAssetItemArray[] = $this->createCaptionAssetItem($entryId, $items[$i]);
					}
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
	protected function getCaptionsIndexesFromPager(kPager $pager, $itemsNum)
	{
		$startIndex = 0;
		$endIndex = $itemsNum;

		if($this->entryIdEqual)
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

	protected static function getNestedQueryFields()
	{
		return self::$captionNestedFields;
	}

	protected function addNestedQueryPart()
	{
		if (!$this->nestedSearchItem)
		{
			$captionItem = new ESearchCaptionItem();
			$captionItem->setFieldName(ESearchCaptionFieldName::CONTENT);
			$captionItem->setItemType(ESearchItemType::EXISTS);
			$this->nestedSearchItem[] = $captionItem;
		}
		parent::addNestedQueryPart();
	}

	protected function getTimeFields()
	{
		return self::$timeFields;
	}
}

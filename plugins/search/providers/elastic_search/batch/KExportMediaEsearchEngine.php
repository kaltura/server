<?php
/**
 * @package Scheduler
 * @subpackage ExportCsv
 */

class KExportMediaEsearchEngine extends KObjectExportEngine
{
	const LIMIT = 10000;
	const PAGE_SIZE = 500;
	
	public function fillCsv(&$csvFile, &$data)
	{
		KalturaLog::info ('Exporting content for media items through Esearch, data:'.json_encode($data));
		$entrySearchParams = clone $data->searchParams;
		
		$results = KalturaElasticSearchClientPlugin::get(KBatchBase::$kClient)->eSearch->searchEntry($entrySearchParams);
		if ($results->totalCount > self::LIMIT)
		{
			KalturaLog::info ('More than 10000 results detected. Only the first 10000 results will be returned.');
		}

		$this->addHeaderRowToCsv($csvFile, $data->options);
		
		$entryPager = new KalturaFilterPager();
		$entryPager->pageSize = self::PAGE_SIZE;
		$entryPager->pageIndex = 1;
		
		$entriesToReturn = array();
		do
		{
			$results = KalturaElasticSearchClientPlugin::get(KBatchBase::$kClient)->eSearch->searchEntry($entrySearchParams, $entryPager);
			
			foreach ($results->objects as $singleResult)
			{
				/* @var $singleResult KalturaESearchEntryResult */
				
				$entriesToReturn[] = $singleResult->object;
			}
			
			if (count($entriesToReturn) > self::LIMIT)
			{
				KalturaLog::info ('Upper limit for object count reached.');
				break;
			}
			
			$entryPager->pageIndex++;
		}
		while (count($results->objects) == self::PAGE_SIZE);
		
		$this->addContentToCsv ($entriesToReturn, $csvFile, $data);
	}
	
	/**
	 * Generate the first csv row containing the fields
	 */
	protected function addHeaderRowToCsv($csvFile, $additionalFields,
	                                     $mappedFileds = null)
	{
		$headerRow = 'EntryID, Name, Description, Tags, Categories, UserID, CreatedAt, UpdatedAt, CreatorId ';
		$headerRow .= $this->getAdditionalFieldHeaders($additionalFields);
		KCsvWrapper::sanitizedFputCsv($csvFile, explode(',', $headerRow));
		
		return $csvFile;
	}
	/**
	 * @param $additionalFields
	 * @return string
	 */
	protected function getAdditionalFieldHeaders($additionalFields)
	{
		$headerRow = '';
		if($this->getEntryType($additionalFields) == KalturaEntryType::MEDIA_CLIP){
			$headerRow = ',CategoriesNames, Plays, Views, Duration, LastPlayedAt, Captions ';
		}
		return $headerRow;
	}

	/**
	 * The function grabs all the fields values for each entry and adds them as a new row to the csv file
	 */
	protected function addContentToCsv($entriesArray, $csvFile, $data)
	{
		if(!count($entriesArray))
			return;
		
		$entriesData = array();
		foreach ($entriesArray as $entry)
		{
			$entriesData[$entry->id] = $this->getCsvRowValues($entry, $data);
		}
		
		foreach ($entriesData as $entryId => $values)
		{
			KCsvWrapper::sanitizedFputCsv($csvFile, $values);
		}
	}

	/**
	 * This function calculates the default values for CSV row representing a single entry and returns them as an array
	 *
	 * @param KalturaBaseEntry $entry
	 * @param                  $data
	 * @return array
	 */
	protected function getCsvRowValues (KalturaBaseEntry $entry, $data)
	{
		$entryCategories = $this->retrieveEntryCategories ($entry->id);
		$categoriesIds = implode (',', $entryCategories);
		$values = array(
			$entry->id,
			$entry->name,
			$entry->description,
			$entry->tags,
			$categoriesIds,
			$entry->userId,
			$this->formatTimestamp($entry->createdAt, $data->options),
			$this->formatTimestamp($entry->updatedAt, $data->options),
			$entry->creatorId,
		);
		$values = array_merge($values, $this->getAdditionalFields($entry, $data->options, $categoriesIds));
		return $values;
	}

	/**
	 * Function returns an array of every category the entry is published to.
	 *
	 * @param string $entryId
	 *
	 * @return array;
	 */
	protected function retrieveEntryCategories ($entryId)
	{
		$categoryEntryFilter = new KalturaCategoryEntryFilter();
		$categoryEntryFilter->entryIdEqual = $entryId;
		$categoryEntryFilter->statusEqual = KalturaCategoryEntryStatus::ACTIVE;
		
		$pager = new KalturaFilterPager();
		$pager->pageIndex = 1;
		$pager->pageSize = self::PAGE_SIZE;
		
		$categoryEntryResult = KBatchBase::$kClient->categoryEntry->listAction($categoryEntryFilter, $pager);
		$result = array();
		foreach ($categoryEntryResult->objects as $categoryEntry)
		{
			$result[] = $categoryEntry->categoryId;
		}
		
		return $result;
	}
	/**
	 *
	 * @param string $categoriesIds
	 * @return string;
	 */
	protected function retrieveCategoriesNames ($categoriesIds)
	{
		$categoryEntryFilter = new KalturaCategoryFilter();
		$categoryEntryFilter->idIn = $categoriesIds;

		$pager = new KalturaFilterPager();
		$pager->pageIndex = 1;
		$pager->pageSize = self::PAGE_SIZE;

		$categoriesResult = KBatchBase::$kClient->category->listAction($categoryEntryFilter, $pager);
		$result = array();
		foreach ($categoriesResult->objects as $category)
		{
			$result[] = $category->name;
		}

		return  implode (',', $result);
	}
	/**
	 * Function returns an array of captions.
	 *
	 * @param string $entryId
	 *
	 * @return array;
	 */
	protected function retrieveEntryCaptions ($entryId)
	{
		$captionEntryFilter = new KalturaCaptionAssetFilter();
		$captionEntryFilter->entryIdEqual = $entryId;
		$captionEntryFilter->statusEqual = KalturaCaptionAssetStatus::READY;

		$pager = new KalturaFilterPager();
		$pager->pageIndex = 1;
		$pager->pageSize = self::PAGE_SIZE;

		$captionAssetList = KBatchBase::$kClient->captionAsset->listAction($captionEntryFilter, $pager);
		$result = array();
		/** @var KalturaCaptionAsset $caption */
		foreach ($captionAssetList->objects as $caption)
		{
			$result[] = "$caption->language;$caption->fileExt;$caption->accuracy;";
		}

		return $result;
	}
	/**
	 * @param int $timestamp
	 * @param array $options
	 * @return false|string
	 */
	protected function formatTimestamp($timestamp, $options)
	{
		if(is_array($options))
		{
			foreach($options as $option)
			{
				if($option instanceof KalturaExportToCsvOptions && !empty($option->format))
				{
					return date($option->format, $timestamp);
				}
			}
		}
		return $timestamp;
	}

	/**
	 * @param KalturaBaseEntry $entry
	 * @param array            $options
	 * @param string            $entryCategoriesIds
	 * @return array
	 */
	protected function getAdditionalFields(KalturaBaseEntry $entry, $options, $entryCategoriesIds) {
		if(is_array($options) && $this->getEntryType($options) == KalturaEntryType::MEDIA_CLIP){
			if(!$entry instanceof KalturaMediaEntry){
				return array('','','','','');
			}
			$captions = $this->retrieveEntryCaptions($entry->id);
			$entryCategoriesNames = $this->retrieveCategoriesNames($entryCategoriesIds);
			return array(
				$entryCategoriesNames,
				$entry->plays,
				$entry->views,
				$entry->duration,
				$entry->lastPlayedAt,
				!empty($captions) ? implode('|', $captions) : '',
			);
		}
		return array();
	}
	/**
	 * @param $options
	 * @return int|KalturaEntryType
	 */
	protected function getEntryType($options){
		if(is_array($options)){
			foreach($options as $option)
			{
				if($option instanceof KalturaExportToCsvOptions && !empty($option->typeEqual))
				{
					return $option->typeEqual;
				}
			}
		}
		return KalturaEntryType::AUTOMATIC;
	}
}

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
		
		// TODO: at this point, no additional fields are allowed to be passed
		$this->addHeaderRowToCsv($csvFile, array());
		
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
	protected function addHeaderRowToCsv($csvFile, $additionalFields)
	{
		$headerRow = 'EntryID, Name, Description, Tags, Categories, UserID, CreatedAt, UpdatedAt ';
		KCsvWrapper::sanitizedFputCsv($csvFile, explode(',', $headerRow));
		
		return $csvFile;
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
		
		$values = array(
			$entry->id,
			$entry->name,
			$entry->description,
			$entry->tags,
			implode (',', $entryCategories),
			$entry->userId,
			$this->formatTimestamp($entry->createdAt, $data->options),
			$this->formatTimestamp($entry->updatedAt, $data->options),
		);
		
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
		
		foreach ($categoryEntryResult->objects as $categoryEntry)
		{
			$result[] = $categoryEntry->categoryId;
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
				if($option instanceof KalturaExportToCsvOptions)
				{
					switch($option->option)
					{
						case KalturaExportToCsvOption::HUMAN_READABLE_DATE:
							return date('c', $timestamp);
						break;
					}

				}
			}
		}
		return $timestamp;
	}
}

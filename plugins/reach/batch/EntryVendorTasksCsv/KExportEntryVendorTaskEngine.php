<?php
/**
 * @package Scheduler
 * @subpackage ExportCsv
 */

class KExportEntryVendorTaskEngine extends KObjectExportEngine
{
	private $apiError = null;
	
	static private $statusEnumTranslate = array(
		1 => "PENDING",
		2 => "READY",
		3 => "PROCESSING",
		4 => "PENDING_MODERATION",
		5 => "REJECTED",
		6 => "ERROR",
		7 => "ABORTED",
		8 => "PENDING_ENTRY_READY",
	);
	
	static private $serviceFeatureEnumTranslate = array(
		1 => "CAPTIONS",
		2 => "TRANSLATION",
		3 => "ALIGNMENT",
		4 => "AUDIO_DESCRIPTION",
		"N/A" => "N/A",
	);
	
	static private $serviceTypeEnumTranslate = array(
		1 => "HUMAN",
		2 => "MACHINE",
		"N/A" => "N/A",
	);
	
	static private $catalogItemData = array();
	
	public function fillCsv(&$csvFile, &$data)
	{
		KalturaLog::info('Exporting content for entry vendor task items');
		$filter = clone $data->filter;
		$pager = new KalturaFilterPager();
		$pager->pageSize = 500;
		$pager->pageIndex = 1;
		
		$this->addHeaderRowToCsv($csvFile, array());
		$lastCreatedAt = 0;
		$totalCount = 0;
		$filter->orderBy = KalturaEntryVendorTaskOrderBy::CREATED_AT_ASC;
		do {
			if ($lastCreatedAt) {
				$filter->createdAtGreaterThanOrEqual = $lastCreatedAt;
			}
			try {
				$entryVendorTaskList = KBatchBase::$kClient->entryVendorTask->listAction($filter, $pager);
				$returnedSize = count($entryVendorTaskList->objects);
			} catch (Exception $e) {
				KalturaLog::info("Couldn't list entry Vendor Tasks on page: [$pager->pageIndex]" . $e->getMessage());
				$this->apiError = $e;
				return;
			}
			
			$this->addEntryVendorTasksToCsv($entryVendorTaskList->objects, $csvFile);
			$tasksCount = count($entryVendorTaskList->objects);
			$totalCount += $tasksCount;
			KalturaLog::debug("Adding More - $tasksCount totalCount - " . $totalCount);
			unset($entryVendorTaskList);
			if (function_exists('gc_collect_cycles')) // php 5.3 and above
				gc_collect_cycles();
		} while ($pager->pageSize == $returnedSize);
	}
	
	/**
	 * Generate the first csv row containing the fields
	 */
	protected function addHeaderRowToCsv($csvFile, $additionalFields)
	{
		$headerRow = 'Task id,createdAt,finishTime,entryId,status,reachProfileId,turnaroundTime,serviceType,serviceFeature,price,userId,moderatingUser,errDescription,notes,accuracy,context,partnerData';
		KCsvWrapper::sanitizedFputCsv($csvFile, explode(',', $headerRow));
		return $csvFile;
	}
	
	/**
	 * The function grabs all the fields values for each EntryVendorTasks and adding them as a new row to the csv file
	 */
	protected function addEntryVendorTasksToCsv(&$entryVendorTasks, &$csvFile)
	{
		if (!$entryVendorTasks)
			return;
		
		$entryVendorTasksIds = array();
		$entryVendorTaskIdToRow = array();
		
		foreach ($entryVendorTasks as $entryVendorTask) {
			$entryVendorTasksIds[] = $entryVendorTask->id;
			$entryVendorTaskIdToRow = $this->initializeCsvRowValues($entryVendorTask, $entryVendorTaskIdToRow);
		}
		
		foreach ($entryVendorTaskIdToRow as $key => $val) {
			KCsvWrapper::sanitizedFputCsv($csvFile, $val);
		}
	}
	
	/**
	 * adds the default fields values and the additional fields as nulls
	 */
	protected function initializeCsvRowValues($entryVendorTask, $entryVendorTaskIdToRow)
	{
		$catalogItemData = $this->getCatalogItemDataById($entryVendorTask->catalogItemId);
		
		$defaultRowValues = array(
			'Task id' => $entryVendorTask->id,
			'createdAt' => $this->getHumanReadbaleDate($entryVendorTask->createdAt),
			'finishTime' => $this->getHumanReadbaleDate($entryVendorTask->finishTime),
			'entryId' => $entryVendorTask->entryId,
			'status' => $this->translateEnumsToHumanReadable("status", $entryVendorTask->status),
			'reachProfileId' => $entryVendorTask->reachProfileId,
			'turnaroundTime' => $catalogItemData ? $catalogItemData["TAT"] : "N/A",
			'serviceType' => $catalogItemData ? $this->translateEnumsToHumanReadable("serviceType", $catalogItemData["serviceType"]) : "N/A",
			'serviceFeature' => $catalogItemData ? $this->translateEnumsToHumanReadable("serviceFeature", $catalogItemData["serviceFeature"]) : "N/A",
			'price' => $entryVendorTask->price,
			'userId' => $entryVendorTask->userId,
			'moderatingUser' => $entryVendorTask->moderatingUser,
			'errDescription' => $entryVendorTask->errDescription,
			'notes' => $entryVendorTask->notes,
			'accuracy' => $entryVendorTask->accuracy,
			'context' => $entryVendorTask->context,
			'partnerData' => $entryVendorTask->partnerData
		);
		
		$entryVendorTaskIdToRow[$entryVendorTask->id] = $defaultRowValues;
		
		return $entryVendorTaskIdToRow;
	}
	
	protected function getHumanReadbaleDate($unixTimeStamp)
	{
		if (!$unixTimeStamp)
			return null;
		
		return date("Y-m-d H:i", $unixTimeStamp);
	}
	
	protected function translateEnumsToHumanReadable($enumName, $enumValue)
	{
		$mapName = $enumName . "EnumTranslate";
		
		if (!self::${$enumName . "EnumTranslate"})
			return null;
		
		if (!isset(self::${$enumName . "EnumTranslate"}[$enumValue]))
			return null;
		
		return self::${$enumName . "EnumTranslate"}[$enumValue];
		
	}
	
	protected function getCatalogItemDataById($id)
	{
		if (isset(self::$catalogItemData[$id]))
			return self::$catalogItemData[$id];
		
		try
		{
			$vendorCatalogItem = KBatchBase::$kClient->vendorCatalogItem->get($id);
		}
		catch (Exception $e)
		{
			$vendorCatalogItem = null;
			KalturaLog::info("Failed to get catalog item data info for catalog item id [$id], with err message: " . $e->getMessage());
		}
		
		$catalogItemInfo = array(
			"TAT" => $vendorCatalogItem ? $vendorCatalogItem->turnAroundTime : "N/A",
			"serviceType" => $vendorCatalogItem ? $vendorCatalogItem->serviceType : "N/A",
			"serviceFeature" => $vendorCatalogItem ? $vendorCatalogItem->serviceFeature : "N/A"
		);
		
		self::$catalogItemData[$id] = $catalogItemInfo;
		return $catalogItemInfo;
	}
}
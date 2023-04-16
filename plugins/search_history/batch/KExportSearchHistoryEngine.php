<?php
/**
 * @package Scheduler
 * @subpackage ExportCsv
 */
class KExportSearchHistoryEngine extends KObjectExportEngine
{

	public function fillCsv(&$csvFile, &$data)
	{
		KalturaLog::info('Exporting search history content');
		$filter = clone $data->filter;

		try
		{
			$searchHistoryList = KBatchBase::$kClient->searchHistory->listAction($filter);
		}
		catch (Exception $e)
		{
			$this->apiError = $e;
			return;
		}

		$this->addHeaderRowToCsv($csvFile, null);
		$this->addItemsToCsv($searchHistoryList->aggregations, $csvFile);
	}

	/**
	 * Generate the first csv row containing the fields
	 */
	protected function addHeaderRowToCsv($csvFile, $additionalFields, $mappedFields = null)
	{
		KCsvWrapper::sanitizedFputCsv($csvFile, explode(',', 'searchTerm,count'));
	}

	protected function addItemsToCsv($aggregations, $csvFile)
	{
		if(count($aggregations) != 1)
		{
			return;
		}
		foreach($aggregations[0]->buckets as $bucket)
		{
			KCsvWrapper::sanitizedFputCsv($csvFile, array($bucket->value, $bucket->count));
		}
	}

}
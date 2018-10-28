<?php
/**
 * Class which parses the bulk upload CSV and activates the objects listed in it.
 * This engine class parses CSVs which describe category entries.
 *
 * @package plugins.bulkUploadCsv
 * @subpackage batch
 */
class BulkUploadCategoryEntryEngineCsv extends BulkUploadEngineCsv
{
	const OBJECT_TYPE_TITLE = 'category entry';
	const ACTION = 'action';
	const ENTRY_ID = 'entryId';
	const CATEGORY_ID = 'categoryId';

	protected function createObjects()
	{
		// start a multi request for activating category entries
		KBatchBase::impersonate($this->currentPartnerId);;
		KBatchBase::$kClient->startMultiRequest();

		KalturaLog::info("job[{$this->job->id}] start activating category entries");
		$bulkUploadResultChunk = array(); // store the results of the activated category entries

		foreach($this->bulkUploadResults as $bulkUploadResult)
		{
			/* @var $bulkUploadResult KalturaBulkUploadResultCategoryEntry */
			switch ($bulkUploadResult->action)
			{
				case KalturaBulkUploadAction::ACTIVATE:
					$bulkUploadResultChunk[] = $bulkUploadResult;
					KBatchBase::$kClient->categoryEntry->activate($bulkUploadResult->entryId, $bulkUploadResult->categoryId );
					break;

				case KalturaBulkUploadAction::REJECT:
					$bulkUploadResultChunk[] = $bulkUploadResult;
					KBatchBase::$kClient->categoryEntry->reject($bulkUploadResult->entryId, $bulkUploadResult->categoryId );
					break;

				default:
					$bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
					$bulkUploadResult->errorDescription = "Unknown action passed: [".$bulkUploadResult->action ."]";
					break;
			}

			if(KBatchBase::$kClient->getMultiRequestQueueSize() >= $this->multiRequestSize)
			{
				// handle all categoryEntry objects as the partner
				$requestResults = KBatchBase::$kClient->doMultiRequest();
				KBatchBase::unimpersonate();
				$this->updateObjectsResults($requestResults, $bulkUploadResultChunk);
				$this->checkAborted();
				KBatchBase::impersonate($this->currentPartnerId);;
				KBatchBase::$kClient->startMultiRequest();
				$bulkUploadResultChunk = array();
			}
		}

		// make all the category entry actions as the partner
		$requestResults = KBatchBase::$kClient->doMultiRequest();

		KBatchBase::unimpersonate();

		if(count($requestResults))
			$this->updateObjectsResults($requestResults, $bulkUploadResultChunk);

		KalturaLog::info("job[{$this->job->id}] finish updating category entries");
	}

	protected function getColumns()
	{
		return array(
			self::ACTION,
			self::ENTRY_ID,
			self::CATEGORY_ID
		);
	}

	protected function getUploadResultInstance()
	{
		return new KalturaBulkUploadResultCategoryEntry();
	}

	public function getObjectTypeTitle()
	{
		return self::OBJECT_TYPE_TITLE;
	}

	protected function createUploadResult($values, $columns)
	{
		$bulkUploadResult = parent::createUploadResult($values, $columns);
		if (!$bulkUploadResult)
			return;

		$bulkUploadResult->bulkUploadResultObjectType = KalturaBulkUploadObjectType::CATEGORY_ENTRY;

		// trim the values
		array_walk($values, array('BulkUploadCategoryEntryEngineCsv', 'trimArray'));

		// sets the result values
		foreach($columns as $index => $column)
		{
			if(!is_numeric($index))
				continue;
			if(iconv_strlen($values[$index], 'UTF-8'))
			{
				$bulkUploadResult->$column = $values[$index];
				KalturaLog::info("Set value $column [{$bulkUploadResult->$column}]");
			}
			else
			{
				KalturaLog::info("Value $column is empty");
			}
		}
		$bulkUploadResult->status = KalturaBulkUploadResultStatus::IN_PROGRESS;

		if (!$bulkUploadResult->action)
		{
			$bulkUploadResult->action = KalturaBulkUploadAction::ACTIVATE;
		}

		$bulkUploadResult = $this->validateBulkUploadResult($bulkUploadResult);
		if($bulkUploadResult)
			$this->bulkUploadResults[] = $bulkUploadResult;
	}

	protected function validateBulkUploadResult (KalturaBulkUploadResult $bulkUploadResult)
	{
		/* @var $bulkUploadResult KalturaBulkUploadResultUser */
		if (!$bulkUploadResult->entryId || !$bulkUploadResult->categoryId)
		{
			$bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
			$bulkUploadResult->errorType = KalturaBatchJobErrorTypes::APP;
			$bulkUploadResult->errorDescription = "Mandatory Column missing from CSV.";

			if($this->maxRecords && $this->lineNumber > $this->maxRecords) // check max records
			{
				$bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
				$bulkUploadResult->errorType = KalturaBatchJobErrorTypes::APP;
				$bulkUploadResult->errorDescription = "Exceeded max records count per bulk";
			}

			if($bulkUploadResult->status == KalturaBulkUploadResultStatus::ERROR)
			{
				$this->addBulkUploadResult($bulkUploadResult);
				return null;
			}
		}
		return $bulkUploadResult;
	}

	protected function updateObjectsResults(array $requestResults, array $bulkUploadResults)
	{
		KalturaLog::info("Updating " . count($requestResults) . " results");
		$dummy=array();
		// checking the created entries
		foreach($requestResults as $index => $requestResult)
		{
			$bulkUploadResult = $bulkUploadResults[$index];
			$this->handleMultiRequest($dummy);
			if(is_array($requestResult) && isset($requestResult['code']))
			{
				$bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
				$bulkUploadResult->errorType = KalturaBatchJobErrorTypes::KALTURA_API;
				$bulkUploadResult->objectStatus = $requestResult['code'];
				$bulkUploadResult->errorDescription = $requestResult['message'];
				$this->addBulkUploadResult($bulkUploadResult);
				continue;
			}

			if($requestResult instanceof Exception)
			{
				$bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
				$bulkUploadResult->errorType = KalturaBatchJobErrorTypes::KALTURA_API;
				$bulkUploadResult->errorDescription = $requestResult->getMessage();
				$this->addBulkUploadResult($bulkUploadResult);
				continue;
			}

			$this->addBulkUploadResult($bulkUploadResult);
		}
		$this->handleMultiRequest($dummy,true);
	}

	private function handleMultiRequest(&$ret,$finish=false)
	{
		$count = KBatchBase::$kClient->getMultiRequestQueueSize();
		//Start of new multi request session
		if($count)
		{
			if (($count % $this->multiRequestSize) == 0 || $finish)
			{
				$result = KBatchBase::$kClient->doMultiRequest();
				if (count($result))
					$ret = array_merge($ret, $result);
				if (!$finish)
					KBatchBase::$kClient->startMultiRequest();
			}
		}
		elseif (!$finish)
		{
			KBatchBase::$kClient->startMultiRequest();
		}
	}

}
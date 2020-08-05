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

	const MISSING_COLUMN = 'Mandatory Column missing from CSV';
	const EXCEEDED_MAX_RESULTS = 'Exceeded max records count per bulk';

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
		{
			$this->updateObjectsResults($requestResults, $bulkUploadResultChunk);
		}

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

	protected function getUploadResultInstanceType()
	{
		return KalturaBulkUploadObjectType::CATEGORY_ENTRY;
	}

	public function getObjectTypeTitle()
	{
		return self::OBJECT_TYPE_TITLE;
	}

	protected function createUploadResult($values, $columns)
	{
		$bulkUploadResult = parent::createUploadResult($values, $columns);
		if (!$bulkUploadResult)
		{
			return;
		}

		$bulkUploadResult->bulkUploadResultObjectType = KalturaBulkUploadObjectType::CATEGORY_ENTRY;

		array_walk($values, array('BulkUploadCategoryEntryEngineCsv', 'trimArray'));
		$this->setResultValues($columns, $values, $bulkUploadResult);

		$bulkUploadResult->status = KalturaBulkUploadResultStatus::IN_PROGRESS;

		if (!$bulkUploadResult->action)
		{
			$bulkUploadResult->action = KalturaBulkUploadAction::ACTIVATE;
		}

		$bulkUploadResult = $this->validateBulkUploadResult($bulkUploadResult);
		if($bulkUploadResult)
		{
			$this->bulkUploadResults[] = $bulkUploadResult;
		}
	}

	protected function validateBulkUploadResult (KalturaBulkUploadResult $bulkUploadResult)
	{
		/* @var $bulkUploadResult KalturaBulkUploadResultUser */
		if (!$bulkUploadResult->entryId || !$bulkUploadResult->categoryId)
		{
			$this->handleResultError($bulkUploadResult, KalturaBatchJobErrorTypes::APP, self::MISSING_COLUMN);

			if($this->maxRecords && $this->lineNumber > $this->maxRecords) // check max records
			{
				$this->handleResultError($bulkUploadResult, KalturaBatchJobErrorTypes::APP, self::EXCEEDED_MAX_RESULTS);
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
		$multiRequestResults = array();
		// checking the status of the category entries
		foreach($requestResults as $index => $requestResult)
		{
			$bulkUploadResult = $bulkUploadResults[$index];
			$this->handleMultiRequest($multiRequestResults);
			if(is_array($requestResult) && isset($requestResult['code']))
			{
				$this->handleResultError($bulkUploadResult, KalturaBatchJobErrorTypes::KALTURA_API, $requestResult['message']);
				$bulkUploadResult->objectStatus = $requestResult['code'];
			}
			else if($requestResult instanceof Exception)
			{
				$this->handleResultError($bulkUploadResult, KalturaBatchJobErrorTypes::KALTURA_API, $requestResult->getMessage());
			}

			$this->addBulkUploadResult($bulkUploadResult);
		}
		$this->handleMultiRequest($multiRequestResults,true);
	}

	protected function handleMultiRequest(&$ret, $finish = false)
	{
		$count = KBatchBase::$kClient->getMultiRequestQueueSize();
		//Start of new multi request session
		if($count)
		{
			if (($count % $this->multiRequestSize) == 0 || $finish)
			{
				$result = KBatchBase::$kClient->doMultiRequest();
				if (count($result))
				{
					$ret = array_merge($ret, $result);
				}
				if (!$finish)
				{
					KBatchBase::$kClient->startMultiRequest();
				}
			}
		}
		elseif (!$finish)
		{
			KBatchBase::$kClient->startMultiRequest();
		}
	}

}
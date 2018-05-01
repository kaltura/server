<?php

require_once KALTURA_ROOT_PATH.'/vendor/webex/xml/WebexXmlClient.class.php';
require_once KALTURA_ROOT_PATH.'/vendor/webex/xml/WebexXmlEpListControlType.class.php';
require_once KALTURA_ROOT_PATH.'/vendor/webex/xml/WebexXmlListRecordingRequest.class.php';
require_once KALTURA_ROOT_PATH.'/vendor/webex/xml/WebexXmlDelRecordingRequest.class.php';

/**
 *  This class is a helper class for the use of web xml client
 *
 *  @package infra
 *  @subpackage general
 */
class webexWrapper
{
	/**
	 * @var string $url
	 * @var WebexXmlSecurityContext $securityContext
	 * @var callable $errorLogger
	 * @var callable $debugLogger
	 * @var bool $validateNoBackup
	 */
	public function __construct($url, WebexXmlSecurityContext $securityContext, $errorLogger = null, $debugLogger = null, $validateNoBackup = false)
	{
		$this->webexClient = new WebexXmlClient($url, $securityContext, $validateNoBackup);
		$this->errorLogger = $errorLogger;
		$this->debugLogger = $debugLogger;
	}

	const MAX_DELETE_FAILURES = 10;
	const START_INDEX_OFFSET = 1;
	const NO_RECORDS_FOUND_ERROR_CODE = 15;
	const NO_RECORDS_FOUND_ERROR_MSG = 'Status: FAILURE, Reason: Sorry, no record found';
	const MAX_PAGE_SIZE = 500;

	// <editor-fold defaultstate="collapsed" desc="private members">

	/**
	 * @var callable
	 */
	private $errorLogger;

	/**
	 * @var callable
	 */
	private $debugLogger;

	/**
	 * Webex XML API client
	 * @var WebexXmlClient
	 */
	private $webexClient;

	// </editor-fold>

	// <editor-fold defaultstate="collapsed" desc="private methods">

	/**
	 * @param WebexXmlArray $serviceTypes
	 * @param $pageSize
	 * @param $startTime
	 * @param $endTime
	 * @param $startFrom
	 * @param $isRecycleBin
	 * @return WebexXmlListRecordingRequest
	 */
	private function initListRecordingRequest($serviceTypes, $pageSize, $startTime, $endTime, $startFrom, $isRecycleBin = false)
	{
		if($isRecycleBin)
		{
			$this->logDebug("Searching the recycleBin.");
			$listRecordingRequest = new WebexXmlListRecordingInRecycleBinRequest();
		}
		else
			$listRecordingRequest = new WebexXmlListRecordingRequest();

		$listControl = new WebexXmlEpListControlType();
		$listControl->setStartFrom($startFrom);
		$listControl->setMaximumNum($pageSize);
		$listRecordingRequest->setListControl($listControl);
		$listRecordingRequest->setServiceTypes($serviceTypes);

		if ($startTime && $endTime)
		{
			$createTimeScope = $this->getTimeScope($startTime, $endTime);
			$listRecordingRequest->setCreateTimeScope($createTimeScope);
		}

		return $listRecordingRequest;
	}

	private function log($logger, $str)
	{
		if ($logger)
			call_user_func($logger, '[From webexWrapper] ' .$str);
	}

	private function logError($str)
	{
		$this->log($this->errorLogger, $str);
	}

	private function logDebug($str)
	{
		$this->log($this->debugLogger, $str);
	}

	private function getTimeScope($startTime, $endTime)
	{
		$createTimeScope = new WebexXmlEpCreateTimeScopeType();
		$createTimeScope->setCreateTimeStart($startTime);
		$createTimeScope->setCreateTimeEnd($endTime);
		return $createTimeScope;
	}

	/**
	 * @param WebexXmlArray $serviceTypes
	 * @param long $startTime
	 * @param long $endTime
	 * @param bool $isRecycleBin
	 * @throws Exception
	 */
	public function deleteRecordingsByDates($serviceTypes, $startTime = null, $endTime = null, $isRecycleBin = false)
	{
		$result = $this->listRecordings($serviceTypes, $startTime, $endTime, self::START_INDEX_OFFSET, self::MAX_PAGE_SIZE, $isRecycleBin);
		$count = 0;
		$faultCounter = 0;
		while($result)
		{
			$records = $result->getRecording();
			foreach ($records as $record)
			{
				try
				{
					$this->deleteRecordById($record->getRecordingID(), $isRecycleBin);
					$this->logDebug('deleted ' . ++$count . ' records so far');
				}
				catch (Exception $e)
				{
					$this->logError("Failed to delete record {$record->getRecordingID()} ".print_r($e, true));
					if(++$faultCounter >= webexWrapper::MAX_DELETE_FAILURES)
						throw new Exception("Failed to delete more then ".webexWrapper::MAX_FAILURES." times", 0, $e);
				}
			}

			$result = $this->listRecordings($serviceTypes, $startTime, $endTime, $faultCounter+webexWrapper::START_INDEX_OFFSET,self::MAX_PAGE_SIZE, $isRecycleBin);
		}
	}

	// </editor-fold>

	/**
	 * @param string[] $stringServiceTypes
	 * @return WebexXmlComServiceTypeType[]
	 */
	public static function stringServicesTypesToWebexXmlArray($stringServiceTypes)
	{
		$servicesTypes = new WebexXmlArray('WebexXmlComServiceTypeType');
		foreach($stringServiceTypes as $serviceType)
		{
			$servicesTypes[] = new WebexXmlComServiceTypeType($serviceType);
		}

		return $servicesTypes;
	}

	/**
	 * @param WebexXmlArray $serviceTypes
	 * @param long $startTime
	 * @param long $endTime
	 * @param int $startFrom
	 * @param int $pageSize
	 * @param bool $isRecycleBin
	 * @return WebexXmlListRecording
	 * @throws Exception
	 */
	public function listRecordings($serviceTypes, $startTime = null, $endTime = null, $startFrom = 1, $pageSize = 500, $isRecycleBin = false)
	{
		$listRecordingRequest = $this->initListRecordingRequest($serviceTypes, $pageSize, $startTime, $endTime, $startFrom, $isRecycleBin);
		try
		{
			$listRecordingResponse = $this->webexClient->send($listRecordingRequest);
		}
		catch (Exception $e)
		{
			if ($e->getCode() != webexWrapper::NO_RECORDS_FOUND_ERROR_CODE && $e->getMessage() != webexWrapper::NO_RECORDS_FOUND_ERROR_MSG)
			{
				$this->logError("Error occurred while fetching records from webex: " . print_r($e, true));
				throw $e;
			}

			$this->logDebug("No records found between {$startTime} and {$endTime}");
			return null;
		}

		$this->logDebug("Found {$listRecordingResponse->getMatchingRecords()->getTotal()} matching records");
		return $listRecordingResponse;
	}

	/**
	 * @param WebexXmlArray $serviceTypes
	 * @param long $startTime
	 * @param long $endTime
	 * @param bool $isRecycleBin
	 * @return array
	 * @throws Exception
	 */
	public function listAllRecordings($serviceTypes, $startTime = null, $endTime = null, $isRecycleBin = false)
	{
		$startFrom = 1;
		$fileList = array();
		do
		{
			$listRecordingRequest = $this->initListRecordingRequest($serviceTypes, self::MAX_PAGE_SIZE, $startTime, $endTime, $startFrom, $isRecycleBin);
			try
			{
				$listRecordingResponse = $this->webexClient->send($listRecordingRequest);
			}
			catch (Exception $e)
			{
				if ($e->getCode() != webexWrapper::NO_RECORDS_FOUND_ERROR_CODE && $e->getMessage() != webexWrapper::NO_RECORDS_FOUND_ERROR_MSG)
				{
					$this->logError("Error occurred while fetching records from webex: " . print_r($e, true));
					throw $e;
				}

				break;
			}

			$fileList = array_merge($fileList, $listRecordingResponse->getRecording());
			$startFrom = $listRecordingResponse->getMatchingRecords()->getStartFrom() + $listRecordingResponse->getMatchingRecords()->getReturned();
		}while (count ($fileList) < $listRecordingResponse->getMatchingRecords()->getTotal());

		$this->logDebug("Found ".count($fileList)." matching records");
		return $fileList;
	}

	/**
	 * @param int $recordingId
	 * @param bool $isRecycleBin
	 * @return WebexXmlResponseBodyContent
	 * @throws Exception
	 */
	public function deleteRecordById($recordingId, $isRecycleBin = false)
	{
		if($isRecycleBin)
		{
			$deleteRecordingRequest = new WebexXmlDelRecordingRequest();
			$deleteRecordingRequest->setIsServiceRecording(1);
		}
		else
		{
			$deleteRecordingRequest = new WebexXmlDelRecordingFromRecycleBinRequest();
		}

		$deleteRecordingRequest->setRecordingID($recordingId);
		try
		{
			$response = $this->webexClient->send($deleteRecordingRequest);
		}
		catch (Exception $e)
		{
			$this->logError("Error occurred while deleting record {$recordingId} from webex: " . print_r($e, true));
			throw $e;
		}

		return $response;
	}

	/**
	 * @param string $recordName
	 * @param WebexXmlArray $serviceTypes
	 * @param bool $isRecycleBin
	 * @return WebexXmlResponseBodyContent
	 * @throws Exception
	 */
    public function deleteRecordByName($recordName, $serviceTypes, $isRecycleBin = false)
	{
		$listRecordingResponse = $this->getRecordByName($recordName, $serviceTypes, $isRecycleBin);
		if(!$listRecordingResponse)
			return false;

		$records = $listRecordingResponse->getRecording();
		$id = $records[0]->getRecordingID();
		$this->deleteRecordById($id, $isRecycleBin);
		$logMsg = "Deleted record {$recordName} with id {$id}";
		if($isRecycleBin)
			$logMsg = $logMsg." from recycle bin.";

		$this->logDebug($logMsg);
		return true;
	}

	/**
	 * @param string $recordName
	 * @param WebexXmlArray $serviceTypes
	 * @param bool $isRecycleBin
	 * @return WebexXmlListRecording
	 * @throws Exception
	 */
	public function getRecordByName($recordName, $serviceTypes, $isRecycleBin = false)
	{
		if($isRecycleBin)
			$listRecordingRequest = new WebexXmlListRecordingInRecycleBinRequest();
		else
			$listRecordingRequest = new WebexXmlListRecordingRequest();

		$listRecordingRequest->setRecordName($recordName);
		$listRecordingRequest->setServiceTypes($serviceTypes);
		try
		{
			$listRecordingResponse = $this->webexClient->send($listRecordingRequest);
		}
		catch (Exception $e)
		{
			if ($e->getCode() != webexWrapper::NO_RECORDS_FOUND_ERROR_CODE && $e->getMessage() != webexWrapper::NO_RECORDS_FOUND_ERROR_MSG)
			{
				$this->logError("Error occurred while fetching records from webex: " . print_r($e, true));
				throw $e;
			}
			else
			{
				$this->logDebug("No Record found for name {$recordName}.");
				return null;
			}
		}

		return $listRecordingResponse;
	}
}
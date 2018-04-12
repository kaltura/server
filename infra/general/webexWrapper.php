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
	 */
	public function __construct($url, WebexXmlSecurityContext $securityContext, $errorLogger = null, $debugLogger = null)
	{
		$this->webexClient = new WebexXmlClient($url, $securityContext);
		$this->errorLogger = $errorLogger;
		$this->debugLogger = $debugLogger;
	}

	const MAX_DELETE_FAILURES = 10;
	const START_INDEX_OFFSET = 1;
	const NO_RECORDS_FOUND_ERROR_CODE = 15;
	const NO_RECORDS_FOUND_ERROR_MSG = 'Status: FAILURE, Reason: Sorry, no record found';

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
	 * @return WebexXmlListRecordingRequest
	 */
	private function initListRecordingRequest($serviceTypes, $pageSize, $startTime, $endTime, $startFrom)
	{
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
	 * @return WebexXmlListRecording
	 * @throws Exception
	 */
	public function listRecordings ($serviceTypes, $startTime = null, $endTime = null, $startFrom = 1, $pageSize = 500)
	{
		$listRecordingRequest = $this->initListRecordingRequest($serviceTypes, $pageSize, $startTime, $endTime, $startFrom);
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

			return null;
		}

		$this->logDebug("Found {$listRecordingResponse->getMatchingRecords()->getTotal()} matching records");
		return $listRecordingResponse;
	}

	/**
	 * @param int $recordingId
	 * @return WebexXmlResponseBodyContent
	 * @throws Exception
	 */
	public function deleteRecordById($recordingId)
	{
		$deleteRecordingRequest = new WebexXmlDelRecordingRequest();
		$deleteRecordingRequest->setRecordingID($recordingId);
		$deleteRecordingRequest->setIsServiceRecording(1);
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
	 * Delete a record in the recycle bin by its record id
	 * @param int $recordingId
	 * @return WebexXmlResponseBodyContent
	 * @throws Exception
	 */
	public function deleteRecordFromRecycleBinById($recordingId)
	{
		$delFromRecycleBinRequest = new WebexXmlDelRecordingFromRecycleBinRequest();
		$delFromRecycleBinRequest->setRecordingID($recordingId);
		try
		{
			return $this->webexClient->send($delFromRecycleBinRequest);
		}
		catch (Exception $e)
		{
			$this->logError("Error occurred while trying to delete file with id: {$recordingId} from webex recyclebin: " . print_r($e, true));
			throw $e;
		}
	}

	/**
	 * Locate recording in recycle bin according to the creation time
	 * @param string $createTime
	 * @return WebexXmlResponseBodyContent
	 * @throws Exception
	 */
	public function getRecordingsFromRecycleBinByCreationTime($createTime)
	{
		$listControl = new WebexXmlEpListControlType();
		$listControl->setStartFrom(1);
		$createTimeScope = $this->getTimeScope($createTime, $createTime);
		$listRecordingRequest = new WebexXmlListRecordingInRecycleBinRequest();
		$listRecordingRequest->setCreateTimeScope($createTimeScope);
		$listRecordingRequest->setListControl($listControl);
		try
		{
			$listRecordingResponse = $this->webexClient->send($listRecordingRequest);
		}
		catch (Exception $e)
		{
			$this->logError("Error occurred while trying to get file with creation time: {$createTime} from webex recyclebin: " . print_r($e, true));
			throw $e;
		}

		return $listRecordingResponse->getRecording();
	}

	/**
	 * @param string $recordName
	 * @param WebexXmlArray $serviceTypes
	 * @throws Exception
	 */
    public function deleteRecordByName($recordName, $serviceTypes)
	{
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
				return;
			}
		}

		$records = $listRecordingResponse->getRecording();
		$id = $records[0]->getRecordingID();
		$this->deleteRecordById($id);
		$this->logDebug("Deleted record {$recordName} with id {$id}.");
	}

	/**
	 * @param WebexXmlArray $serviceTypes
	 * @param long $startTime
	 * @param long $endTime
	 * @throws Exception
	 */
	public function deleteRecordingsByDates($serviceTypes, $startTime = null, $endTime = null)
	{
		$result = $this->listRecordings($serviceTypes, $startTime, $endTime);
		$count = 0;
		$faultCounter = 0;
		while($result)
		{
			$records = $result->getRecording();
			foreach ($records as $record)
			{
				try
				{
					$this->deleteRecordById($record->getRecordingID());
					$this->logDebug('deleted ' . ++$count . ' records so far');
				}
				catch (Exception $e)
				{
					$this->logError("Failed to delete record {$record->getRecordingID()} ".print_r($e, true));
					if(++$faultCounter >= webexWrapper::MAX_DELETE_FAILURES)
						throw new Exception("Failed to delete more then ".webexWrapper::MAX_FAILURES." times", 0, $e);
				}
			}

			$result = $this->listRecordings($serviceTypes, $startTime, $endTime, $faultCounter+webexWrapper::START_INDEX_OFFSET);
		}
	}
}

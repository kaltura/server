<?php

require_once KALTURA_ROOT_PATH.'/vendor/webex/xml/WebexXmlClient.php';


/**
 *  This class is a helper class for the use of web xml client
 *
 *  @package infra
 *  @subpackage general
 */
class webexWrapper
{
	/**
	 * @var array
	 * this array callable as function name who can print data to the log
	 */
	private $logger;

	/**
	 * Webex XML API client
	 * @var WebexXmlClient
	 */
	private $webexClient;

	/**
	 * @var string $url
	 * @var WebexXmlSecurityContext $securityContext
	 * @var array $logger
	 */
	public function __construct($url, WebexXmlSecurityContext $securityContext, $logger = null)
	{
		$this->webexClient = new WebexXmlClient($url, $securityContext);
		$this->logger = $logger;
	}

	private function log($str)
	{
		if ($this->logger)
			call_user_func($this->logger, '[From webexWrapper] ' .$str);
	}

	/**
	 * @param string[] $stringServiceTypes
	 * @return WebexXmlArray
	 */
	private function stringServicesTypesToWebexXmlComServiceTypeType($stringServiceTypes)
	{
		$servicesTypes = new WebexXmlArray('WebexXmlComServiceTypeType');
		foreach($stringServiceTypes as $serviceType)
		{
			$servicesTypes[] = new WebexXmlComServiceTypeType($serviceType);
		}

		return $servicesTypes;
	}

	/**
	 * @param string[] $stringServiceTypes
	 * @param long $startTime
	 * @param long $endTime
	 * @return WebexXmlEpRecordingType[]
	 * @throws Exception
	 */
	public function listRecordings ($stringServiceTypes = null, $startTime = null, $endTime = null)
	{
		$fileList = array();
		$startFrom = 1;
		if($stringServiceTypes)
			$servicesTypes = $this->stringServicesTypesToWebexXmlComServiceTypeType($stringServiceTypes);

		try{
			do
			{
				$listControl = new WebexXmlEpListControlType();
				$listControl->setStartFrom($startFrom);
				$listRecordingRequest = new WebexXmlListRecordingRequest();
				$listRecordingRequest->setListControl($listControl);
				if($stringServiceTypes)
					$listRecordingRequest->setServiceTypes($servicesTypes);

				if($startTime && $endTime)
				{
					$createTimeScope = new WebexXmlEpCreateTimeScopeType();
					$createTimeScope->setCreateTimeStart($startTime);
					$createTimeScope->setCreateTimeEnd($endTime);
					$listRecordingRequest->setCreateTimeScope($createTimeScope);
				}

				$listRecordingResponse = $this->webexClient->send($listRecordingRequest);
				$fileList = array_merge($fileList, $listRecordingResponse->getRecording());
				$startFrom = $listRecordingResponse->getMatchingRecords()->getStartFrom() + $listRecordingResponse->getMatchingRecords()->getReturned();
			} while (count ($fileList) < $listRecordingResponse->getMatchingRecords()->getTotal());
		}
		catch (Exception $e)
		{
			if ($e->getCode() != 15 && $e->getMessage() != 'Status: FAILURE, Reason: Sorry, no record found')
			{
				$this->log("Error occurred while fetching records from webex: " . print_r($e, true));
				throw $e;
			}
		}

		return $fileList;
	}

	/**
	 * @param int $recordingId
	 * @return WebexXmlResponseBodyContent
	 * @throws Exception
	 */
	public function deleteRecordById($recordingId)
	{
		$deleteRecordingRequest = new WebexXmlDelRecordingRequest();
		$deleteRecordingRequest->setRecordingID(getRecordingID());
		$deleteRecordingRequest->setIsServiceRecording(1);
		try
		{
			$response = $this->webexClient->send($deleteRecordingRequest);
		}
		catch (Exception $e)
		{
			$this->log("Error occurred while deleting record {$recordingId} from webex: " . print_r($e, true));
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
			$this->log("Error occurred while trying to delete file with id: {$recordingId} from webex recyclebin: " . print_r($e, true));
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
		$createTimeScope = new WebexXmlEpCreateTimeScopeType();
		$createTimeScope->setCreateTimeStart($createTime);
		$createTimeScope->setCreateTimeEnd($createTime);
		$listRecordingRequest = new WebexXmlListRecordingInRecycleBinRequest();
		$listRecordingRequest->setCreateTimeScope($createTimeScope);
		$listRecordingRequest->setListControl($listControl);
		try
		{
			$listRecordingResponse = $this->webexClient->send($listRecordingRequest);
		}
		catch (Exception $e)
		{
			$this->log("Error occurred while trying to get file with creation time: {$createTime} from webex recyclebin: " . print_r($e, true));
			throw $e;
		}

		return $listRecordingResponse->getRecording();
	}

	/**
	 * @param string $recordName
	 * @param string $stringServiceTypes
	 * @return WebexXmlResponseBodyContent
	 * @throws Exception
	 */
    public function deleteRecordByName($recordName, $stringServiceTypes = null)
	{
		$listRecordingRequest = new WebexXmlListRecordingRequest();
		$listRecordingRequest->setRecordName($recordName);
		if($stringServiceTypes)
		{
			$servicesTypes = $this->stringServicesTypesToWebexXmlComServiceTypeType($stringServiceTypes);
			$listRecordingRequest->setServiceTypes($servicesTypes);
		}

		$listRecordingResponse = $this->webexClient->send($listRecordingRequest);
		if(!$listRecordingResponse)
		{
			throw new Exception("Record {$recordName} not found on the webex.");
		}

		$id = $listRecordingResponse[0]->getRecordingID();
		return $this->deleteRecordById($id);
	}

	/**
	 * @param string[] $stringServiceTypes
	 * @param long $startTime
	 * @param long $endTime
	 * @return WebexXmlResponseBodyContent[]
	 * @throws Exception
	 */
	public function deleteRecordingsByDates ($stringServiceTypes = null, $startTime = null, $endTime = null)
	{
		$result = array();
		$recordingsToDelete  = $this->listRecordings($stringServiceTypes, $startTime, $endTime);
		foreach ($recordingsToDelete as $recordingToDelete)
		{
			$result[] = $this->deleteRecordById($recordingToDelete);
		}

		return $result;
	}
}

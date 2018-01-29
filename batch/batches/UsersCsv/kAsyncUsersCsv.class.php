<?php
/**
 * @package Scheduler
 * @subpackage Copy
 */

/**
 * Will create csv of users and mail it
 *
 * @package Scheduler
 * @subpackage Users-Csv
 */
class KAsyncUsersCsv extends KJobHandlerWorker
{
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::USERS_CSV;
	}
	/**
	 * (non-PHPdoc)
	 * @see KBatchBase::getJobType()
	 */
	protected function getJobType()
	{
		return KalturaBatchJobType::USERS_CSV;
	}

	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		return $this->generateUsersCsv($job, $job->data);
	}

	/**
	 * Generate csv contains users info which will be later sent by mail
	 */
	private function generateUsersCsv(KalturaBatchJob $job, KalturaUsersCsvJobData $data)
	{
		$this->updateJob($job, "Start generating users csv", KalturaBatchJobStatus::PROCESSING);
		self::impersonate($job->partnerId);

		// Create local path for csv generation
		$directory = self::$taskConfig->params->localTempPath . DIRECTORY_SEPARATOR . $job->partnerId;
		KBatchBase::createDir($directory);
		$filePath = $directory . DIRECTORY_SEPARATOR . 'users_' .$job->partnerId.'_'.$job->id . '.csv';
		$data->outputPath = $filePath;
		KalturaLog::info("Temp file path: [$filePath]");

		//fill the csv with users data
		$csvFile = fopen($filePath,"w");
		$csvFile = $this->fillUsersCsv($csvFile, $data);
		fclose($csvFile);
		$this->setFilePermissions($filePath);
		self::unimpersonate();

		// Copy the report to shared location.
		$this->moveFile($job, $data, $job->partnerId);
		return $job;
	}


	/**
	 * the function move the file to the shared location
	 */
	protected function moveFile(KalturaBatchJob $job, KalturaUsersCsvJobData $data, $partnerId) {
		$fileName =  basename($data->outputPath);
		$directory = self::$taskConfig->params->sharedPath . DIRECTORY_SEPARATOR . $partnerId . DIRECTORY_SEPARATOR;
		KBatchBase::createDir($directory);
		$sharedLocation = self::$taskConfig->params->sharedPath . DIRECTORY_SEPARATOR . $partnerId . DIRECTORY_SEPARATOR. $fileName;

		$fileSize = kFile::fileSize($data->outputPath);
		rename($data->outputPath, $sharedLocation);
		$data->outputPath = $sharedLocation;

		$this->setFilePermissions($sharedLocation);
		if(!$this->checkFileExists($sharedLocation, $fileSize))
		{
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::NFS_FILE_DOESNT_EXIST, 'Failed to move users csv file', KalturaBatchJobStatus::RETRY);
		}

		return $this->closeJob($job, null, null, 'users CSV created successfully', KalturaBatchJobStatus::FINISHED, $data);
	}

	/**
	 * The function fills the csv file with the users data
	 */
	private function fillUsersCsv($csvFile, $data)
	{
		$filter = clone $data->filter;
		$pager = new KalturaFilterPager();
		$pager->pageSize = 500;
		$pager->pageIndex = 1;

		$additionalFields = $data->additionalFields;

		/*
		if(!$data->additionalFields && $data->metadataProfileId)
			$additionalFields = $this->extractAdditionalFieldsFromMetadataProfile($data->metadataProfileId);
		*/

		$csvFile = $this->addHeaderRowToCsv($csvFile, $additionalFields);
		do
		{
			try
			{
				$userList = KBatchBase::$kClient->user->listAction($filter, $pager);
			}
			catch(Exception $e)
			{
				KalturaLog::info("Couldn't list users on page: [$pager->pageIndex]" . $e->getMessage());
				return $csvFile;
			}
			$csvFile = $this->addUsersToCsv($userList->objects, $csvFile, $data->metadataProfileId, $additionalFields);
			$pager->pageIndex ++;
		}
		while ($pager->pageSize == count($userList->objects));

		return $csvFile;
	}


	/**
	 * Generate the first csv row containing the fields
	 */
	private function addHeaderRowToCsv($csvFile, $additionalFields)
	{
		$headerRow = 'User ID,First Name,Last Name,Email';
		foreach ($additionalFields as $field)
			$headerRow .= ','.$field->fieldName;
		fputcsv($csvFile, explode(',', $headerRow));
		return $csvFile;
	}


	/**
	 * The function grabs all the fields values for each user and adding them as a new row to the csv file
	 */
	private function addUsersToCsv($users, $csvFile, $metadataProfileId, $additionalFields)
	{
		if(!$users)
			return $csvFile;

		$userIds = array();
		$userIdToRow = array();

		foreach ($users as $user)
		{
			$userIds[] = $user->id;
			$userIdToRow = $this->initializeCsvRowValues($user, $additionalFields, $userIdToRow);
		}

		$usersMetadata = $this->retrieveUsersMetadata($userIds, $metadataProfileId);
		if($usersMetadata->objects)
			$userIdToRow = $this->fillAdditionalFieldsFromMetadata($usersMetadata, $additionalFields, $userIdToRow);

		foreach ($userIdToRow as $key=>$val)
			fputcsv($csvFile, $val);

		return $csvFile;
	}

	/**
	 * adds the default fields values and the additional fields as nulls
	 */
	private function initializeCsvRowValues($user, $additionalFields, $userIdToRow)
	{
		$defaultRowValues = array(
			'id' => $user->id,
			'firstName' => $user->firstName,
			'lastName' => $user->lastName,
			'email' =>$user->email
		);

		$additionalKeys = array();
		foreach ($additionalFields as $field)
			$additionalKeys[] = $field->fieldName;
		$additionalRowValues = array_fill_keys($additionalKeys, null);
		$row = array_merge($defaultRowValues, $additionalRowValues);
		$userIdToRow[$user->id] = $row;

		return $userIdToRow;
	}

	/**
	 * the function run over each additional field and returns the value for the given field xpath
	 */
	private function fillAdditionalFieldsFromMetadata($usersMetadata, $additionalFields, $userIdToRow)
	{
		foreach($usersMetadata->objects as $metadataObj)
		{
			foreach ($additionalFields as $field)
			{
				if($field->xpath)
				{
					KalturaLog::info("current field xpath: [$field->xpath]");
					$strValue = $this->getValueFromXmlElement($metadataObj->xml, $field->xpath);
					if($strValue)
					{
						$objectRow = $userIdToRow[$metadataObj->objectId];
						$objectRow[$field->fieldName] = $strValue;
						$userIdToRow[$metadataObj->objectId] = $objectRow;
					}
				}

			}
		}

		return $userIdToRow;
	}


	/**
	 * Retrieve all the metadata objects for all the users in specific page
	 */
	private function retrieveUsersMetadata($userIds, $metadataProfileId)
	{
		$result = null;
		$filter = new KalturaMetadataFilter();
		$filter->objectIdIn = implode(',', $userIds);
		$filter->metadataObjectTypeEqual = MetadataObjectType::USER;
		$filter->metadataProfileIdEqual = $metadataProfileId;
		try
		{
			$metadataClient = KalturaMetadataClientPlugin::get(KBatchBase::$kClient);
			$result = $metadataClient->metadata->listAction($filter);
		}
		catch(Exception $e)
		{
			KalturaLog::info("Couldn't list metadata objects for metadataProfileId: [$metadataProfileId]" . $e->getMessage());
		}
		return $result;
	}

	/**
	 * Extract specific value from xml using given xpath
	 */
	private function getValueFromXmlElement($xml, $xpath)
	{
		$strValue = null;
		try
		{
			$xmlObj = new SimpleXMLElement($xml);
		}
		catch(Exception $ex)
		{
			return null;
		}
		$value = $xmlObj->xpath($xpath);
		if(is_array($value) && count($value) == 1)
			$strValue = (string)$value[0];
		else if (count($value) == 1)
			KalturaLog::err("Unknown element in the base xml when quering the xpath: [$xpath]");

		return $strValue;
	}

	/**
	 * retrieve all the fields names and xpath in a given metadata profile and add them later to the csv
	 * this function should be in use in case metadata profile id is specified but there are no given xpath
	 * in additional fields object
	 */
	private function extractAdditionalFieldsFromMetadataProfile($metadataProfileId)
	{
		$additionalParams = array();
		try
		{
			$metadataPlugin = KalturaMetadataClientPlugin::get(KBatchBase::$kClient);
			$metadataProfileFieldList = $metadataPlugin->metadataProfile->listFields($metadataProfileId);
			foreach ($metadataProfileFieldList->objects as $field)
			{
				$data = array();
				$data['fieldName']  = $field->key;
				$data['xpath']  = $field->xPath;
				$additionalParams[] = $data;
			}
		}
		catch(Exception $e)
		{
			KalturaLog::info("Couldn't list metadata fields for metadataProfileId: [$metadataProfileId]" . $e->getMessage());
		}

		return $additionalParams;
	}

}


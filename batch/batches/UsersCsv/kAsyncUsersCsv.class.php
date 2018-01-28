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
	 * Will generate and send users csv
	 */
	private function generateUsersCsv(KalturaBatchJob $job, KalturaUsersCsvJobData $data)
	{
		$this->updateJob($job, "Start generating users csv", KalturaBatchJobStatus::PROCESSING);
		self::impersonate($job->partnerId);

		//TO DO: change the way we save the csv
		$csvFile = fopen("/opt/kaltura/users.csv","w");
		$csvFile = $this->fillUsersCsv($csvFile, $data);
		fclose($csvFile);

		self::unimpersonate();
		$this->closeJob($job, null, null, 'Finished Users Csv', KalturaBatchJobStatus::FINISHED);
		return $job;
	}


	private function fillUsersCsv($csvFile, $data)
	{
		$filter = clone $data->filter;
		$pager = new KalturaFilterPager();
		$pager->pageSize = 500;
		$pager->pageIndex = 1;

		$csvFile = $this->addHeaderRowToCsv($csvFile, $data->additionalFields);
		do
		{
			try
			{
				$userList = KBatchBase::$kClient->user->listAction($filter, $pager);
			}
			catch(Exception $e)
			{
				KalturaLog::info("Couldn't list users on page: [$pager->pageIndex]" . $e->getMessage());
			}
			$csvFile = $this->addUsersToCsv($userList->objects, $csvFile, $data->metadataProfileId, $data->additionalFields);
			$pager->pageIndex ++;
		}
		while ($pager->pageSize == count($userList->objects));

		return $csvFile;
	}


	private function addHeaderRowToCsv($csvFile, $additionalFields)
	{
		$headerRow = 'User ID,First Name,Last Name,Email';
		foreach ($additionalFields as $field)
			$headerRow .= ','.$field->fieldName;
		fputcsv($csvFile, explode(',', $headerRow));
		return $csvFile;
	}

	private function addUsersToCsv($users, $csvFile, $metadataProfileId, $additionalFields)
	{
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

	private function fillAdditionalFieldsFromMetadata($usersMetadata, $additionalFields, $userIdToRow)
	{
		foreach($usersMetadata->objects as $metadataObj)
		{
			foreach ($additionalFields as $field)
			{
				$strValue = $this->getValueFromXmlElement($metadataObj->xml, $field->xpath);
				if($strValue)
				{
					$objectRow = $userIdToRow[$metadataObj->objectId];
					$objectRow[$field->fieldName] = $strValue;
					$userIdToRow[$metadataObj->objectId] = $objectRow;
				}
			}
		}

		return $userIdToRow;
	}

	private function retrieveUsersMetadata($userIds, $metadataProfileId)
	{
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

		return $strValue;
	}

}


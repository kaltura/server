<?php
class kBulkUploadManager
{
	/**
	 * @param string $filePath the full path to the file 
	 * @param string $fileName the file name, use to extract the extension
	 * @param int $conversionProfileId
	 * @param BulkUploadType $bulkUploadType
	 * @throws APIException
	 * @return BatchJob
	 */
	public static function add($filePath, $fileName, $conversionProfileId = null, $bulkUploadType = null)
	{
		$fileInfo = pathinfo($fileName);
		$extension = strtolower($fileInfo["extension"]);
		
		$job = new BatchJob();
		$job->setPartnerId($this->getPartnerId());
		$job->setJobSubType($bulkUploadType);
		$job->save();
		
		$syncKey = $job->getSyncKey(BatchJob::FILE_SYNC_BATCHJOB_SUB_TYPE_BULKUPLOADCSV);
//		kFileSyncUtils::file_put_contents($syncKey, file_get_contents($csvFileData["tmp_name"]));
		try{
			kFileSyncUtils::moveFromFile($filePath, $syncKey, true);
		}
		catch(Exception $e)
		{
			throw new APIException(APIErrors::BULK_UPLOAD_CREATE_CSV_FILE_SYNC_ERROR);
		}
		
		$filePath = kFileSyncUtils::getLocalFilePathForKey($syncKey);
		
		$data = KalturaPluginManager::loadObject('kBulkUploadJobData', $bulkUploadType, array());
				
		$data->setFilePath($filePath);
		$data->setUserId($this->getKuser()->getPuserId());
		$data->setUploadedBy($this->getKuser()->getScreenName());
		if (!$conversionProfileId)
			$conversionProfileId = $this->getPartner()->getDefaultConversionProfileId();
			
		$kmcVersion = $this->getPartner()->getKmcVersion();
		$check = null;
		if($kmcVersion < 2)
		{
			$check = ConversionProfilePeer::retrieveByPK($conversionProfileId);
		}
		else
		{
			$check = conversionProfile2Peer::retrieveByPK($conversionProfileId);
		}
		if(!$check)
			throw new APIException(APIErrors::CONVERSION_PROFILE_ID_NOT_FOUND, $conversionProfileId);
		
		$data->setConversionProfileId($conversionProfileId);
			
		return kJobsManager::addJob($job, $data, BatchJobType::BULKUPLOAD);
	}
}
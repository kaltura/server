<?php
/**
 * The bulk upload manager does all the bulk upload logic for adding via bulk upload 
 * @author Roni
 *
 */
class kBulkUploadManager
{
	/**
	 * 
	 * The partenr for the bulk upload job
	 * @var partner
	 */
	private static $partner;
	
	/**
	 * 
	 * The kuser who performed the bulk operation
	 * @var kuser
	 */
	private static $kuser;
	
	/**
	 * @return the $partner
	 */
	public static function getPartner() {
		return kBulkUploadManager::$partner;
	}

	/**
	 * @return the $kuser
	 */
	public static function getKuser() {
		return kBulkUploadManager::$kuser;
	}

	/**
	 * @param partner $partner
	 */
	public static function setPartner($partner) {
		kBulkUploadManager::$partner = $partner;
	}

	/**
	 * @param kuser $kuser
	 */
	public static function setKuser($kuser) {
		kBulkUploadManager::$kuser = $kuser;
	}

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
		$job->setPartnerId(kBulkUploadManager::getPartner());
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
		$data->setUserId(kBulkUploadManager::getKuser()->getPuserId());
		$data->setUploadedBy(kBulkUploadManager::getKuser()->getScreenName());
		if (!$conversionProfileId)
			$conversionProfileId = kBulkUploadManager::getPartner()->getDefaultConversionProfileId();
			
		$kmcVersion = kBulkUploadManager::getPartner()->getKmcVersion();
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
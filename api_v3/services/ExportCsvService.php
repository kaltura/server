<?php
/**
 * Export CSV service is used to manage CSV exports of objects
 *
 * @service exportcsv
 * @package api
 * @subpackage services
 */
class ExportCsvService extends KalturaBaseService
{
	const SERVICE_NAME = "exportCsv";
	
	
	
	/**
	 * Creates a batch job that sends an email with a link to download a CSV containing a list of users
	 *
	 * @action userExportToCsv
	 * @actionAlias user.exportToCsv
	 * @param KalturaUserFilter $filter A filter used to exclude specific types of users
	 * @param int $metadataProfileId
	 * @param KalturaCsvAdditionalFieldInfoArray $additionalFields
	 * @return string
	 *
	 * @throws APIErrors::USER_EMAIL_NOT_FOUND
	 * @throws MetadataErrors::INVALID_METADATA_PROFILE
	 * @throws MetadataErrors::METADATA_PROFILE_NOT_SPECIFIED
	 */
	public function userExportToCsvAction (KalturaUserFilter $filter = null, $metadataProfileId = null, $additionalFields = null)
	{
		if($metadataProfileId)
		{
			$metadataProfile = MetadataProfilePeer::retrieveByPK($metadataProfileId);
			if (!$metadataProfile || ($metadataProfile->getPartnerId() != $this->getPartnerId()))
				throw new KalturaAPIException(MetadataErrors::INVALID_METADATA_PROFILE, $metadataProfileId);
		}
		else
		{
			if($additionalFields->count)
				throw new KalturaAPIException(MetadataErrors::METADATA_PROFILE_NOT_SPECIFIED, $metadataProfileId);
		}
		
		if (!$filter)
			$filter = new KalturaUserFilter();
		$dbFilter = new kuserFilter();
		$filter->toObject($dbFilter);
		
		$kuser = $this->getKuser();
		if(!$kuser || !$kuser->getEmail())
			throw new KalturaAPIException(APIErrors::USER_EMAIL_NOT_FOUND, $kuser);
		
		$jobData = new kUsersCsvJobData();
		$jobData->setFilter($dbFilter);
		$jobData->setMetadataProfileId($metadataProfileId);
		$jobData->setAdditionalFields($additionalFields);
		$jobData->setUserMail($kuser->getEmail());
		$jobData->setUserName($kuser->getPuserId());
		
		kJobsManager::addExportCsvJob($jobData, $this->getPartnerId(), ExportObjectType::USER);
		
		return $kuser->getEmail();
	}
	
	
	/**
	 *
	 * Will serve a requested CSV
	 * @action serveCsv
	 *
	 *
	 * @param string $id - the requested file id
	 * @return string
	 */
	public function serveCsvAction($id)
	{
		$file_path = self::generateCsvPath($id, $this->getKs());
		
		return $this->dumpFile($file_path, 'text/csv');
	}
	
	/**
	 * Generic CSV file path generator - used from any action which calls the generateCsvPath
	 *
	 * @param string $id
	 * @param string $ks
	 * @return string
	 * @throws KalturaAPIException
	 */
	public static function generateCsvPath($id, $ks)
	{
		if(!preg_match('/^\w+\.csv$/', $id))
			throw new KalturaAPIException(KalturaErrors::INVALID_ID, $id);

		// KS verification - we accept either admin session or download privilege of the file
		if(!$ks->verifyPrivileges(ks::PRIVILEGE_DOWNLOAD, $id))
			KExternalErrors::dieError(KExternalErrors::ACCESS_CONTROL_RESTRICTED);

		$partnerId = kCurrentContext::getCurrentPartnerId();
		$commonCsvPath = '/content/exportcsv/';

		$fullPath = myContentStorage::getFSContentRootPath() . $commonCsvPath . $partnerId . DIRECTORY_SEPARATOR . $id;
		$storageBaseDir = myCloudUtils::getPartnerSharedStoargeBaseDir($partnerId);
		if($storageBaseDir)
		{
			$sharedStorageFullPath = $storageBaseDir . $commonCsvPath . myContentStorage::getScatteredPathFromIntId($partnerId) . DIRECTORY_SEPARATOR . $id;
			if (kFile::checkFileExists($sharedStorageFullPath))
			{
				$fullPath = $sharedStorageFullPath;
			}
		}
		return $fullPath;
	}
	
}
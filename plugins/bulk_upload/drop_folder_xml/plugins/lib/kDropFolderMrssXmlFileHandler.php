<?php
/**
 * @package plugins.DropFolderMrss
 * @subpackage model
 */
class kDropFolderMrssXmlFileHandler extends kDropFolderXmlFileHandler
{
	public function handlePendingDropFolderFile (DropFolder $folder, DropFolderFile $file)
	{
		if (!($file instanceof MrssDropFolderFile))
		{
			KalturaLog::err("Drop folder file does not match folder type");
			return;
		}
		
		$this->addXMLBulkUploadJob($folder, $file);
	}
	
	public function handlePurgedDropFolderFile (DropFolder $folder, DropFolderFile $file)
	{
		//Nothing to do
	}
	
	public function getBulkUploadType ()
	{
		return BulkUploadXmlPlugin::getBulkUploadTypeCoreValue(BulkUploadXmlType::XML);
	}
	
	/**
	 * Add bulk upload job
	 * @param DropFolder $folder
	 * @param DropFolderFile $leadDropFolderFile
	 * @throws Exception
	 */
	private function addXMLBulkUploadJob(DropFolder $folder, DropFolderFile $leadDropFolderFile)
	{
		/* @var $leadDropFolderFile MrssDropFolderFile */
		KalturaLog::debug('Adding BulkUpload job');
		try 
		{
			$coreBulkUploadType = BulkUploadXmlPlugin::getBulkUploadTypeCoreValue(BulkUploadXmlType::XML);
					
			$objectId = $leadDropFolderFile->getId();
			$objectType = DropFolderXmlBulkUploadPlugin::getBatchJobObjectTypeCoreValue(DropFolderBatchJobObjectType::DROP_FOLDER_FILE);
			$partner = PartnerPeer::retrieveByPK($folder->getPartnerId());
			
			$data = KalturaPluginManager::loadObject('kBulkUploadJobData', $coreBulkUploadType);
			/* @var $data kBulkUploadJobData */
			$data->setUploadedBy(kDropFolderXmlEventsConsumer::UPLOADED_BY);
			
			KalturaLog::debug("mrss xml path: " . $leadDropFolderFile->getMrssXmlPath());
			KalturaLog::debug("file exists: " . file_exists($leadDropFolderFile->getMrssXmlPath()));
			$data->setFilePath($leadDropFolderFile->getMrssXmlPath());
			$data->setFileName($leadDropFolderFile->getFileName());
						
			$objectData = new kBulkUploadEntryData();
			KalturaLog::debug('conversion profile id: '.$folder->getConversionProfileId());
			$objectData->setConversionProfileId($folder->getConversionProfileId());
			$data->setObjectData($objectData);
	
			$job = kJobsManager::addBulkUploadJob($partner, $data, $coreBulkUploadType, $objectId, $objectType);
			
			$this->setFileToProcessing ($leadDropFolderFile);
			return $job;
		}
		catch (Exception $e)
		{
			KalturaLog::err("Error adding BulkUpload job -".$e->getMessage());
			throw new Exception(DropFolderXmlBulkUploadPlugin::ERROR_ADDING_BULK_UPLOAD_MESSAGE, DropFolderXmlBulkUploadPlugin::getErrorCodeCoreValue(DropFolderXmlBulkUploadErrorCode::ERROR_ADDING_BULK_UPLOAD));
		}
	}
	
	protected function setFileToProcessing (DropFolderFile $leadDropFolderFile) 
	{
		$leadDropFolderFile->setLeadDropFolderFileId($leadDropFolderFile->getId());
		$leadDropFolderFile->setStatus(DropFolderFileStatus::PROCESSING);
		$leadDropFolderFile->save();
	}
}
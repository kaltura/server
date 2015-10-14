<?php
/**
 * @package plugins.FeedDropFolder
 * @subpackage model
 */
class kDropFolderFeedXmlFileHandler extends kDropFolderXmlFileHandler
{
	public function handlePendingDropFolderFile (DropFolder $folder, DropFolderFile $file)
	{
		if (!($file instanceof FeedDropFolderFile))
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
		/* @var $leadDropFolderFile FeedDropFolderFile */
		try 
		{
			$coreBulkUploadType = BulkUploadXmlPlugin::getBulkUploadTypeCoreValue(BulkUploadXmlType::XML);
					
			$objectId = $leadDropFolderFile->getId();
			$objectType = DropFolderXmlBulkUploadPlugin::getBatchJobObjectTypeCoreValue(DropFolderBatchJobObjectType::DROP_FOLDER_FILE);
			$partner = PartnerPeer::retrieveByPK($folder->getPartnerId());
			
			$data = KalturaPluginManager::loadObject('kBulkUploadJobData', $coreBulkUploadType);
			/* @var $data kBulkUploadJobData */
			$data->setUploadedBy(kDropFolderXmlEventsConsumer::UPLOADED_BY);
			
			KalturaLog::info("Feed XML path: " . $leadDropFolderFile->getFeedXmlPath());
			$data->setFilePath($leadDropFolderFile->getFeedXmlPath());
			$data->setFileName(basename($data->getFilePath()) . '.xml');
						
			$objectData = new kBulkUploadEntryData();
			KalturaLog::info('Conversion profile id: '.$folder->getConversionProfileId());
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
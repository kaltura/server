<?php
/**
 * @package Scheduler
 * @subpackage Bulk-Upload
 */
class DropFolderXmlBulkUploadEngine extends BulkUploadEngineXml
{
	/**
	 * The engine xsd file path
	 * @var string
	 */
	const DROP_FOLDER_XSD_PATH = "/../xml/ingestion.xsd";
	
	
	/**
	 * @param KSchedularTaskConfig $taskConfig
	 */
	public function __construct( KSchedularTaskConfig $taskConfig, KalturaClient $kClient, KalturaBatchJob $job)
	{
		parent::__construct($taskConfig, $kClient, $job);
		$this->setXsdFilePath(dirname(__FILE__) . self::DROP_FOLDER_XSD_PATH);
	}
	
	/* (non-PHPdoc)
	 * @see BulkUploadEngineXml::getResourceInstance()
	 */
	protected function getResourceInstance(SimpleXMLElement $elementToSearchIn)
	{
		if(isset($elementToSearchIn->dropFolderFileContentResource))
		{
			$resource = new KalturaDropFolderFileResource();
			$attributes = $elementToSearchIn->dropFolderFileContentResource->attributes();
			$resource->dropFolderFileId = (string)$attributes['dropFolderFileId'];
			return $resource;
		}
		
		return parent::getResourceInstance($elementToSearchIn);
	}
	
	/* (non-PHPdoc)
	 * @see BulkUploadEngineXml::getResourceInstance()
	 */
	protected function validateResource(KalturaResource $resource, SimpleXMLElement $elementToSearchIn)
	{
		if($resource instanceof KalturaDropFolderFileResource)
		{
			$fileId = $resource->dropFolderFileId;
			if (is_null($fileId)) {
				throw new KalturaBulkUploadXmlException("Drop folder id is null", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
			}
			
			$dropFolderFile = $this->kClient->dropFolderFile->get($fileId);
			if (!$dropFolderFile) {
				throw new KalturaBulkUploadXmlException("Cannot find drop folder file with id [$fileId]", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
			}
		}
		
		return parent::validateResource($resource, $elementToSearchIn);
	}
}
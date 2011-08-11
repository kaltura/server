<?php
/**
 * @package Scheduler
 * @subpackage Bulk-Upload
 */
class DropFolderXmlBulkUploadEngine extends BulkUploadEngineXml
{
	/* (non-PHPdoc)
	 * @see BulkUploadEngineXml::__construct()
	 */
	public function __construct( KSchedularTaskConfig $taskConfig, KalturaClient $kClient, KalturaBatchJob $job)
	{
		parent::__construct($taskConfig, $kClient, $job);
		
		$this->xsdFilePath = 'http://' . kConf::get('cdn_host') . '/api_v3/service/schema/action/serve/type/' . KalturaSchemaType::DROP_FOLDER_XML;
		if($taskConfig->params->xsdFilePath) 
			$this->xsdFilePath = $taskConfig->params->xsdFilePath;
	}
	
	/* (non-PHPdoc)
	 * @see BulkUploadEngineXml::getResourceInstance()
	 */
	protected function getResourceInstance(SimpleXMLElement $elementToSearchIn, $conversionProfileId)
	{
		if(isset($elementToSearchIn->dropFolderFileContentResource))
		{
			$resource = new KalturaDropFolderFileResource();
			$attributes = $elementToSearchIn->dropFolderFileContentResource->attributes();
			$resource->dropFolderFileId = (string)$attributes['dropFolderFileId'];
			return $resource;
		}
		
		return parent::getResourceInstance($elementToSearchIn, $conversionProfileId);
	}
	
	/* (non-PHPdoc)
	 * @see BulkUploadEngineXml::validateResource()
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
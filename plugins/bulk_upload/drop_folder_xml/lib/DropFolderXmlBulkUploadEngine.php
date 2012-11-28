<?php
/**
 * @package Scheduler
 * @subpackage Bulk-Upload
 */
class DropFolderXmlBulkUploadEngine extends BulkUploadEngineXml
{
	/**
	 * @var KalturaDropFolder
	 */
	private $dropFolder = null;
	
	/**
	 * @var KalturaDropFolderFile
	 */
	private $xmlDropFolderFile = null;
	
	/**
	 * @var KPhysicalDropFolderUtils
	 */
	private $physicalFileUtils = null;
	
	/**
	 * 
	 * @var array
	 */
	private $contentResourceNameToIdMap = null;
	
	/* (non-PHPdoc)
	 * @see BulkUploadEngineXml::__construct()
	 */
	public function __construct( KSchedularTaskConfig $taskConfig, KalturaClient $kClient, KalturaBatchJob $job)
	{
		parent::__construct($taskConfig, $kClient, $job);
		
		$this->xsdFilePath = 'http://' . kConf::get('cdn_host') . '/api_v3/index.php/service/schema/action/serve/type/' . KalturaSchemaType::DROP_FOLDER_XML;
		if($taskConfig->params->xsdFilePath) 
			$this->xsdFilePath = $taskConfig->params->xsdFilePath;
	}
	
	public function handleBulkUpload() 
	{
		KalturaLog::debug("Starting BulkUpload for XML drop folder file with id [".$this->job->jobObjectId.']');
		
		$this->impersonate();
		$dropFolderPlugin = KalturaDropFolderClientPlugin::get($this->kClient);
		$this->kClient->startMultiRequest();
		$dropFolderFile = $dropFolderPlugin->dropFolderFile->get($this->job->jobObjectId);
		$dropFolderPlugin->dropFolder->get($dropFolderFile->dropFolderId);
		list($this->xmlDropFolderFile, $this->dropFolder) = $this->kClient->doMultiRequest(); 
				
		$this->physicalFileUtils = new KPhysicalDropFolderUtils($this->dropFolder);
		$this->data->filePath = $this->physicalFileUtils->getLocalFilePath($this->xmlDropFolderFile->fileName, $this->xmlDropFolderFile->id);
		$this->setContentResourceFilesMap($dropFolderPlugin);
		$this->unimpersonate();
		parent::handleBulkUpload();
	}
	
	private function setContentResourceFilesMap(KalturaDropFolderClientPlugin $dropFolderPlugin)
	{
		$filter = new KalturaDropFolderFileFilter();
		$filter->dropFolderIdEqual = $this->dropFolder->id;
		$filter->leadDropFolderFileIdEqual = $this->xmlDropFolderFile->id;
		
		$pager = new KalturaFilterPager();
		$pager->pageSize = 500;
		$pager->pageIndex = 1;		
		
		$getNextPage = true;
		
		$this->contentResourceNameToIdMap = array();
		
		while($getNextPage)
		{
			$dropFolderFiles = $dropFolderPlugin->dropFolderFile->listAction($filter, $pager);
			foreach ($dropFolderFiles->objects as $dropFolderFile) 
			{
				$this->contentResourceNameToIdMap[$dropFolderFile->fileName] = $dropFolderFile->id;
			}
			
			if($dropFolderFiles->totalCount < $pager->pageSize)
				$getNextPage = false;
			else 
				$pager->pageIndex++;			
		}
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
			$filePath = (string)$attributes['filePath'];
			$resource->dropFolderFileId = $this->contentResourceNameToIdMap[$filePath];
			
			return $resource;
		}
		
		return parent::getResourceInstance($elementToSearchIn, $conversionProfileId);
	}
	
	/* (non-PHPdoc)
	 * @see BulkUploadEngineXml::validateResource()
	 */
	protected function validateResource(KalturaResource $resource, SimpleXMLElement $elementToSearchIn)
	{
		KalturaLog::debug('In validateResource');
		if($resource instanceof KalturaDropFolderFileResource)
		{			
			$fileId = $resource->dropFolderFileId;
			KalturaLog::debug('drop folder file id '.$fileId);			
			if (is_null($fileId)) {
				throw new KalturaBulkUploadXmlException("Drop folder id is null", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
			}
						
			$filePath = $this->getFilePath($elementToSearchIn);
			$this->validateFileSize($elementToSearchIn, $filePath);
			if($this->dropFolder->type == KalturaDropFolderType::LOCAL)
			{
				$this->validateChecksum($elementToSearchIn, $filePath);
			}
		}
		
		return parent::validateResource($resource, $elementToSearchIn);
	}
	
	private function getFilePath(SimpleXMLElement $elementToSearchIn)
	{
		KalturaLog::debug('In getFilePath');			
		$attributes = $elementToSearchIn->dropFolderFileContentResource->attributes();
		$filePath = (string)$attributes['filePath'];
		
		if(isset($filePath))
		{
			$filePath = $this->dropFolder->path.'/'.$filePath;
			if($this->dropFolder->type == KalturaDropFolderType::LOCAL)
				$filePath = realpath($filePath);
			return $filePath;
		}
		else
		{
			throw new KalturaBulkUploadXmlException("Can't validate file as file path is null", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
		}		
	}
	
	private function validateFileSize(SimpleXMLElement $elementToSearchIn, $filePath)
	{
		if(isset($elementToSearchIn->dropFolderFileContentResource->fileSize))
		{
			KalturaLog::debug("Validating file size");
			$fileSize = $this->physicalFileUtils->fileTransferMgr->fileSize($filePath);
			$xmlFileSize = (int)$elementToSearchIn->dropFolderFileContentResource->fileSize;
			if($xmlFileSize != $fileSize)
				throw new KalturaBulkUploadXmlException("File size is invalid for file [$filePath], Xml size [$xmlFileSize], actual size [$fileSize]", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
			KalturaLog::debug("Filesize [$fileSize] verified for local resource [$filePath]");
		}
	}
	
	private function validateChecksum(SimpleXMLElement $elementToSearchIn, $filePath)
	{
		if(isset($elementToSearchIn->dropFolderFileContentResource->fileChecksum))
		{
			if($elementToSearchIn->dropFolderFileContentResource->fileChecksum['type'] == 'sha1')
			{
				 $checksum = sha1_file($filePath);
			}
			else
			{
				$checksum = md5_file($filePath);
			}
			
			$xmlChecksum = (string)$elementToSearchIn->dropFolderFileContentResource->fileChecksum;
			if($xmlChecksum != $checksum)
			{
				throw new KalturaBulkUploadXmlException("File checksum is invalid for file [$filePath], Xml checksum [$xmlChecksum], actual checksum [$checksum]", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
			}
			KalturaLog::debug("Checksum [$checksum] verified for local resource [$filePath]");
		}
	}
}
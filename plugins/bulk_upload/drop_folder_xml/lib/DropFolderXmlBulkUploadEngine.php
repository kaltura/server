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
	 * @var kFileTransferMgr
	 */
	private $fileTransferMgr = null;
	
	/**
	 *
	 * @var array
	 */
	private $contentResourceNameToIdMap = null;
	
	/**
	 * XML provided KS info
	 * @var KalturaSessionInfo
	 */
	private $ksInfo = null;
	
	public function __construct(KalturaBatchJob $job)
	{
		parent::__construct($job);
		
		KBatchBase::impersonate($this->currentPartnerId);
		$dropFolderPlugin = KalturaDropFolderClientPlugin::get(KBatchBase::$kClient);
		KBatchBase::$kClient->startMultiRequest();
		$dropFolderFile = $dropFolderPlugin->dropFolderFile->get($this->job->jobObjectId);
		$dropFolderPlugin->dropFolder->get($dropFolderFile->dropFolderId);
		list($this->xmlDropFolderFile, $this->dropFolder) = KBatchBase::$kClient->doMultiRequest();
				
		$this->fileTransferMgr = KDropFolderFileTransferEngine::getFileTransferManager($this->dropFolder);
		$this->data->filePath = $this->getLocalFilePath($this->xmlDropFolderFile->fileName, $this->xmlDropFolderFile->id);
		
		KBatchBase::unimpersonate();
	}
	
	/* (non-PHPdoc)
	 * @see BulkUploadEngineXml::getSchemaType()
	 */
	protected function getSchemaType()
	{
		return KalturaSchemaType::DROP_FOLDER_XML;
	}
	
	/* (non-PHPdoc)
	 * @see BulkUploadEngineXml::handleBulkUpload()
	 */
	public function handleBulkUpload()
	{
		KalturaLog::debug("Starting BulkUpload for XML drop folder file with id [".$this->job->jobObjectId.']');
		
		KBatchBase::impersonate($this->currentPartnerId);
		$dropFolderPlugin = KalturaDropFolderClientPlugin::get(KBatchBase::$kClient);
		$this->setContentResourceFilesMap($dropFolderPlugin);
		KBatchBase::unimpersonate();
		
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
			
			if(count($dropFolderFiles->objects) < $pager->pageSize)
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
			$fileSize = $this->fileTransferMgr->fileSize($filePath);
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
	
	/**
	 * Local drop folder - constract full path
	 * Remote drop folder - download file to a local temp directory and return the temp file path
	 * @param string $fileName
	 * @param int $fileId
	 * @throws Exception
	 */
	protected function getLocalFilePath($fileName, $fileId)
	{
		$dropFolderFilePath = $this->dropFolder->path.'/'.$fileName;
	    
	    // local drop folder
	    if ($this->dropFolder->type == KalturaDropFolderType::LOCAL) 
	    {
	        $dropFolderFilePath = realpath($dropFolderFilePath);
	        return $dropFolderFilePath;
	    }
	    else
	    {
	    	// remote drop folder	
			$tempFilePath = tempnam(KBatchBase::$taskConfig->params->sharedTempPath, 'parse_dropFolderFileId_'.$fileId.'_');		
			$this->fileTransferMgr->getFile($dropFolderFilePath, $tempFilePath);
			$this->setFilePermissions ($tempFilePath);
			return $tempFilePath;
	    }			    		
	}
	
	protected function setFilePermissions ($filepath)
	{
		$chmod = 0640;
		if(KBatchBase::$taskConfig->getChmod())
			$chmod = octdec(KBatchBase::$taskConfig->getChmod());
			
		KalturaLog::debug("chmod($filepath, $chmod)");
		@chmod($filepath, $chmod);
		
		$chown_name = KBatchBase::$taskConfig->params->fileOwner;
		if ($chown_name) {
			KalturaLog::debug("Changing owner of file [$filepath] to [$chown_name]");
			@chown($filepath, $chown_name);
		}
	}
	
	protected function validate()
	{
		$isValid = parent::validate();
		
		if($this->dropFolder->shouldValidateKS){
			$this->validateKs();		
		}
		
		return $isValid;
	}
	
	protected function validateKs()
	{
		//Retrieve the KS from within the XML
		$xdoc = new SimpleXMLElement($this->xslTransform($this->data->filePath));
		$xmlKs = $xdoc->ks;
		
		//Get session info
		KBatchBase::impersonate($this->currentPartnerId);
		try{
			$this->ksInfo = KBatchBase::$kClient->session->get($xmlKs);	
		}
		catch (Exception $e){
			KBatchBase::unimpersonate();
			throw new KalturaBatchException("KS [$xmlKs] validation failed for [{$this->job->id}], $errorMessage", KalturaBatchJobAppErrors::BULK_VALIDATION_FAILED);
		}
		KBatchBase::unimpersonate();
		
		//validate ks is still valid
		$currentTime = time();
		if($currentTime > $this->ksInfo->expiry){
			throw new KalturaBatchException("KS validation failed for [{$this->job->id}], ks provided in XML Expired", KalturaBatchJobAppErrors::BULK_VALIDATION_FAILED);
		}
	}
	
	/**
	 * Validates the given item's user id is identical to the user id on the KS
	 * @param SimpleXMLElement $item
	 */
	protected function validateItem(SimpleXMLElement $item)
	{
		if($this->dropFolder->shouldValidateKS){
			if($item->userId != $this->ksInfo->userId)
				throw new KalturaBulkUploadXmlException("KS user ID [" . $this->ksInfo->userId . "] does not match item user ID [" . $item->userId . "]", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
		}
			
		parent::validateItem($item);
	}
}
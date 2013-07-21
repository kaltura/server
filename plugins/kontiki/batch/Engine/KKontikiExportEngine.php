<?php
/**
*/
class KKontikiExportEngine extends KExportEngine 
{
    protected $partnerId;
    
	/**
	 * @var KontikiAPWrapper
	 */
	protected $kontikiAPIWrapper;
	
    protected static $pending_statuses = array ("PENDING_RESTART","RESTARTING","PENDING","PROCESSING","TRANSCODE_QUEUED","TRANSCODE_DONE","TRANSCODING","UPLOADING","SCANNING","ENCRYPTING","ENCRYPT_DONE","SIGNING","SIGN_DONE","RESIZING_THUMBNAILS","RESIZING_THUMBNAILS_DONE","PUBLISHING","PENDING_APPROVAL");
    
    protected static $failed_statuses = array("RESTART_FAILED","UNPROCESSABLE","TRANSCODE_FAILED","TRANSCODE_ERROR","TRANSCODE_CANCELLED","TRANSCODE_INTERRUPTED","UPLOAD_FAILED","SCAN_FAILED","SCAN_ERROR","ENCRYPT_FAILED","SIGN_FAILED","RESIZING_THUMBNAILS_FAILED","SMIL_FILE_GENERATION_FAILED","PUBLISHING_FAILED","PENDING_APPROVAL_FAIL","READY_FAIL" );
    
    const FINISHED_STATUS = 'READY';
	
	function __construct($data, $partnerId, $jobSubType)
	{
		parent::__construct($data, $jobSubType);
        $this->partnerId = $partnerId;
		$this->kontikiAPIWrapper = new KontikiAPIWrapper($data->entryPoint);
    }
	
	/* (non-PHPdoc)
	 * @see KExportEngine::export()
	 */
	public function export() 
	{
		KBatchBase::impersonate($this->partnerId);
		$url = KBatchBase::$kClient->flavorAsset->getUrl($this->data->flavorAssetId);
		KBatchBase::unimpersonate();
		$result = $this->kontikiAPIWrapper->addKontikiUploadResource('srv-' . base64_encode($this->data->serviceToken), $url);
		KalturaLog::info("Upload result: $result");
        
        $kontikiResult = new SimpleXMLElement($result);
        if (!$kontikiResult->moid)
            throw new kApplicativeException(KalturaBatchJobAppErrors::MISSING_PARAMETERS, "missing mandatory parameter moid");
                    
        $uploadMoid = strval($kontikiResult->moid);
        
        KBatchBase::impersonate($this->partnerId);
        KBatchBase::$kClient->startMultiRequest();
        $flavorAsset = KBatchBase::$kClient->flavorAsset->get($this->data->flavorAssetId);
        $entry = KBatchBase::$kClient->baseEntry->get($flavorAsset->entryId);
        $result = KBatchBase::$kClient->doMultiRequest();
        KBatchBase::unimpersonate();
        $contentResourceResult = $this->kontikiAPIWrapper->addKontikiVideoContentResource('srv-' . base64_encode($this->data->serviceToken), $uploadMoid, $result[1], $result[0]);
        KalturaLog::info("Content resource result: " . $contentResourceResult);
        $resultAsXml = new SimpleXMLElement($contentResourceResult);
        
        $this->data->contentMoid = strval($resultAsXml->content->moid);
        
        return false;
	}

	/* (non-PHPdoc)
	 * @see KExportEngine::verifyExportedResource()
	 */
	public function verifyExportedResource()
    {
		$contentResource = $this->kontikiAPIWrapper->getKontikiContentResource('srv-' . base64_encode($this->data->serviceToken), $this->data->contentMoid);
        if (!$contentResource)
        {
            throw new kKontikiApplicativeException(kKontikiApplicativeException::KONTIKI_API_EXCEPTION, "Failed to retrieve content resource");
        }
        
        KalturaLog::info("content resource: $contentResource");
        $contentResourceXml = new SimpleXMLElement($contentResource);
        if (!strval($contentResourceXml->content->contentStatusType))
        {
            throw new kKontikiApplicativeException(kKontikiApplicativeException::KONTIKI_API_EXCEPTION, "Unexpected: contentResource does not contain contentResourceStatusType");
        }
        
        $contentResourceStatus = strval($contentResourceXml->content->contentStatusType);
        if ($contentResourceStatus == self::FINISHED_STATUS)
            return true;
        if (in_array($contentResourceStatus, self::$pending_statuses))
        {
            return false;
        }
        if (in_array($contentResourceStatus, self::$failed_statuses))
        {
            $nodeName = 'related-upload';
            throw new kKontikiApplicativeException(kKontikiApplicativeException::KONTIKI_CONTENT_RESOURCE_EXCEPTION, $contentResourceXml->content->$nodeName->statusLog);
        }
	}
	
	/* (non-PHPdoc)
     * @see KExportEngine::verifyExportedResource()
     */
	public function delete ()
	{
	    $deleteResult = $this->kontikiAPIWrapper->deleteKontikiContentResource('srv-' . base64_encode($this->data->serviceToken), $this->data->contentMoid);
        if (!$deleteResult)
        {
            throw new kKontikiApplicativeException(kKontikiApplicativeException::KONTIKI_API_EXCEPTION, "Failed to delete content resource");
        }
        
        return true;
	}

	
}
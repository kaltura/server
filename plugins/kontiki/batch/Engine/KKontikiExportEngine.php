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
    
    protected static $failed_statuses = array("RESTART_FAILED","UNPROCESSABLE","TRANSCODE_FAILED","TRANSCODE_ERROR","TRANSCODE_CANCELLED","TRANSCODE_INTERRUPTED","UPLOADING_FAILED","SCAN_FAILED","SCAN_ERROR","ENCRYPT_FAILED","SIGN_FAILED","RESIZING_THUMBNAILS_FAILED","SMIL_FILE_GENERATION_FAILED","PUBLISHING_FAILED","PENDING_APPROVAL_FAIL","READY_FAIL" );
    
    const FINISHED_STATUS = 'READY';
	
	
	function __construct($data, $partnerId)
	{
		parent::__construct($data);
        $this->partnerId = $partnerId;
		$this->kontikiAPIWrapper = new KontikiAPIWrapper($data->serverUrl);
    }
	
	/* (non-PHPdoc)
	 * @see KExportEngine::export()
	 */
	public function export() 
	{
		KBatchBase::impersonate($this->partnerId);
		$url = KBatchBase::$kClient->flavorAsset->getUrl($this->data->flavorAssetId, null, true);
		$kontikiResult = $this->kontikiAPIWrapper->addKontikiUploadResource(KontikiPlugin::SERVICE_TOKEN_PREFIX . base64_encode($this->data->serviceToken), $url);
		KalturaLog::info("Upload result: $result");
        
        if (!$kontikiResult->moid)
            throw new kApplicativeException(KalturaBatchJobAppErrors::MISSING_PARAMETERS, "missing mandatory parameter moid");
                    
        $uploadMoid = strval($kontikiResult->moid);
        
        KBatchBase::$kClient->startMultiRequest();
        $flavorAsset = KBatchBase::$kClient->flavorAsset->get($this->data->flavorAssetId);
        $entry = KBatchBase::$kClient->baseEntry->get($flavorAsset->entryId);
        $result = KBatchBase::$kClient->doMultiRequest();
        KBatchBase::unimpersonate();
		if (!$result || !count($result))
		{
			throw new Exception();
		}
		else if (!($result[0]) instanceof KalturaFlavorAsset)
		{
			throw new KalturaException($result[0]['message'], $result[0]['code']);
		}
		else if (!($result[1]) instanceof KalturaBaseEntry)
		{
			throw new KalturaException($result[1]['message'], $result[1]['code']);
		}
        $contentResourceResult = $this->kontikiAPIWrapper->addKontikiVideoContentResource(KontikiPlugin::SERVICE_TOKEN_PREFIX . base64_encode($this->data->serviceToken), $uploadMoid, $result[1], $result[0]);
        KalturaLog::info("Content resource result: " . $contentResourceResult);
        
        $this->data->contentMoid = strval($contentResourceResult->content->moid);
        
        return false;
	}

	/* (non-PHPdoc)
	 * @see KExportEngine::verifyExportedResource()
	 */
	public function verifyExportedResource()
    {
		$contentResource = $this->kontikiAPIWrapper->getKontikiContentResource(KontikiPlugin::SERVICE_TOKEN_PREFIX . base64_encode($this->data->serviceToken), $this->data->contentMoid);
        if (!$contentResource)
        {
            throw new kApplicativeException(KalturaBatchJobAppErrors::EXTERNAL_ENGINE_ERROR, "Failed to retrieve Kontiki content resource");
        }
        
        KalturaLog::info("content resource:". $contentResource->asXML());
        if (!strval($contentResource->content->contentStatusType))
        {
            throw new kApplicativeException(KalturaBatchJobAppErrors::EXTERNAL_ENGINE_ERROR, "Unexpected: Kontiki contentResource does not contain contentResourceStatusType");
        }
        
        $contentResourceStatus = strval($contentResource->content->contentStatusType);
        if ($contentResourceStatus == self::FINISHED_STATUS)
            return true;
        if (in_array($contentResourceStatus, self::$pending_statuses))
        {
            return false;
        }
        if (in_array($contentResourceStatus, self::$failed_statuses))
        {
            $nodeName = 'related-upload';
            throw new kApplicativeException(KalturaBatchJobAppErrors::EXTERNAL_ENGINE_ERROR, $contentResource->content->$nodeName->statusLog);
        }
	}
	
	/* (non-PHPdoc)
     * @see KExportEngine::verifyExportedResource()
     */
	public function delete ()
	{
	    $deleteResult = $this->kontikiAPIWrapper->deleteKontikiContentResource(KontikiPlugin::SERVICE_TOKEN_PREFIX . base64_encode($this->data->serviceToken), $this->data->contentMoid);
        if (!$deleteResult)
        {
            throw new kApplicativeException(KalturaBatchJobAppErrors::EXTERNAL_ENGINE_ERROR, "Failed to delete content resource");
        }
        
        return true;
	}

	
}
<?php
class KFileTransferExportEngine extends KExportEngine
{
	protected $srcFile;
	
	protected $destFile;
	
	protected $protocol;
	
	/* (non-PHPdoc)
	 * @see KExportEngine::init()
	 */
	function __construct($data, $jobSubType) {
		KalturaLog::debug("initializing export process");
		parent::__construct($data);
		
		$this->protocol = $jobSubType;
		$this->srcFile = str_replace('//', '/', trim($this->data->srcFileSyncLocalPath));
		
		if(!KBatchBase::pollingFileExists($this->srcFile))
			throw new kTemporaryException("Source file {$this->srcFile} does not exist");
					
		$this->destFile = str_replace('//', '/', trim($this->data->destFileSyncStoredPath));
	}
	
	/* (non-PHPdoc)
	 * @see KExportEngine::export()
	 */
	function export() 
	{
		KalturaLog::debug("starting export process");
		$engineOptions = isset(KBatchBase::$taskConfig->engineOptions) ? KBatchBase::$taskConfig->engineOptions->toArray() : array();
		$engineOptions['passiveMode'] = $this->data->ftpPassiveMode;
		if($this->data instanceof KalturaAmazonS3StorageExportJobData)
			$engineOptions['filesAcl'] = $this->data->filesPermissionInS3;
			
		$engine = kFileTransferMgr::getInstance($this->protocol, $engineOptions);
		
		try
		{
			$engine->login($this->data->serverUrl, $this->data->serverUsername, $this->data->serverPassword);
		}
		catch(Exception $e)
		{
			throw new kTemporaryException($e->getMessage());
		}
	
		try
		{
			if (is_file($this->srcFile))
			{
				$engine->putFile($this->destFile, $this->srcFile, $this->data->force);
				if(KBatchBase::$taskConfig->params->chmod)
					$engine->chmod($this->destFile, KBatchBase::$taskConfig->params->chmod);
			}
			else if (is_dir($this->srcFile))
			{
				$filesPaths = kFile::dirList($this->srcFile);
				$destDir = $this->destFile;
				foreach ($filesPaths as $filePath)
				{
					$destFile = $destDir . '/' . basename($filePath);
					$engine->putFile($destFile, $filePath, $this->data->force);
					if(KBatchBase::$taskConfig->params->chmod)
						$engine->chmod($destFile, KBatchBase::$taskConfig->params->chmod);
				}
			}
		}
		catch(kFileTransferMgrException $e)
		{
			if($e->getCode() == kFileTransferMgrException::remoteFileExists)
				throw new kApplicativeException(KalturaBatchJobAppErrors::FILE_ALREADY_EXISTS, $e->getMessage());
			
			throw new Exception($e->getMessage(), $e->getCode());
		}
		
		return true;
	}

	/* (non-PHPdoc)
	 * @see KExportEngine::verifyExportedResource()
	 */
	function verifyExportedResource() {
		// TODO Auto-generated method stub
		
	}
    
    /* (non-PHPdoc)
     * @see KExportEngine::delete()
     */
    function delete()
    {
        $srcFile = str_replace('//', '/', trim($this->data->srcFileSyncLocalPath));
        $destFile = str_replace('//', '/', trim($this->data->destFileSyncStoredPath));
        

        $engineOptions = isset(KBatchBase::$taskConfig->engineOptions) ? KBatchBase::$taskConfig->engineOptions->toArray() : array();
        $engineOptions['passiveMode'] = $this->data->ftpPassiveMode;
        $engine = kFileTransferMgr::getInstance($this->protocol, $engineOptions);
        
        try{
            $engine->login($this->data->serverUrl, $this->data->serverUsername, $this->data->serverPassword);
            $engine->delFile($destFile);
        }
        catch(kFileTransferMgrException $ke)
        {
            throw new kApplicativeException($ke->getCode(), $ke->getMessage());
        }
        
        return true;
    }
}
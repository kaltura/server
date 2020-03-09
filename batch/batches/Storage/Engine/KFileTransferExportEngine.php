<?php
class KFileTransferExportEngine extends KExportEngine
{
	protected $srcFile;
	
	protected $destFile;
	
	protected $protocol;

	protected $encryptionKey;
	
	/* (non-PHPdoc)
	 * @see KExportEngine::init()
	 */
	function __construct($data, $jobSubType) {
		parent::__construct($data);
		
		$this->protocol = $jobSubType;
		$this->srcFile = str_replace('//', '/', trim($this->data->srcFileSyncLocalPath));
		$this->destFile = str_replace('//', '/', trim($this->data->destFileSyncStoredPath));
		$this->encryptionKey = $this->data->srcFileEncryptionKey;
	}
	
	/* (non-PHPdoc)
	 * @see KExportEngine::export()
	 */
	function export() 
	{
		if(!KBatchBase::pollingFileExists($this->srcFile))
			throw new kTemporaryException("Source file {$this->srcFile} does not exist");
							
		$engineOptions = isset(KBatchBase::$taskConfig->engineOptions) ? KBatchBase::$taskConfig->engineOptions->toArray() : array();
		$engineOptions['passiveMode'] = $this->data->ftpPassiveMode;
		$engineOptions['createLink'] = $this->data->createLink;
		if($this->data instanceof KalturaAmazonS3StorageExportJobData)
		{
			$engineOptions['filesAcl'] = $this->data->filesPermissionInS3;
			$engineOptions['s3Region'] = $this->data->s3Region;
			$engineOptions['sseType'] = $this->data->sseType;
			$engineOptions['sseKmsKeyId'] = $this->data->sseKmsKeyId;
			$engineOptions['signatureType'] = $this->data->signatureType;
			$engineOptions['endPoint'] = $this->data->endPoint;
		}

		if($this->data instanceof KalturaGCPStorageExportJobData)
		{
			$engineOptions['filesAcl'] = $this->data->filesPermissionInGCP;
			$engineOptions['bucketName'] = $this->data->bucketName;
			$engineOptions['keyFile'] = $this->data->keyFile;
		}
			
		$engine = kFileTransferMgr::getInstance($this->protocol, $engineOptions);
		
		try
		{
			$keyPairLogin = false;
			if($this->protocol == KalturaStorageProfileProtocol::SFTP) {
				$keyPairLogin = ($this->data->serverPrivateKey || $this->data->serverPublicKey);
			}
			
			if($keyPairLogin) {
				$privateKeyFile = $this->data->serverPrivateKey ? kFile::createTempFile($this->data->serverPrivateKey, 'privateKey', 0600) : null;
				$publicKeyFile = $this->data->serverPublicKey ? kFile::createTempFile($this->data->serverPublicKey, 'publicKey', 0600) : null;
				$engine->loginPubKey($this->data->serverUrl, $this->data->serverUsername, $publicKeyFile, $privateKeyFile, $this->data->serverPassPhrase);
			} else {	
				$engine->login($this->data->serverUrl, $this->data->serverUsername, $this->data->serverPassword);
			}
		}
		catch(Exception $e)
		{
			throw new kTemporaryException($e->getMessage());
		}
	
		try
		{
			if (is_file($this->srcFile))
			{
				$this->putFile($engine, $this->destFile, $this->srcFile, $this->data->force);
			}
			else if (is_dir($this->srcFile))
			{
				$filesPaths = kFile::dirList($this->srcFile);
				$destDir = $this->destFile;
				foreach ($filesPaths as $filePath)
				{
					$destFile = $destDir . '/' . basename($filePath);
					$this->putFile($engine, $destFile, $filePath, $this->data->force);
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
        $engineOptions = isset(KBatchBase::$taskConfig->engineOptions) ? KBatchBase::$taskConfig->engineOptions->toArray() : array();
        $engineOptions['passiveMode'] = $this->data->ftpPassiveMode;

	    if($this->data instanceof KalturaGCPStorageDeleteJobData)
	    {
		    $engineOptions['bucketName'] = $this->data->bucketName;
		    $engineOptions['keyFile'] = $this->data->keyFile;
	    }

        $engine = kFileTransferMgr::getInstance($this->protocol, $engineOptions);
        
        try{
            $engine->login($this->data->serverUrl, $this->data->serverUsername, $this->data->serverPassword);
            $engine->delFile($this->destFile);
        }
        catch(kFileTransferMgrException $ke)
        {
            throw new kApplicativeException($ke->getCode(), $ke->getMessage());
        }
        
        return true;
    }

	private function putFile(kFileTransferMgr $engine, $destFilePath, $srcFilePath, $force)
	{
		if (!$this->encryptionKey)
			$engine->putFile($destFilePath, $srcFilePath, $force);
		else
		{
			$tempPath = KBatchBase::createTempClearFile($srcFilePath, $this->encryptionKey);
			$engine->putFile($destFilePath, $tempPath, $force);
			unlink($tempPath);
		}
		if(KBatchBase::$taskConfig->params->chmod)
		{
			try {
				$engine->chmod($destFilePath, KBatchBase::$taskConfig->params->chmod);
			}
			catch(Exception $e){}
		}
	}
}
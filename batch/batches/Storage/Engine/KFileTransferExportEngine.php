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
		parent::__construct($data);
		
		$this->protocol = $jobSubType;
		$this->srcFile = str_replace('//', '/', trim($this->data->srcFileSyncLocalPath));
		$this->destFile = str_replace('//', '/', trim($this->data->destFileSyncStoredPath));
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
		}
			
		$engine = kFileTransferMgr::getInstance($this->protocol, $engineOptions);
		
		try
		{
			$keyPairLogin = false;
			if($this->protocol == KalturaStorageProfileProtocol::SFTP) {
				$keyPairLogin = ($this->data->serverPrivateKey || $this->data->serverPublicKey);
			}
			
			if($keyPairLogin) {
				$privateKeyFile = self::getTempFileWithContent($this->data->serverPrivateKey, 'privateKey');
				$publicKeyFile = self::getTempFileWithContent($this->data->serverPublicKey, 'publicKey');
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
				$engine->putFile($this->destFile, $this->srcFile, $this->data->force);
				if(KBatchBase::$taskConfig->params->chmod)
				{
					try {
					$engine->chmod($this->destFile, KBatchBase::$taskConfig->params->chmod);
					}
					catch(Exception $e){}
				}
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
					{
						try {
						$engine->chmod($destFile, KBatchBase::$taskConfig->params->chmod);
						}
						catch(Exception $e){}
					}
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
	
	/**
	 * Lazy saving of file content to a temporary path, the file will exist in this location until the temp files are purged
	 * @param string $fileContent
	 * @param string $prefix
	 * @return string path to temporary file location
	 */
	private static function getTempFileWithContent($fileContent, $prefix = '')
	{
		if(!$fileContent)
			return null;
		$tempDirectory = sys_get_temp_dir();
		$fileLocation = tempnam($tempDirectory, $prefix);
		file_put_contents($fileLocation, $fileContent);
		chmod($fileLocation, 0600);
		return $fileLocation;
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
}
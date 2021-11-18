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
		$srcTempFile = null;

		list($isRemote, $remoteUrl, $isDir) = kFile::resolveFilePath($this->srcFile);
		$realPathRemote = $isRemote && kFile::checkFileExists($this->srcFile);
		if( $realPathRemote || !KBatchBase::pollingFileExists($this->srcFile))
		{
			$externalUrl = $isRemote ? $remoteUrl : $this->data->externalUrl;
			if($externalUrl == null)
			{
				throw new kTemporaryException("Source file {$this->srcFile} does not exist, and no external URL provided");
			}

			$srcTempFile = kFile::fetchRemoteToLocal($this->srcFile, $externalUrl, $isDir,
				sys_get_temp_dir() . "/imports/", $this->data->flavorAssetId . "_" . basename($this->srcFile));

			if(!$srcTempFile)
			{
				throw new kTemporaryException("Source file {$this->srcFile} does not exist");
			}
			$this->srcFile = $srcTempFile;
		}

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
			$engineOptions['storageClass'] = $this->data->storageClass;
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
				$engine->loginPubKey($this->data->serverUrl, $this->data->serverUsername, $publicKeyFile, $privateKeyFile, $this->data->serverPassPhrase, $this->data->port);
			} else {	
				$engine->login($this->data->serverUrl, $this->data->serverUsername, $this->data->serverPassword, $this->data->port);
			}
		}
		catch(Exception $e)
		{
			$this->unlinkFileIfNeeded($srcTempFile);
			throw new kTemporaryException($e->getMessage());
		}

		try
		{
			if (kFile::isFile($this->srcFile))
			{
				$this->putFile($engine, $this->destFile, $this->srcFile, $this->data->force);
			}
			else if (kFile::isDir($this->srcFile))
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
			$this->unlinkFileIfNeeded($srcTempFile);

			if($e->getCode() == kFileTransferMgrException::remoteFileExists)
				throw new kApplicativeException(KalturaBatchJobAppErrors::FILE_ALREADY_EXISTS, $e->getMessage());
			
			throw new Exception($e->getMessage(), $e->getCode());
		}

		$this->unlinkFileIfNeeded($srcTempFile);
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

        if($this->data instanceof KalturaAmazonS3StorageExportJobData)
        {
            $engineOptions['s3Region'] = $this->data->s3Region;
        }

        $engine = kFileTransferMgr::getInstance($this->protocol, $engineOptions);
        
        try{
            $engine->login($this->data->serverUrl, $this->data->serverUsername, $this->data->serverPassword, $this->data->port);
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

	public function setExportDataFields($storageProfile, $fileSync)
	{
		if ($storageProfile->protocol == StorageProfileProtocol::S3)
		{
			$storageExportData = new KalturaAmazonS3StorageExportJobData();
		}
		else
		{
			$storageExportData = new KalturaStorageExportJobData();
		}

		$storageExportData = $this->fillStorageExportJobData($storageExportData, $storageProfile, $fileSync);
		$this->data = $storageExportData;
		$this->srcFile = str_replace('//', '/', trim($this->data->srcFileSyncLocalPath));
		$this->destFile = str_replace('//', '/', trim($this->data->destFileSyncStoredPath));
		$this->encryptionKey = $this->data->srcFileEncryptionKey;
	}

	protected function fillStorageExportJobData($storageExportData, $externalStorage, $fileSync, $force = false)
	{
		if($externalStorage->protocol == StorageProfileProtocol::KALTURA_DC)
		{
			$externalStorage->storageBaseDir = $externalStorage->storageUrl;
		}

		$storageExportData->serverUrl = $externalStorage->storageUrl;
		$storageExportData->serverUsername = $externalStorage->storageUsername;
		$storageExportData->serverPassword = $externalStorage->storagePassword;
		$storageExportData->serverPrivateKey = $externalStorage->privateKey;
		$storageExportData->serverPublicKey = $externalStorage->publicKey;
		$storageExportData->serverPassPhrase = $externalStorage->passPhrase;
		$storageExportData->ftpPassiveMode = $externalStorage->storageFtpPassiveMode;

		$storageExportData->srcFileSyncLocalPath = $fileSync->srcPath;
		$storageExportData->srcFileEncryptionKey = $fileSync->srcEncKey;
		$storageExportData->srcFileSyncId = $fileSync->id;

		$storageExportData->force = $force;
		$storageExportData->destFileSyncStoredPath = $externalStorage->storageBaseDir . '/' . $fileSync->filePath;
		$storageExportData->createLink = $externalStorage->createFileLink;

		if($externalStorage->protocol == StorageProfileProtocol::S3)
		{
			$storageExportData = $this->addS3FieldsToStorageData($storageExportData, $externalStorage, $fileSync);
		}

		return $storageExportData;
	}

	protected function addS3FieldsToStorageData($storageExportData, $externalStorage, $fileSync)
	{
		$storageExportData->filesPermissionInS3 = $externalStorage->filesPermissionInS3;
		$storageExportData->s3Region = $externalStorage->s3Region;
		$storageExportData->sseType = $externalStorage->sseType;
		$storageExportData->sseKmsKeyId = $externalStorage->sseKmsKeyId;
		$storageExportData->signatureType = $externalStorage->signatureType;
		$storageExportData->storageClass = $fileSync->storageClass;
		return $storageExportData;
	}

	protected function unlinkFileIfNeeded($tempFile)
	{
		if (!$tempFile)
		{
			return;
		}

		if (kFile::isDir($tempFile))
		{
			$dir = dir($tempFile);
			if (!$dir)
			{
				return null;
			}
			while ($dirFile = $dir->read())
			{
				if ($dirFile != "." && $dirFile != "..")
				{
					unlink($tempFile . DIRECTORY_SEPARATOR . $dirFile);
				}
			}
		}
		else
		{
			unlink($tempFile);
		}
	}

}


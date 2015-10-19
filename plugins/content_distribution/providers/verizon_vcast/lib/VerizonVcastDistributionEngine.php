<?php
/**
 * @package plugins.verizonVcastDistribution
 * @subpackage lib
 */
class VerizonVcastDistributionEngine extends DistributionEngine implements 
	IDistributionEngineSubmit,
	IDistributionEngineCloseSubmit,
	IDistributionEngineUpdate,
	IDistributionEngineCloseUpdate,
	IDistributionEngineDelete,
	IDistributionEngineCloseDelete
{
	const VERIZON_STATUS_PUBLISHED = 'PUBLISHED';
	const VERIZON_STATUS_PENDING = 'PENDING';
	
	/* (non-PHPdoc)
	 * @see IDistributionEngineSubmit::submit()
	 */
	public function submit(KalturaDistributionSubmitJobData $data)
	{
		$this->validateJobDataObjectTypes($data);
		
		$this->handleSubmit($data, $data->distributionProfile, $data->providerData);
		
		return true;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseSubmit::closeSubmit()
	 */
	public function closeSubmit(KalturaDistributionSubmitJobData $data)
	{
		$this->validateJobDataObjectTypes($data);
		
		// verizon didn't approve that this logic does work, for now just mark every submited xml as successful
		return true;
		
		$publishState = $this->fetchStatus($data);
		switch($publishState)
		{
			case self::VERIZON_STATUS_PUBLISHED:
				return true;
			case self::VERIZON_STATUS_PENDING:
				return false;
			default:
				throw new Exception("Unknown status [$publishState]");
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IDistributionEngineUpdate::update()
	 */
	public function update(KalturaDistributionUpdateJobData $data)
	{
		$this->validateJobDataObjectTypes($data);
		
		$this->handleSubmit($data, $data->distributionProfile, $data->providerData);
		
		return true;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IDistributionEngineCloseUpdate::closeUpdate()
	 */
	public function closeUpdate(KalturaDistributionUpdateJobData $data)
	{
		$this->validateJobDataObjectTypes($data);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IDistributionEngineDelete::delete()
	 */
	public function delete(KalturaDistributionDeleteJobData $data)
	{
		$this->validateJobDataObjectTypes($data);
		return true;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IDistributionEngineCloseDelete::closeDelete()
	 */
	public function closeDelete(KalturaDistributionDeleteJobData $data)
	{
		$this->validateJobDataObjectTypes($data);
	}
	
	/**
	 * @param KalturaDistributionJobData $data
	 * @throws Exception
	 */
	protected function validateJobDataObjectTypes(KalturaDistributionJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaVerizonVcastDistributionProfile))
			throw new Exception("Distribution profile must be of type KalturaVerizonVcastDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaVerizonVcastDistributionJobProviderData))
			throw new Exception("Provider data must be of type KalturaVerizonVcastDistributionJobProviderData");
	}
	
	/**
	 * @param string $path
	 * @param KalturaDistributionJobData $data
	 * @param KalturaVerizonDistributionProfile $distributionProfile
	 * @param KalturaVerizonDistributionJobProviderData $providerData
	 */
	public function handleSubmit(KalturaDistributionJobData $data, KalturaVerizonVcastDistributionProfile $distributionProfile, KalturaVerizonVcastDistributionJobProviderData $providerData)
	{
		$fileName = $data->entryDistribution->entryId . '_' . date('Y-m-d_H-i-s') . '.xml';
		KalturaLog::info('Sending file '. $fileName);
		
		$ftpManager = $this->getFTPManager($distributionProfile);
		$tmpFile = tmpfile();
		if ($tmpFile === false)
			throw new Exception('Failed to create tmp file');
		fwrite($tmpFile, $providerData->xml);
		rewind($tmpFile);
		$res = ftp_fput($ftpManager->getConnection(), $fileName, $tmpFile, FTP_ASCII);
		fclose($tmpFile);
		
		if ($res === false)
			throw new Exception('Failed to upload tmp file to ftp');
			
		$data->remoteId = $fileName;
		$data->sentData = $providerData->xml;
	}
	
	/**
	 * 
	 * @param KalturaVerizonVcastDistributionProfile $distributionProfile
	 * @return ftpMgr
	 */
	protected function getFTPManager(KalturaVerizonVcastDistributionProfile $distributionProfile)
	{
		$host = $distributionProfile->ftpHost;
		$login = $distributionProfile->ftpLogin;
		$pass = $distributionProfile->ftpPass;
		$engineOptions = isset(KBatchBase::$taskConfig->engineOptions) ? KBatchBase::$taskConfig->engineOptions->toArray() : array();
		$ftpManager = kFileTransferMgr::getInstance(kFileTransferMgrType::FTP, $engineOptions);
		$ftpManager->login($host, $login, $pass);
		return $ftpManager;
	}
	
	/**
	 * @param KalturaDistributionSubmitJobData $data
	 * @return string status
	 */
	protected function fetchStatus(KalturaDistributionJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaVerizonVcastDistributionProfile))
			return KalturaLog::err("Distribution profile must be of type KalturaVerizonVcastDistributionProfile");
	
		$fileArray = $this->fetchFilesList($data->distributionProfile);
		
		for	($i=0; $i<count($fileArray); $i++)
		{
			if (preg_match ( "/{$data->remoteId}.rcvd/" , $fileArray[$i] , $matches))
			{
				return self::VERIZON_STATUS_PUBLISHED;
			}
			else if (preg_match ( "/{$data->remoteId}.*.err/" , $fileArray[$i] , $matches))
			{
				$res = preg_split ('/\./', $matches[0]);
				return $res[1];			
			}
		}

		return self::VERIZON_STATUS_PENDING;
	}

	/**
	 * @param KalturaVerizonDistributionProfile $distributionProfile
	 */
	protected function fetchFilesList(KalturaVerizonVcastDistributionProfile $distributionProfile)
	{
		$host = $distributionProfile->ftpHost;
		$login = $distributionProfile->ftpLogin;
		$pass = $distributionProfile->ftpPass;
		
		$engineOptions = isset(KBatchBase::$taskConfig->engineOptions) ? KBatchBase::$taskConfig->engineOptions->toArray() : array();
		$fileTransferMgr = kFileTransferMgr::getInstance(kFileTransferMgrType::FTP, $engineOptions);
		if(!$fileTransferMgr)
			throw new Exception("FTP manager not loaded");
			
		$fileTransferMgr->login($host, $host, $pass);
		return $fileTransferMgr->listDir('/');
	}

}
<?php
class GenericDistributionEngine extends DistributionEngine implements 
	IDistributionEngineUpdate,
	IDistributionEngineSubmit,
	IDistributionEngineReport,
	IDistributionEngineDelete,
	IDistributionEngineCloseUpdate,
	IDistributionEngineCloseSubmit,
	IDistributionEngineCloseReport,
	IDistributionEngineCloseDelete
{
	protected $tempXmlPath;
	
	/* (non-PHPdoc)
	 * @see DistributionEngine::configure()
	 */
	public function configure(KSchedularTaskConfig $taskConfig)
	{
		if($taskConfig->params->tempXmlPath)
		{
			$this->tempXmlPath = $taskConfig->params->tempXmlPath;
		}
		else
		{
			KalturaLog::err("params.tempXmlPath configuration not supplied");
			$this->tempXmlPath = sys_get_temp_dir();
		}
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineSubmit::submit()
	 */
	public function submit(KalturaDistributionSubmitJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaGenericDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaGenericDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaGenericDistributionJobProviderData))
			KalturaLog::err("Provider data must be of type KalturaGenericDistributionJobProviderData");
		
		return $this->handleAction($data, $data->distributionProfile, $data->distributionProfile->submit, $data->providerData);
	}

	/**
	 * @param KalturaDistributionJobData $data
	 * @param KalturaGenericDistributionProfile $distributionProfile
	 * @param KalturaGenericDistributionJobProviderData $providerData
	 * @throws Exception
	 * @throws kFileTransferMgrException
	 * @return boolean true if finished, false if will be finished asynchronously
	 */
	public function handleAction(KalturaDistributionJobData $data, KalturaGenericDistributionProfile $distributionProfile, KalturaGenericDistributionProfileAction $distributionProfileAction, KalturaGenericDistributionJobProviderData $providerData)
	{
		if(!$providerData->xml)
			throw new Exception("XML data not supplied");
		
		$fileName = uniqid() . '.xml';
		$srcFile = $this->tempXmlPath . '/' . $fileName;
		$destFile = $distributionProfileAction->serverPath;
			
		if($distributionProfileAction->protocol != KalturaDistributionProtocol::HTTP && $distributionProfileAction->protocol != KalturaDistributionProtocol::HTTPS)
		{
			$destFile .= '/' . $fileName;
		}
		
		file_put_contents($srcFile, $providerData->xml);
		KalturaLog::debug("XML written to file [$srcFile]");
		
		$fileTransferMgr = kFileTransferMgr::getInstance($distributionProfileAction->protocol);
		if(!$fileTransferMgr)
			throw new Exception("File transfer manager type [$distributionProfileAction->protocol] not supported");
			
		$fileTransferMgr->login($distributionProfileAction->serverUrl, $distributionProfileAction->username, $distributionProfileAction->password, null, $distributionProfileAction->ftpPassiveMode);
		$fileTransferMgr->putFile($destFile, $srcFile, true, FTP_BINARY, $distributionProfileAction->httpFieldName, $distributionProfileAction->httpFileName);
		
//		TODO - parse http response and set the remoteId
//		$data->remoteId = $remoteId
		
		return true;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseSubmit::closeSubmit()
	 */
	public function closeSubmit(KalturaDistributionSubmitJobData $data)
	{
		// TODO Auto-generated method stub
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineDelete::delete()
	 */
	public function delete(KalturaDistributionDeleteJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaGenericDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaGenericDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaGenericDistributionJobProviderData))
			KalturaLog::err("Provider data must be of type KalturaGenericDistributionJobProviderData");
		
		return $this->handleAction($data, $data->distributionProfile, $data->distributionProfile->delete, $data->providerData);
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseDelete::closeDelete()
	 */
	public function closeDelete(KalturaDistributionDeleteJobData $data)
	{
		// TODO Auto-generated method stub
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseReport::closeReport()
	 */
	public function closeReport(KalturaDistributionFetchReportJobData $data)
	{
		// TODO Auto-generated method stub
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseUpdate::closeUpdate()
	 */
	public function closeUpdate(KalturaDistributionUpdateJobData $data)
	{
		// TODO Auto-generated method stub
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineReport::fetchReport()
	 */
	public function fetchReport(KalturaDistributionFetchReportJobData $data)
	{
		// TODO Auto-generated method stub
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineUpdate::update()
	 */
	public function update(KalturaDistributionUpdateJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaGenericDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaGenericDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaGenericDistributionJobProviderData))
			KalturaLog::err("Provider data must be of type KalturaGenericDistributionJobProviderData");
		
		return $this->handleAction($data, $data->distributionProfile, $data->distributionProfile->update, $data->providerData);
	}

}
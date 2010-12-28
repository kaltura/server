<?php
class HuluDistributionEngine extends DistributionEngine implements 
	IDistributionEngineUpdate,
	IDistributionEngineSubmit,
	IDistributionEngineReport,
	IDistributionEngineDelete
{
	private $domain = 'sftp.hulu.com';
	private $submitPath = '/';
	private $updatePath = '/';
	private $deletePath = '/';
	
	/* (non-PHPdoc)
	 * @see DistributionEngine::configure()
	 */
	public function configure(KSchedularTaskConfig $taskConfig)
	{
		if($taskConfig->params->huluDomain)
			$this->domain = $taskConfig->params->huluDomain;
			
		if($taskConfig->params->huluSubmitPath)
			$this->submitPath = $taskConfig->params->huluSubmitPath;
		if($taskConfig->params->huluUpdatePath)
			$this->updatePath = $taskConfig->params->huluUpdatePath;
		if($taskConfig->params->huluDeletePath)
			$this->deletePath = $taskConfig->params->huluDeletePath;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineSubmit::submit()
	 */
	public function submit(KalturaDistributionSubmitJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaHuluDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaHuluDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaHuluDistributionJobProviderData))
			KalturaLog::err("Provider data must be of type KalturaHuluDistributionJobProviderData");
		
		$this->handleSend($this->submitPath, $data, $data->distributionProfile, $data->providerData);
		
		// TODO do I have a way to validate the file uploaded?
		return true;
	}

	/**
	 * @param string $path
	 * @param KalturaDistributionJobData $data
	 * @param KalturaHuluDistributionProfile $distributionProfile
	 * @param KalturaHuluDistributionJobProviderData $providerData
	 * @throws Exception
	 */
	public function handleSend($path, KalturaDistributionJobData $data, KalturaHuluDistributionProfile $distributionProfile, KalturaHuluDistributionJobProviderData $providerData)
	{
		$username = $distributionProfile->username;
		$password = $distributionProfile->password;
		
		if(!$providerData->xml)
			throw new Exception("XML data not supplied");
		
		$fileName = uniqid() . '.xml';
		$srcFile = $this->tempXmlPath . '/' . $fileName;
		$destFile = "$path/{$providerData->xmlFileName}";
			
		file_put_contents($srcFile, $providerData->xml);
		KalturaLog::debug("XML written to file [$srcFile]");
		
		$fileTransferMgr = kFileTransferMgr::getInstance(kFileTransferMgrType::SFTP);
		if(!$fileTransferMgr)
			throw new Exception("SFTP manager not loaded");
			
		$fileTransferMgr->login($this->domain, $username, $password);
		$fileTransferMgr->putFile($destFile, $srcFile, true);
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineDelete::delete()
	 */
	public function delete(KalturaDistributionDeleteJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaHuluDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaHuluDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaHuluDistributionJobProviderData))
			KalturaLog::err("Provider data must be of type KalturaHuluDistributionJobProviderData");
		
		$this->handleSend($this->deletePath, $data, $data->distributionProfile, $data->providerData);
		
		// TODO - validate that the media deleted
		return true;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineReport::fetchReport()
	 */
	public function fetchReport(KalturaDistributionFetchReportJobData $data)
	{
		// TODO implement report fetching
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineUpdate::update()
	 */
	public function update(KalturaDistributionUpdateJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaHuluDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaHuluDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaHuluDistributionJobProviderData))
			KalturaLog::err("Provider data must be of type KalturaHuluDistributionJobProviderData");
		
		$this->handleSend($this->updatePath, $data, $data->distributionProfile, $data->providerData);
		
		// TODO - validate that the media updated
		return true;
	}
}
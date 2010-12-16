<?php
class MsnDistributionEngine extends DistributionEngine implements 
	IDistributionEngineUpdate,
	IDistributionEngineSubmit,
	IDistributionEngineReport,
	IDistributionEngineDelete,
	IDistributionEngineCloseUpdate,
	IDistributionEngineCloseSubmit,
	IDistributionEngineCloseReport,
	IDistributionEngineCloseDelete
{
	private $defaultDomain = 'www.the.default.domain'; // TODO
	private $submitPath = '/admin/services/storevideoandfiles.aspx';
	private $updatePath = '/admin/services/storevideoandfiles.aspx';
	private $deletePath = '/admin/services/'; // TODO
	private $fetchReportPath = '/admin/services/videobyuuid.aspx';
	
	/* (non-PHPdoc)
	 * @see DistributionEngine::configure()
	 */
	public function configure(KSchedularTaskConfig $taskConfig)
	{
		if($taskConfig->params->msnDefaultDomain)
			$this->defaultDomain = $taskConfig->params->msnDefaultDomain;
			
		if($taskConfig->params->msnSubmitPath)
			$this->submitPath = $taskConfig->params->msnSubmitPath;
			
		if($taskConfig->params->msnUpdatePath)
			$this->updatePath = $taskConfig->params->msnUpdatePath;
			
		if($taskConfig->params->msnDeletePath)
			$this->deletePath = $taskConfig->params->msnDeletePath;
			
		if($taskConfig->params->msnFetchReportPath)
			$this->fetchReportPath = $taskConfig->params->msnFetchReportPath;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineSubmit::submit()
	 */
	public function submit(KalturaDistributionSubmitJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaMsnDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaMsnDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaMsnDistributionJobProviderData))
			KalturaLog::err("Provider data must be of type KalturaMsnDistributionJobProviderData");
		
		$this->handleSubmit($data, $data->distributionProfile, $data->providerData);
		
		return false;
	}

	/**
	 * @param KalturaDistributionJobData $data
	 * @param KalturaMsnDistributionProfile $distributionProfile
	 * @param KalturaMsnDistributionJobProviderData $providerData
	 * @throws Exception
	 */
	public function handleSubmit(KalturaDistributionJobData $data, KalturaMsnDistributionProfile $distributionProfile, KalturaMsnDistributionJobProviderData $providerData)
	{
		$domain = $this->defaultDomain;
		if(!is_null($distributionProfile->domain))
			$domain = $distributionProfile->domain;
			
		$username = $distributionProfile->username;
		$password = $distributionProfile->password;
		
		$url = $domain . $this->submitPath;
		
		$entryId = $data->entryDistribution->entryId;
		$entry = $this->getEntry($entryId);
		$metadata = $this->getEntryMetadata($entryId);
		
		// TODO generate the xml and send it to the domain
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
		// TODO Auto-generated method stub
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
		// TODO Auto-generated method stub
	}

}
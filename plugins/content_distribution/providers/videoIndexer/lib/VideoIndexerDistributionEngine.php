<?php

/**
 * @package plugins.videoIndexerDistribution
 * @subpackage lib
 */
class VideoIndexerDistributionEngine extends DistributionEngine implements 
	IDistributionEngineUpdate,
	IDistributionEngineSubmit,
	IDistributionEngineReport,
	IDistributionEngineDelete
{

	/* (non-PHPdoc)
	 * @see IDistributionEngineSubmit::submit()
	 * 
	 * Demonstrate asynchronous external API usage
	 */
	public function submit(KalturaDistributionSubmitJobData $data)
	{
		// validates received object types
				
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaVideoIndexerDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaVideoIndexerDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaVideoIndexerDistributionJobProviderData))
			KalturaLog::err("Provider data must be of type KalturaVideoIndexerDistributionJobProviderData");
		
		// call the actual submit action
		$this->handleSubmit($data, $data->distributionProfile, $data->providerData);
		
		// always return false to be closed asynchronously by the closer
		return false;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineDelete::delete()
	 */
	public function delete(KalturaDistributionDeleteJobData $data)
	{
		// demonstrate asynchronous XML delivery usage from XSL
		
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaVideoIndexerDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaVideoIndexerDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaVideoIndexerDistributionJobProviderData))
			KalturaLog::err("Provider data must be of type KalturaVideoIndexerDistributionJobProviderData");
		
		$this->handleDelete($data, $data->distributionProfile, $data->providerData);
		
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionEngineUpdate::update()
	 * 
	 * demonstrate asynchronous XML delivery usage from template and uploading the media
	 */
	public function update(KalturaDistributionUpdateJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaVideoIndexerDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaVideoIndexerDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaVideoIndexerDistributionJobProviderData))
			KalturaLog::err("Provider data must be of type KalturaVideoIndexerDistributionJobProviderData");
		
		$this->handleUpdate($data, $data->distributionProfile, $data->providerData);
		
		return false;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineReport::fetchReport()
	 * 
	 * Demonstrate asynchronous http url parsing
	 */
	public function fetchReport(KalturaDistributionFetchReportJobData $data)
	{
		return false;
	}
	
	/**
	 * @param KalturaDistributionJobData $data
	 * @param KalturaVideoIndexerDistributionProfile $distributionProfile
	 * @param KalturaVideoIndexerDistributionJobProviderData $providerData
	 */
	protected function handleSubmit(KalturaDistributionJobData $data, KalturaVideoIndexerDistributionProfile $distributionProfile, KalturaVideoIndexerDistributionJobProviderData $providerData)
	{
		$accessTokenUrl = 'https://api.videoindexer.ai/auth/trial/Accounts/*account*/AccessToken?allowEdit=true';
		$accessTokenString = KCurlWrapper::getContent($accessTokenUrl, array('Ocp-Apim-Subscription-Key : *key*'));
		KalturaLog::log('Access token string: ' . $accessTokenString);

		$filePath = $providerData->filePath;
		$uploadUrl = "https://api.videoindexer.ai/trial/Accounts/*account*/Videos?accessToken='.$accessTokenString.'&name=testUpload1&streamingPreset=Default'";
		exec("curl -i " . $uploadUrl .  " -F 'data=@" . $filePath . "'");

	}
	
	/**
	 * @param KalturaDistributionJobData $data
	 * @param KalturaVideoIndexerDistributionProfile $distributionProfile
	 * @param KalturaVideoIndexerDistributionJobProviderData $providerData
	 */
	protected function handleDelete(KalturaDistributionJobData $data, KalturaVideoIndexerDistributionProfile $distributionProfile, KalturaVideoIndexerDistributionJobProviderData $providerData)
	{
	}
	
	/**
	 * @param KalturaDistributionJobData $data
	 * @param KalturaVideoIndexerDistributionProfile $distributionProfile
	 * @param KalturaVideoIndexerDistributionJobProviderData $providerData
	 */
	protected function handleUpdate(KalturaDistributionJobData $data, KalturaVideoIndexerDistributionProfile $distributionProfile, KalturaVideoIndexerDistributionJobProviderData $providerData)
	{

	}
}
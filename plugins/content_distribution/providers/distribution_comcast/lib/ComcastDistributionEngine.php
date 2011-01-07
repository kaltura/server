<?php
class ComcastDistributionEngine extends DistributionEngine implements 
	IDistributionEngineSubmit
{
	const FIELD_XML = 'xml';
	const FIELD_EMAIL = 'emailAddress';
	const FIELD_PASSWORD = 'password';
	const FIELD_ACCOUNT = 'account';
	
	private $submitPath = 'http://admin.theplatform.com/API/urn:service';
	private $updatePath = '';
	private $deletePath = '';
	private $fetchReportPath = '';
	
	
	/* (non-PHPdoc)
	 * @see DistributionEngine::configure()
	 */
	public function configure(KSchedularTaskConfig $taskConfig)
	{
		if($taskConfig->params->comcastSubmitPath)
			$this->submitPath = $taskConfig->params->comcastSubmitPath;
			
		if($taskConfig->params->comcastUpdatePath)
			$this->updatePath = $taskConfig->params->comcastUpdatePath;
			
		if($taskConfig->params->comcastDeletePath)
			$this->deletePath = $taskConfig->params->comcastDeletePath;
			
		if($taskConfig->params->comcastFetchReportPath)
			$this->fetchReportPath = $taskConfig->params->comcastFetchReportPath;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineSubmit::submit()
	 */
	public function submit(KalturaDistributionSubmitJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaComcastDistributionProfile))
			throw new Exception("Distribution profile must be of type KalturaComcastDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaComcastDistributionJobProviderData))
			throw new Exception("Provider data must be of type KalturaComcastDistributionJobProviderData");
		
		$results = $this->handleSend($this->submitPath, $data, $data->distributionProfile, $data->providerData);
		$matches = null;
		if(preg_match('/<uuid[^>]*>([^<]+)<\/uuid>/', $results, $matches))
		{
			$data->remoteId = $matches[1];
		}
		else 
		{
			throw new Exception("No uuid returned from Comcast");
		}
		
		return false;
	}

	/**
	 * @param string $path
	 * @param KalturaDistributionJobData $data
	 * @param KalturaComcastDistributionProfile $distributionProfile
	 * @param KalturaComcastDistributionJobProviderData $providerData
	 * @throws Exception
	 */
	protected function handleSend($url, KalturaDistributionJobData $data, KalturaComcastDistributionProfile $distributionProfile, KalturaComcastDistributionJobProviderData $providerData)
	{
		$params = array(
			self::FIELD_XML => $providerData->xml,
			self::FIELD_EMAIL => $distributionProfile->email,
			self::FIELD_PASSWORD => $distributionProfile->password,
			self::FIELD_ACCOUNT => $distributionProfile->account,
		);
		
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

//		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
//		curl_setopt($ch, CURLOPT_USERPWD, "{$username}:{$password}");

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		
		$results = curl_exec($ch);
		if(!$results)
		{
			$errNumber = curl_errno($ch);
			$errDescription = curl_error($ch);
			
			curl_close($ch);
		
			throw new Exception("Curl error [$errDescription] number [$errNumber]", $errNumber);
		}
		curl_close($ch);
		KalturaLog::debug("Comcast HTTP response:\n$results\n");
		$data->sentData = $providerData->xml;
		$data->results = $results;
		return $results;
	}
}
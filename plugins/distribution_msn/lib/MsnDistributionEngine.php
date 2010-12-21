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
	private $postFieldName = 'videoxml';
	
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
			
		if($taskConfig->params->msnPostFieldName)
			$this->postFieldName = $taskConfig->params->msnPostFieldName;
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
		
		$url = "https://{$domain}{$this->submitPath}";
		
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_USERAGENT, self::HTTP_USER_AGENT);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, "{$username}:{$password}");

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		
		$params = array($this->postFieldName => $providerData->xml);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		
		$results = curl_exec($ch);
		if(!$results)
		{
			$errNumber = curl_errno($ch);
			$errDescription = curl_error($ch);
			
			curl_close($ch);
		
			throw new Exception($errDescription, $errNumber);
		}
		curl_close($ch);
		
		KalturaLog::debug("MSN HTTP response:\n$results\n");
		$data->remoteId = trim($results);
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseSubmit::closeSubmit()
	 */
	public function closeSubmit(KalturaDistributionSubmitJobData $data)
	{
		$domain = $this->defaultDomain;
		if(!is_null($distributionProfile->domain))
			$domain = $distributionProfile->domain;
			
		$username = $distributionProfile->username;
		$password = $distributionProfile->password;
		
		$url = "https://{$domain}{$this->fetchReportPath}?uuid={$data->remoteId}";
		
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_USERAGENT, self::HTTP_USER_AGENT);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, true); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, "{$username}:{$password}");

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, false);
		
		$results = curl_exec($ch);
		if(!$results)
		{
			$errNumber = curl_errno($ch);
			$errDescription = curl_error($ch);
			
			curl_close($ch);
		
			echo "$errNumber: $errDescription\n\n";
			throw new Exception($errDescription, $errNumber);
		}
		curl_close($ch);
		
		var_dump($results);
		$xml = new DOMDocument();
		if(!$xml->loadXML($results))
			return false;
			
		$publishStateAttr = $xml->documentElement->attributes->getNamedItem('publishState');
		if(!$publishStateAttr)
			return false;
		$publishState = $publishStateAttr->value;
		
		switch($publishState)
		{
			case 'Published':
				return true;
				
			case 'Error':
				$liveSiteErrorNodes = $xml->documentElement->getElementsByTagName('liveSiteError');
				if($liveSiteErrorNodes->length)
				{
					$errDescription = $liveSiteErrorNodes->item(0)->textContent;
					throw new Exception("MSN error: $errDescription");
				}
				throw new Exception('Unknows MSN error');
				
			// TODO - check with MSN what other statuses are available
			
			default:
				return false;
		}
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
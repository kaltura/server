<?php
class YouTubeDistributionEngine extends DistributionEngine implements 
	IDistributionEngineUpdate,
	IDistributionEngineSubmit,
	IDistributionEngineReport,
	IDistributionEngineDelete,
	IDistributionEngineCloseUpdate,
	IDistributionEngineCloseSubmit,
	IDistributionEngineCloseDelete
{
	const USAGE_COUNTER_PLAYED = 1;
	const USAGE_COUNTER_EMAILED = 2;
	const USAGE_COUNTER_RATED = 3;
	const USAGE_COUNTER_BLOGGED = 4;
	const USAGE_COUNTER_REVIEWED = 5;
	const USAGE_COUNTER_BOOKMARKED = 6;
	const USAGE_COUNTER_PLAYBACKFAILED = 7;
	const USAGE_COUNTER_TIMESPENT = 8;
	const USAGE_COUNTER_RECOMMENDED = 9;

	
	/* (non-PHPdoc)
	 * @see DistributionEngine::configure()
	 */
	public function configure(KSchedularTaskConfig $taskConfig)
	{
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineSubmit::submit()
	 */
	public function submit(KalturaDistributionSubmitJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaYouTubeDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaYouTubeDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaYouTubeDistributionJobProviderData))
			KalturaLog::err("Provider data must be of type KalturaYouTubeDistributionJobProviderData");
		
		$results = $this->handleSend($this->submitPath, $data, $data->distributionProfile, $data->providerData);
		$data->remoteId = trim($results);
		
		return false;
	}

	/**
	 * @param string $path
	 * @param KalturaDistributionJobData $data
	 * @param KalturaYouTubeDistributionProfile $distributionProfile
	 * @param KalturaYouTubeDistributionJobProviderData $providerData
	 * @throws Exception
	 */
	public function handleSend($path, KalturaDistributionJobData $data, KalturaYouTubeDistributionProfile $distributionProfile, KalturaYouTubeDistributionJobProviderData $providerData)
	{
		// FIXME !!! hardcoded for testing
		$serverUrl = 'foxsports-kaltura.xfer.youtube.com';
		$loginName = 'foxsports-kaltura';
		$publicKeyFile = '/var/www/kaltura/app/plugins/distribution_youtube/id_rsa_youtube.pub';
		$privateKeyFile = '/var/www/kaltura/app/plugins/distribution_youtube/id_rsa_youtube';
		$fileTransferMgr = kFileTransferMgr::getInstance(kFileTransferMgrType::SFTP);
		$fileTransferMgr->loginPubKey($serverUrl, $distributionProfileAction->username, $publicKeyFile, $privateKeyFile);
		$fileTransferMgr->mkDir('test');
		$results = $fileTransferMgr->getResults();
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseSubmit::closeSubmit()
	 */
	public function closeSubmit(KalturaDistributionSubmitJobData $data)
	{
		return false;
		$publishState = $this->fetchStatus($data);
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
				KalturaLog::err("Unknown publishState [$publishState]");
				return false;
		}
	}

	/**
	 * @param KalturaDistributionSubmitJobData $data
	 * @return string status
	 */
	public function fetchStatus(KalturaDistributionSubmitJobData $data)
	{
		$xml = $this->fetchXML($data);
			
		$publishStateAttr = $xml->documentElement->attributes->getNamedItem('publishState');
		if($publishStateAttr)
			return $publishStateAttr->value;
				
		return null;
	}

	/**
	 * @param KalturaDistributionSubmitJobData $data
	 * @throws Exception
	 * @return DOMDocument
	 */
	public function fetchXML(KalturaDistributionSubmitJobData $data)
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
		
		$xml = new DOMDocument();
		if($xml->loadXML($results))
			return $xml;
			
		return null;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineDelete::delete()
	 */
	public function delete(KalturaDistributionDeleteJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaMsnDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaMsnDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaMsnDistributionJobProviderData))
			KalturaLog::err("Provider data must be of type KalturaMsnDistributionJobProviderData");
		
		$this->handleSend($this->deletePath, $data, $data->distributionProfile, $data->providerData);
		
		return false;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseDelete::closeDelete()
	 */
	public function closeDelete(KalturaDistributionDeleteJobData $data)
	{
		$publishState = $this->fetchStatus($data);
		switch($publishState)
		{
//			case 'Deleted': // TODO - what is the right status after delete?
//				return true;
				
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
				KalturaLog::err("Unknown publishState [$publishState]");
				return false;
		}
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseUpdate::closeUpdate()
	 */
	public function closeUpdate(KalturaDistributionUpdateJobData $data)
	{
		$publishState = $this->fetchStatus($data);
		switch($publishState)
		{
			case 'Published': // TODO - is that the right status after update?
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
				KalturaLog::err("Unknown publishState [$publishState]");
				return false;
		}
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineReport::fetchReport()
	 */
	public function fetchReport(KalturaDistributionFetchReportJobData $data)
	{
		$xml = $this->fetchXML($data);
			
		$usageNodes = $xml->documentElement->getElementsByTagName('usageItem');
		if(!$usageNodes->length)
			throw new Exception('usageItem node not found in XML');
			
		foreach($usageNodes as $usageNode)
		{
			$typeAttr = $usageNode->attributes->getNamedItem('counterType');
			$usageAttr = $usageNode->attributes->getNamedItem('totalCount');
			if(!$typeAttr || !$usageAttr)
				continue;
				
			switch($typeAttr->value)
			{
				case self::USAGE_COUNTER_PLAYED:
					$data->plays = $usageAttr->value;
					break;
					
				case self::USAGE_COUNTER_EMAILED:
					$data->providerData->emailed = $usageAttr->value;
					break;
					
				case self::USAGE_COUNTER_RATED:
					$data->providerData->rated = $usageAttr->value;
					break;
					
				case self::USAGE_COUNTER_BLOGGED:
					$data->providerData->blogged = $usageAttr->value;
					break;
					
				case self::USAGE_COUNTER_REVIEWED:
					$data->providerData->reviewed = $usageAttr->value;
					break;
					
				case self::USAGE_COUNTER_BOOKMARKED:
					$data->providerData->bookmarked = $usageAttr->value;
					break;
					
				case self::USAGE_COUNTER_PLAYBACKFAILED:
					$data->providerData->playbackFailed = $usageAttr->value;
					break;
					
				case self::USAGE_COUNTER_TIMESPENT:
					$data->providerData->timeSpent = $usageAttr->value;
					break;
					
				case self::USAGE_COUNTER_RECOMMENDED:
					$data->providerData->recommended = $usageAttr->value;
					break;
					
				default:
					KalturaLog::err("Unknown counterType [{$typeAttr->value}]");
					break;
			}
		}
				
		return true;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineUpdate::update()
	 */
	public function update(KalturaDistributionUpdateJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaMsnDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaMsnDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaMsnDistributionJobProviderData))
			KalturaLog::err("Provider data must be of type KalturaMsnDistributionJobProviderData");
		
		$this->handleSend($this->deletePath, $data, $data->distributionProfile, $data->providerData);
		
		return false;
	}

}
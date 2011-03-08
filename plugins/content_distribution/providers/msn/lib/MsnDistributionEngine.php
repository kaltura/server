<?php
/**
 * @package plugins.msnDistribution
 * @subpackage lib
 */
class MsnDistributionEngine extends DistributionEngine implements 
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

	
	private $submitPath = '/admin/services/storevideoandfiles.aspx';
	private $updatePath = '/admin/services/storevideoandfiles.aspx';
	private $deletePath = '/admin/services/storevideoandfiles.aspx'; // it's updating with end date to 5 days from now
	private $fetchReportPath = '/admin/services/videobyuuid.aspx';
	private $postFieldName = 'videoxml';
	
	/* (non-PHPdoc)
	 * @see DistributionEngine::configure()
	 */
	public function configure(KSchedularTaskConfig $taskConfig)
	{
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
			throw new Exception("Distribution profile must be of type KalturaMsnDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaMsnDistributionJobProviderData))
			throw new Exception("Provider data must be of type KalturaMsnDistributionJobProviderData");
		
		$results = $this->handleSend($this->submitPath, $data, $data->distributionProfile, $data->providerData);
		$matches = null;
		if(preg_match('/<uuid[^>]*>([^<]+)<\/uuid>/', $results, $matches))
		{
			$data->remoteId = $matches[1];
		}
		else 
		{
			throw new Exception("No uuid returned from MSN");
		}
		
		return false;
	}

	/**
	 * @param string $path
	 * @param KalturaDistributionJobData $data
	 * @param KalturaMsnDistributionProfile $distributionProfile
	 * @param KalturaMsnDistributionJobProviderData $providerData
	 * @throws Exception
	 */
	protected function handleSend($path, KalturaDistributionJobData $data, KalturaMsnDistributionProfile $distributionProfile, KalturaMsnDistributionJobProviderData $providerData)
	{
//		$pattern = '/<([^\/]+)\/>/';
//		$replacement = '<$1></$1>';
//		$xml = preg_replace($pattern, $replacement, $providerData->xml);
		
		$domain = $distributionProfile->domain;
		$username = $distributionProfile->username;
		$password = $distributionProfile->password;
		
		$url = "https://{$domain}{$path}";
		
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 

		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, "{$username}:{$password}");

		$params = http_build_query(array($this->postFieldName => $providerData->xml));
		
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
		KalturaLog::log("MSN HTTP response:\n$results\n");
		$data->sentData = $providerData->xml;
		$data->results = $results;
		return $results;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseSubmit::closeSubmit()
	 */
	public function closeSubmit(KalturaDistributionSubmitJobData $data)
	{
		$errDescription = null;
		$publishState = $this->fetchStatus($data, $errDescription);
		KalturaLog::info("publishState [$publishState]");
		switch($publishState)
		{
			case 'Published':
				return true;
				
			case 'Pending':
				return false;
				
			case 'Error':
			case 'Update Error':
				
				if($errDescription)
					throw new Exception("MSN error: $errDescription");
					
				throw new Exception('Unknows MSN error');
				
			default:
				KalturaLog::err("Unknown publishState [$publishState]");
				return false;
		}
	}

	/**
	 * @param KalturaDistributionJobData $data
	 * @return string status
	 */
	protected function fetchStatus(KalturaDistributionJobData $data, &$errDescription)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaMsnDistributionProfile))
			throw new Exception("Distribution profile must be of type KalturaMsnDistributionProfile");
	
		$xml = $this->fetchXML($data, $data->distributionProfile);
	
		$liveSiteErrorNodes = $xml->documentElement->getElementsByTagName('liveSiteError');
		if($liveSiteErrorNodes->length)
			$errDescription = $liveSiteErrorNodes->item(0)->textContent;
		
		$publishStateAttr = $xml->documentElement->attributes->getNamedItem('publishState');
		if($publishStateAttr)
			return $publishStateAttr->value;
				
		return null;
	}

	/**
	 * @param KalturaDistributionJobData $data
	 * @throws Exception
	 * @return DOMDocument
	 */
	protected function fetchXML(KalturaDistributionJobData $data, KalturaMsnDistributionProfile $distributionProfile)
	{
		$domain = $distributionProfile->domain;
		$username = $distributionProfile->username;
		$password = $distributionProfile->password;
		
		$url = "https://{$domain}{$this->fetchReportPath}?uuid={$data->remoteId}";
		
		$ch = curl_init();

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
		
			throw new Exception("Curl error [$errDescription] number [$errNumber]", $errNumber);
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
			throw new Exception("Distribution profile must be of type KalturaMsnDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaMsnDistributionJobProviderData))
			throw new Exception("Provider data must be of type KalturaMsnDistributionJobProviderData");
		
		$this->handleSend($this->deletePath, $data, $data->distributionProfile, $data->providerData);
		
		return false;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseDelete::closeDelete()
	 */
	public function closeDelete(KalturaDistributionDeleteJobData $data)
	{
		$errDescription = null;
		$publishState = $this->fetchStatus($data, $errDescription);
		switch($publishState)
		{
			case 'Published':
				return true;
				
			case 'Pending':
				return false;
				
			case 'Error':
			case 'Update Error':
				if($errDescription)
					throw new Exception("MSN error: $errDescription");
					
				throw new Exception('Unknows MSN error');
				
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
		$errDescription = null;
		$publishState = $this->fetchStatus($data, $errDescription);
		switch($publishState)
		{
			case 'Published':
				return true;
				
			case 'Pending':
				return false;
				
			case 'Error':
			case 'Update Error':
				if($errDescription)
					throw new Exception("MSN error: $errDescription");
					
				throw new Exception('Unknows MSN error');
				
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
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaMsnDistributionProfile))
			throw new Exception("Distribution profile must be of type KalturaMsnDistributionProfile");
	
		$xml = $this->fetchXML($data, $data->distributionProfile);
			
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
			throw new Exception("Distribution profile must be of type KalturaMsnDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaMsnDistributionJobProviderData))
			throw new Exception("Provider data must be of type KalturaMsnDistributionJobProviderData");
		
		$this->handleSend($this->updatePath, $data, $data->distributionProfile, $data->providerData);
		
		return false;
	}

}
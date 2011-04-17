<?php
/**
 * @package plugins.freewheelDistribution
 * @subpackage lib
 */
class FreewheelDistributionEngine extends DistributionEngine implements 
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

	protected $tempXmlPath;
	const HTTP_USER_AGENT = "\"Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.6) Gecko/2009011913 Firefox/3.0.6\"";
	const API_URL = "https://api.freewheel.tv/services/upload/bvi.xml";
	/* (non-PHPdoc)
	 * @see DistributionEngine::configure()
	 */
	public function configure(KSchedularTaskConfig $taskConfig)
	{
		$this->tempXmlPath = sys_get_temp_dir();
	
		if($taskConfig->params->freewheelSubmitPath)
			$this->submitPath = $taskConfig->params->freewheelSubmitPath;
			
		if($taskConfig->params->freewheelUpdatePath)
			$this->updatePath = $taskConfig->params->freewheelUpdatePath;
			
		if($taskConfig->params->freewheelDeletePath)
			$this->deletePath = $taskConfig->params->freewheelDeletePath;
			
		if($taskConfig->params->freewheelFetchReportPath)
			$this->fetchReportPath = $taskConfig->params->freewheelFetchReportPath;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineSubmit::submit()
	 */
	public function submit(KalturaDistributionSubmitJobData $data)
	{
		KalturaLog::debug("freewheel: submit");
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaFreewheelDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaFreewheelDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaFreewheelDistributionJobProviderData))
			KalturaLog::err("Provider data must be of type KalturaFreewheelDistributionJobProviderData");
		
		$data->remoteId = $this->handleSend($data, $data->distributionProfile, $data->providerData);
		
		return true;
	}

	/**
	 * @param string $path
	 * @param KalturaDistributionJobData $data
	 * @param KalturaFreewheelDistributionProfile $distributionProfile
	 * @param KalturaFreewheelDistributionJobProviderData $providerData
	 * @throws Exception
	 */
	public function handleSend(KalturaDistributionJobData $data, KalturaFreewheelDistributionProfile $distributionProfile, KalturaFreewheelDistributionJobProviderData $providerData)
	{
	
		$username = $distributionProfile->username;
		$password = $distributionProfile->password;
		$fileName = uniqid() . '.xml';
		$srcFile = $this->tempXmlPath . '/' . $fileName;
			
		
		$params = array("upload_file[]" => "@{$srcFile}");
//		$params = array("upload_file[]" => "@test.xml");
		
		KalturaLog::debug("freewheel: send");
//		KalturaLog::debug($providerData->xml);		
		if(!$providerData->xml)
			throw new Exception("XML data not supplied");

		file_put_contents($srcFile, $providerData->xml);

//		echo $srcFile;
		$ch = curl_init();

/*		curl_setopt($ch, CURLOPT_USERAGENT, self::HTTP_USER_AGENT);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		
		curl_setopt($ch, CURLOPT_NOSIGNAL, true);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, true); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, "{$username}:{$password}");

		curl_setopt($ch, CURLOPT_URL, self::API_URL);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RANGE, false);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_VERBOSE, true); 		
*/
//		curl_setopt($ch, CURLOPT_HTTPHEADER	, array('Expect:','Content-Type: multipart/form-data','Content-Disposition: form-data; name="upload_file[]";filename="a.xml"', 'X-FreeWheelToken: 39102300472f'));
		

		curl_setopt($ch, CURLOPT_URL,  self::API_URL);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_USERPWD, "{$username}:{$password}");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		$results = curl_exec($ch);
//		unlink($srcFile);
//		echo "1" . $results;

		if(!$results)
		{
			$errNumber = curl_errno($ch);
			$errDescription = curl_error($ch);
		
			if(!$results)
				throw new Exception($errDescription, $errNumber);
		}
//		echo $results;
		return '';
//		return $results;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseSubmit::closeSubmit()
	 */
	public function closeSubmit(KalturaDistributionSubmitJobData $data)
	{
		$publishState = $this->fetchStatus($data);
		switch($publishState)
		{
			case 'Published':
				return true;
				
			case 'Pending':
				return false;
				
			default:
				KalturaLog::err("Unknown publishState [$publishState]");
				throw new Exception("FREEWHEEL error: $publishState");
				return false;
		}
	}

	/**
	 * @param KalturaDistributionSubmitJobData $data
	 * @return string status
	 */
	public function fetchStatus(KalturaDistributionSubmitJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaFreewheelDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaFreewheelDistributionProfile");
	
		$fileArray = $this->fetchFilesList($data, $data->distributionProfile);
		
		for	($i=0; $i<count($fileArray); $i++)
		{
			if (preg_match ( "/{$data->remoteId}.rcvd/" , $fileArray[$i] , $matches))
			{
				return "Published";
			}
			else if (preg_match ( "/{$data->remoteId}.*.err/" , $fileArray[$i] , $matches))
			{
				$res = preg_split ("/\./", $matches[0]);
				return $res[1];			
			}
		}
				
		return "Pending";
	}

	/**
	 * @param KalturaDistributionSubmitJobData $data
	 * @throws Exception
	 * @return DOMDocument
	 */
	public function fetchFilesList(KalturaDistributionSubmitJobData $data, KalturaFreewheelDistributionProfile $distributionProfile)
	{
		$domain = $distributionProfile->domain;
		$username = $distributionProfile->username;
		$password = $distributionProfile->password;
		
		KalturaLog::debug("Listing content for [$path]");
		
		$fileTransferMgr = kFileTransferMgr::getInstance(kFileTransferMgrType::FTP);
		if(!$fileTransferMgr)
			throw new Exception("FTP manager not loaded");
			
		$fileTransferMgr->login($this->domain, $username, $password);
		return $fileTransferMgr->listDir($path);
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineDelete::delete()
	 */
	public function delete(KalturaDistributionDeleteJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaFreewheelDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaFreewheelDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaFreewheelDistributionJobProviderData))
			KalturaLog::err("Provider data must be of type KalturaFreewheelDistributionJobProviderData");
		
		$this->handleSend($this->deletePath, $data, $data->distributionProfile, $data->providerData);
		
		return false;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseDelete::closeDelete()
	 */
	public function closeDelete(KalturaDistributionDeleteJobData $data)
	{
		return true;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseUpdate::closeUpdate()
	 */
	public function closeUpdate(KalturaDistributionUpdateJobData $data)
	{
		return true;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineReport::fetchReport()
	 */
	public function fetchReport(KalturaDistributionFetchReportJobData $data)
	{
/*		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaFreewheelDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaFreewheelDistributionProfile");
	
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
	*?			
		return true;
	*/}

	/* (non-PHPdoc)
	 * @see IDistributionEngineUpdate::update()
	 */
	public function update(KalturaDistributionUpdateJobData $data)
	{
		KalturaLog::debug("freewheel: hooray update " . $data->providerData->xml);
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaFreewheelDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaFreewheelDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaFreewheelDistributionJobProviderData))
			KalturaLog::err("Provider data must be of type KalturaFreewheelDistributionJobProviderData");
		
		$this->handleSend($this->updatePath, $data, $data->distributionProfile, $data->providerData);
		
		return false;
	}

}
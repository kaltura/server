<?php
/**
 * @package plugins.ideticDistribution
 * @subpackage lib
 */
class IdeticDistributionEngine extends SftpDistributionEngine implements 
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

	const TEMP_DIRECTORY = 'idetic_distribution';
	private $domain = 'jukebox.mobitv.com';
	
	protected $tempXmlPath;
	protected $fieldValues;
	
	const TEMPLATE_XML_FILE_NAME = 'feed_template.xml';
	
	/* (non-PHPdoc)
	 * @see DistributionEngine::configure()
	 */
	public function configure()
	{
		parent::configure();
		
		$this->tempXmlPath = sys_get_temp_dir();
		if(KBatchBase::$taskConfig->params->ideticFetchReportPath)
			$this->fetchReportPath = KBatchBase::$taskConfig->params->ideticFetchReportPath;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineSubmit::submit()
	 */
	public function submit(KalturaDistributionSubmitJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaIdeticDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaIdeticDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaIdeticDistributionJobProviderData))
			KalturaLog::err("Provider data must be of type KalturaIdeticDistributionJobProviderData");
		
		$data->remoteId = $this->handleSend($data);
		
		return true;
	}

	/**
	 * @param string $path
	 * @param KalturaDistributionJobData $data
	 * @param KalturaIdeticDistributionProfile $distributionProfile
	 * @param KalturaIdeticDistributionJobProviderData $providerData
	 * @throws Exception
	 */
	public function handleDelete(KalturaDistributionJobData $data, KalturaIdeticDistributionProfile $distributionProfile, KalturaIdeticDistributionJobProviderData $providerData)
	{
		$domain = $distributionProfile->domain;
		$username = $distributionProfile->username;
		$password = $distributionProfile->password;
		$path = $distributionProfile->ftpPath;
		
		if (!isset($data->remoteId) || $data->remoteId == "")
		{
			return false;
		}
		else
		{
			$remoteId = $data->remoteId;
		}
		$fileName = $remoteId . '.xml';
		$destFile = "{$path}/{$fileName}";
			
		
		$engineOptions = isset(KBatchBase::$taskConfig->engineOptions) ? KBatchBase::$taskConfig->engineOptions->toArray() : array();
		$fileTransferMgr = kFileTransferMgr::getInstance(kFileTransferMgrType::FTP, $engineOptions);
		if(!$fileTransferMgr)
			throw new Exception("FTP manager not loaded");
			
		$fileTransferMgr->login($this->domain, $username, $password);
		if ($fileTransferMgr->fileExists($destFile))
		{
			$fileTransferMgr->delFile($destFile);
		}

		return $remoteId;
	}
	
	/**
	 * @param string $path
	 * @param KalturaDistributionJobData $data
	 * @throws Exception
	 */
	public function handleSend(KalturaDistributionJobData $data)
	{
		$distributionProfile = $data->distributionProfile;
		$providerData = $data->providerData;
		$path = $distributionProfile->ftpPath;
		
		$this->fieldValues = unserialize($providerData->fieldValues);
		if (!$this->fieldValues) {
			KalturaLog::err("fieldValues array is empty or null");
			throw new Exception("fieldValues array is empty or null");		
		}		
		$domain = $distributionProfile->domain;
		$username = $distributionProfile->username;
		$password = $distributionProfile->password;
		
		//xml creation
		$feedHelper = new IdeticDistributionFeedHelper(self::TEMPLATE_XML_FILE_NAME);
		$this->insertToXml($feedHelper);
			
		$feedHelper->setIndirectUploadUrl($providerData->flavorAssetUrl);
		$feedHelper->setThumbnail($providerData->thumbnailUrl);	

		//checksum
		//TODO:add $feedHelper->setChecksum()
		
		if (!isset($data->remoteId) || $data->remoteId == "")
		{
			$remoteId = uniqid();
		}
		else
		{
			$remoteId = $data->remoteId;
		}
		$fileName = $remoteId . '.xml';
		$srcFile = $this->tempXmlPath . '/' . $fileName;
		$destFile = "{$path}/{$fileName}";
			
		file_put_contents($srcFile, $feedHelper->getXmlString());
		KalturaLog::info("XML written to file [$srcFile]");
		
		$engineOptions = isset(KBatchBase::$taskConfig->engineOptions) ? KBatchBase::$taskConfig->engineOptions->toArray() : array();
		$fileTransferMgr = kFileTransferMgr::getInstance(kFileTransferMgrType::FTP, $engineOptions);
		if(!$fileTransferMgr)
			throw new Exception("FTP manager not loaded");
			
		$fileTransferMgr->login($this->domain, $username, $password);
		$fileTransferMgr->putFile($destFile, $srcFile, true);

		return $remoteId;	
	}
	
	/**
	 * 
	 * Inserts the fields values to xml
	 * @param IdeticDistributionFeedHelper $feedHelper
	 */
	protected function insertToXml(IdeticDistributionFeedHelper $feedHelper)
	{
		$feedHelper->setTitle($this->getValueForField(KalturaIdeticDistributionField::SHORT_TITLE));
		$feedHelper->setShortTitle($this->getValueForField(KalturaIdeticDistributionField::TITLE));
		$feedHelper->setKeyword($this->getValueForField(KalturaIdeticDistributionField::KEYWORD));
		$feedHelper->setSynopsis($this->getValueForField(KalturaIdeticDistributionField::SYNOPSIS));
		$feedHelper->setGenre($this->getValueForField(KalturaIdeticDistributionField::GENRE));
		$feedHelper->setSlot($this->getValueForField(KalturaIdeticDistributionField::SLOT));
		$feedHelper->setFolder($this->getValueForField(KalturaIdeticDistributionField::FOLDER));
		
		$startTime = $this->getValueForField(KalturaIdeticDistributionField::START_OF_AVAILABILITY);
		if (is_null($startTime)) {
		    $startTime = time() - 24*60*60;  // yesterday, to make the video public by default
		}
		$feedHelper->setStartTime(date('c', intval($startTime)));
		
		$endTime = $this->getValueForField(KalturaIdeticDistributionField::END_OF_AVAILABILITY);
		if ($endTime && intval($endTime)) {
            $feedHelper->setEndTime(date('c', $endTime));
		}
	}
	
	
	/** 
	 * returns the value of $fieldName in fieldValues array
	 * @param unknown_type $fieldName
	 */
	protected function getValueForField($fieldName)
	{
	    if (isset($this->fieldValues[$fieldName])) {
	        return $this->fieldValues[$fieldName];
	    }
	    return null;
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
				throw new Exception("IDETIC error: $publishState");
				return false;
		}
	}

	/**
	 * @param KalturaDistributionSubmitJobData $data
	 * @return string status
	 */
	public function fetchStatus(KalturaDistributionSubmitJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaIdeticDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaIdeticDistributionProfile");
	
		$fileArray = $this->fetchFilesList($data, $data->distributionProfile);
		
		for	($i=0; $i<count($fileArray); $i++)
		{
			if (preg_match ( "/{$data->remoteId}.rcvd/" , $fileArray[$i] , $matches))
			{
				return "Published";
			}
			else if (preg_match ( "/{$data->remoteId}.*.err/" , $fileArray[$i] , $matches))
			{
				$res = preg_split ('/\./', $matches[0]);
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
	public function fetchFilesList(KalturaDistributionSubmitJobData $data, KalturaIdeticDistributionProfile $distributionProfile)
	{
		$domain = $distributionProfile->domain;
		$username = $distributionProfile->username;
		$password = $distributionProfile->password;
		
		KalturaLog::info("Listing content for [$this->path]");
		
		$engineOptions = isset(KBatchBase::$taskConfig->engineOptions) ? KBatchBase::$taskConfig->engineOptions->toArray() : array();
		$fileTransferMgr = kFileTransferMgr::getInstance(kFileTransferMgrType::FTP, $engineOptions);
		if(!$fileTransferMgr)
			throw new Exception("FTP manager not loaded");
			
		$fileTransferMgr->login($this->domain, $username, $password);
		return $fileTransferMgr->listDir($this->path);
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineDelete::delete()
	 */
	public function delete(KalturaDistributionDeleteJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaIdeticDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaIdeticDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaIdeticDistributionJobProviderData))
			KalturaLog::err("Provider data must be of type KalturaIdeticDistributionJobProviderData");
			
		$this->handleDelete($data, $data->distributionProfile, $data->providerData);
		
		return true;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseDelete::closeDelete()
	 */
	public function closeDelete(KalturaDistributionDeleteJobData $data)
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
				throw new Exception("IDETIC error: $publishState");
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
			case 'Published':
				return true;
				
			case 'Pending':
				return false;
				
			default:
				KalturaLog::err("Unknown publishState [$publishState]");
				throw new Exception("IDETIC error: $publishState");
				return false;
		}
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineReport::fetchReport()
	 */
	public function fetchReport(KalturaDistributionFetchReportJobData $data)
	{
/*		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaIdeticDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaIdeticDistributionProfile");
	
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
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaIdeticDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaIdeticDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaIdeticDistributionJobProviderData))
			KalturaLog::err("Provider data must be of type KalturaIdeticDistributionJobProviderData");
		
		$this->handleSend($data);
		
		return true;
	}
	
	/**
	 * 
	 * @param KalturaYouTubeDistributionProfile $distributionProfile
	 * @return sftpMgr
	 */
	protected function getSFTPManager(KalturaYouTubeDistributionProfile $distributionProfile)
	{
		$serverUrl = $distributionProfile->sftpHost;
		$loginName = $distributionProfile->sftpLogin;
		$publicKeyFile = $this->getFileLocationForSFTPKey($distributionProfile->id, $distributionProfile->sftpPublicKey, 'publickey');
		$privateKeyFile = $this->getFileLocationForSFTPKey($distributionProfile->id, $distributionProfile->sftpPrivateKey, 'privatekey');
		$engineOptions = isset(KBatchBase::$taskConfig->engineOptions) ? KBatchBase::$taskConfig->engineOptions->toArray() : array();
		$sftpManager = kFileTransferMgr::getInstance(kFileTransferMgrType::SFTP, $engineOptions);
		$sftpManager->loginPubKey($serverUrl, $loginName, $publicKeyFile, $privateKeyFile);
		return $sftpManager;
	}



	
/**
	 * @param KalturaDistributionJobData $data
	 * @param KalturaYouTubeDistributionProfile $distributionProfile
	 * @param KalturaYouTubeDistributionJobProviderData $providerData
	 * @return Status XML or FALSE when status is not available yet
	 */
	protected function fetchStatusXml(KalturaDistributionJobData $data, KalturaYouTubeDistributionProfile $distributionProfile, KalturaYouTubeDistributionJobProviderData $providerData)
	{
		$statusFilePath = $providerData->sftpDirectory . '/' . 'status-' . $providerData->sftpMetadataFilename;
		$sftpManager = $this->getSFTPManager($distributionProfile);
		$statusXml = null;
		try 
		{
			KalturaLog::info('Trying to get the following status file: ['.$statusFilePath.']');
			$statusXml = $sftpManager->getFile($statusFilePath);
		}
		catch(kFileTransferMgrException $ex) // file is still missing
		{
			KalturaLog::info('File doesn\'t exist yet, retry later');
			return false;
		}
		
		return $statusXml;
	}

	public function getTempDirectory()
	{
		return self::TEMP_DIRECTORY;
	}
}
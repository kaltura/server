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
			if(!is_dir($this->tempXmlPath))
				kFile::fullMkfileDir($this->tempXmlPath, 0777, true);
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
		
		return $this->handleAction($data, $data->distributionProfile, $data->distributionProfile->submitAction, $data->providerData);
	}

	/**
	 * @param KalturaDistributionJobData $data
	 * @param KalturaGenericDistributionProfile $distributionProfile
	 * @param KalturaGenericDistributionJobProviderData $providerData
	 * @throws Exception
	 * @throws kFileTransferMgrException
	 * @return boolean true if finished, false if will be finished asynchronously
	 */
	protected function handleAction(KalturaDistributionJobData $data, KalturaGenericDistributionProfile $distributionProfile, KalturaGenericDistributionProfileAction $distributionProfileAction, KalturaGenericDistributionJobProviderData $providerData)
	{
		if(!$providerData->xml)
			throw new Exception("XML data not supplied");
		
		$fileName = uniqid() . '.xml';
		$srcFile = $this->tempXmlPath . '/' . $fileName;
		$destFile = $distributionProfileAction->serverPath;
			
		if($distributionProfileAction->protocol != KalturaDistributionProtocol::HTTP && $distributionProfileAction->protocol != KalturaDistributionProtocol::HTTPS)
			$destFile .= '/' . $fileName;
			
		$destFile = str_replace('{REMOTE_ID}', $data->remoteId, $destFile);
		
		file_put_contents($srcFile, $providerData->xml);
		KalturaLog::log("XML written to file [$srcFile]");
		
		$fileTransferMgr = kFileTransferMgr::getInstance($distributionProfileAction->protocol);
		if(!$fileTransferMgr)
			throw new Exception("File transfer manager type [$distributionProfileAction->protocol] not supported");
			
		$fileTransferMgr->login($distributionProfileAction->serverUrl, $distributionProfileAction->username, $distributionProfileAction->password, null, $distributionProfileAction->ftpPassiveMode);
		$fileTransferMgr->putFile($destFile, $srcFile, true, FTP_BINARY, $distributionProfileAction->httpFieldName, $distributionProfileAction->httpFileName);
		$results = $fileTransferMgr->getResults();
		
		if($results && is_string($results))
		{
			$data->results = $results;
			$parsedValues = $this->parseResults($results, $providerData->resultParserType, $providerData->resultParseData);
			if(count($parsedValues))
				list($data->remoteId) = $parsedValues;
		}
		$data->sentData = $providerData->xml;
		
		return true;
	}

	/**
	 * @param string $results
	 * @param KalturaGenericDistributionProviderParser $resultParserType
	 * @param string $resultParseData
	 * @return array of parsed values
	 */
	protected function parseResults($results, $resultParserType, $resultParseData)
	{
		switch($resultParserType)
		{
			case KalturaGenericDistributionProviderParser::XSL;
				$xml = new DOMDocument();
				if(!$xml->loadXML($results))
					return false;
		
				$xsl = new DOMDocument();
				$xsl->loadXML($resultParseData);
				
				$proc = new XSLTProcessor;
				$proc->importStyleSheet($xsl);
				
				$data = $proc->transformToDoc($xml);
				if(!$data)
					return false;
					
				return explode(',', $data);
				
			case KalturaGenericDistributionProviderParser::XPATH;
				$xml = new DOMDocument();
				if(!$xml->loadXML($results))
					return false;
		
				$xpath = new DOMXpath($xml);
				$elements = $xpath->query($resultParseData);
				if(is_null($elements))
					return false;
					
				$matches = array();
				foreach ($elements as $element)
					$matches[] = $element->textContent;
					
				return $matches;;
				
			case KalturaGenericDistributionProviderParser::REGEX;
				$matches = array();
				if(!preg_match("/$resultParseData/", $results, $matches))
					return false;
					
				return array_shift($matches);
				
			default;
				return false;
		}
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseSubmit::closeSubmit()
	 */
	public function closeSubmit(KalturaDistributionSubmitJobData $data)
	{
		// not supported
		return false;
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
		
		return $this->handleAction($data, $data->distributionProfile, $data->distributionProfile->deleteAction, $data->providerData);
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseDelete::closeDelete()
	 */
	public function closeDelete(KalturaDistributionDeleteJobData $data)
	{
		// not supported
		return false;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseReport::closeReport()
	 */
	public function closeReport(KalturaDistributionFetchReportJobData $data)
	{
		// not supported
		return false;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseUpdate::closeUpdate()
	 */
	public function closeUpdate(KalturaDistributionUpdateJobData $data)
	{
		// not supported
		return false;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineReport::fetchReport()
	 */
	public function fetchReport(KalturaDistributionFetchReportJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaGenericDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaGenericDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaGenericDistributionJobProviderData))
			KalturaLog::err("Provider data must be of type KalturaGenericDistributionJobProviderData");
		
		return $this->handleFetchReport($data, $data->distributionProfile, $data->distributionProfile->report, $data->providerData);
	}


	/**
	 * @param KalturaDistributionJobData $data
	 * @param KalturaGenericDistributionProfile $distributionProfile
	 * @param KalturaGenericDistributionJobProviderData $providerData
	 * @throws Exception
	 * @throws kFileTransferMgrException
	 * @return boolean true if finished, false if will be finished asynchronously
	 */
	protected function handleFetchReport(KalturaDistributionFetchReportJobData $data, KalturaGenericDistributionProfile $distributionProfile, KalturaGenericDistributionProfileAction $distributionProfileAction, KalturaGenericDistributionJobProviderData $providerData)
	{
		$srcFile = str_replace('{REMOTE_ID}', $data->remoteId, $distributionProfileAction->serverPath);
		
		KalturaLog::log("Fetch report from url [$srcFile]");
		$results = file_get_contents($srcFile);
	
		if($results && is_string($results))
		{
			$data->results = $results;
			$parsedValues = $this->parseResults($results, $providerData->resultParserType, $providerData->resultParseData);
			if(count($parsedValues))
				list($data->plays, $data->views) = $parsedValues;
		}
		
		return true;
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
		
		return $this->handleAction($data, $data->distributionProfile, $data->distributionProfile->updateAction, $data->providerData);
	}

}
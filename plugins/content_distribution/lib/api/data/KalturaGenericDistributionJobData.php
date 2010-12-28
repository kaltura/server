<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaGenericDistributionJobProviderData extends KalturaDistributionJobProviderData
{
	private static $actionAttributes = array(
		KalturaDistributionAction::SUBMIT => 'submitAction',
		KalturaDistributionAction::UPDATE => 'updateAction',
		KalturaDistributionAction::DELETE => 'deleteAction',
		KalturaDistributionAction::FETCH_REPORT => 'fetchReportAction',
	);
	
	/**
	 * @var string
	 */
	public $xml;
	
	/**
	 * @var string
	 */
	public $resultParseData;
	
	/**
	 * @var KalturaGenericDistributionProviderParser
	 */
	public $resultParserType;
	
	public function __construct(KalturaDistributionJobData $distributionJobData = null)
	{
		if(!$distributionJobData)
			return;
			
		$action = KalturaDistributionAction::SUBMIT;
		if($distributionJobData instanceof KalturaDistributionDeleteJobData)
			$action = KalturaDistributionAction::DELETE;
		if($distributionJobData instanceof KalturaDistributionUpdateJobData)
			$action = KalturaDistributionAction::UPDATE;
		if($distributionJobData instanceof KalturaDistributionFetchReportJobData)
			$action = KalturaDistributionAction::FETCH_REPORT;
			
		if(!($distributionJobData->distributionProfile instanceof KalturaGenericDistributionProfile))
		{
			KalturaLog::err("Distribution profile is not generic");
			return;
		}
		
		$this->loadProperties($distributionJobData, $distributionJobData->distributionProfile, $action);
	}
	
	public function loadProperties(KalturaDistributionJobData $distributionJobData, KalturaGenericDistributionProfile $distributionProfile, $action)
	{
		$actionName = self::$actionAttributes[$action];
		
		$genericProviderAction = GenericDistributionProviderActionPeer::retrieveByProviderAndAction($distributionProfile->genericProviderId, $action);
		if(!$genericProviderAction)
		{
			KalturaLog::err("Generic provider [{$distributionProfile->genericProviderId}] action [$actionName] not found");
			return;
		}
		
		if(!$distributionJobData->entryDistribution)
		{
			KalturaLog::err("Entry Distribution object not provided");
			return;
		}
		
		if(!$distributionProfile->$actionName->protocol)
			$distributionProfile->$actionName->protocol = $genericProviderAction->getProtocol();
		if(!$distributionProfile->$actionName->serverUrl)
			$distributionProfile->$actionName->serverUrl = $genericProviderAction->getServerAddress();
		if(!$distributionProfile->$actionName->serverPath)
			$distributionProfile->$actionName->serverPath = $genericProviderAction->getRemotePath();
		if(!$distributionProfile->$actionName->username)
			$distributionProfile->$actionName->username = $genericProviderAction->getRemoteUsername();
		if(!$distributionProfile->$actionName->password)
			$distributionProfile->$actionName->password = $genericProviderAction->getRemotePassword();
		if(!$distributionProfile->$actionName->ftpPassiveMode)
			$distributionProfile->$actionName->ftpPassiveMode = $genericProviderAction->getFtpPassiveMode();
		if(!$distributionProfile->$actionName->httpFieldName)
			$distributionProfile->$actionName->httpFieldName = $genericProviderAction->getHttpFieldName();
		if(!$distributionProfile->$actionName->httpFileName)
			$distributionProfile->$actionName->httpFileName = $genericProviderAction->getHttpFileName();
	
		$entry = entryPeer::retrieveByPKNoFilter($distributionJobData->entryDistribution->entryId);
		if(!$entry)
		{
			KalturaLog::err("Entry [" . $distributionJobData->entryDistribution->entryId . "] not found");
			return;
		}
			
		$mrss = kMrssManager::getEntryMrss($entry);
		if(!$mrss)
		{
			KalturaLog::err("MRSS not returned for entry [" . $entry->getId() . "]");
			return;
		}
			
		$xml = new DOMDocument();
		if(!$xml->loadXML($mrss))
		{
			KalturaLog::err("MRSS not is not valid XML:\n$mrss\n");
			return;
		}
		
		$key = $genericProviderAction->getSyncKey(GenericDistributionProviderAction::FILE_SYNC_DISTRIBUTION_PROVIDER_ACTION_MRSS_TRANSFORMER);
		if(kFileSyncUtils::fileSync_exists($key))
		{
			$xslPath = kFileSyncUtils::getLocalFilePathForKey($key);
			if($xslPath)
			{
				$xsl = new DOMDocument();
				$xsl->load($xslPath);
			
				// set variables in the xsl
				$varNodes = $xsl->getElementsByTagName('variable');
				foreach($varNodes as $varNode)
				{
					$nameAttr = $varNode->attributes->getNamedItem('name');
					if(!$nameAttr)
						continue;
						
					$name = $nameAttr->value;
					if($name && $distributionJobData->$name)
					{
						$varNode->textContent = $distributionJobData->$name;
						$varNode->appendChild($xsl->createTextNode($distributionJobData->$name));
						KalturaLog::debug("Set variable [$name] to [{$distributionJobData->$name}]");
					}
				}
				
				$proc = new XSLTProcessor;
				$proc->registerPHPFunctions();
				$proc->importStyleSheet($xsl);
				
				$xml = $proc->transformToDoc($xml);
				if(!$xml)
				{
					KalturaLog::err("Transform returned false");
					return;
				}
			}
		}
	
		$key = $genericProviderAction->getSyncKey(GenericDistributionProviderAction::FILE_SYNC_DISTRIBUTION_PROVIDER_ACTION_MRSS_VALIDATOR);
		if(kFileSyncUtils::fileSync_exists($key))
		{
			$xsdPath = kFileSyncUtils::getLocalFilePathForKey($key);
			if($xsdPath && !$xml->schemaValidate($xsdPath))	
			{
				KalturaLog::err("Inavlid XML:\n" . $xml->saveXML());
				KalturaLog::err("Schema [$xsdPath]:\n" . file_get_contents($xsdPath));	
				return;
			}
		}
		
		$this->xml = $xml->saveXML();
		
		$key = $genericProviderAction->getSyncKey(GenericDistributionProviderAction::FILE_SYNC_DISTRIBUTION_PROVIDER_ACTION_RESULTS_TRANSFORMER);
		if(kFileSyncUtils::fileSync_exists($key))
			$this->resultParseData = kFileSyncUtils::file_get_contents($key, true, false);
			
		$this->resultParserType = $genericProviderAction->getResultsParser();
	}
		
	private static $map_between_objects = array
	(
		"xml" ,
		"resultParseData" ,
		"resultParserType" ,
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}

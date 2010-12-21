<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaGenericDistributionJobProviderData extends KalturaDistributionJobProviderData
{
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
			
		$genericProviderAction = GenericDistributionProviderActionPeer::retrieveByProviderAndAction($distributionJobData->distributionProfile->genericProviderId, $action);
		
		if(is_null($distributionJobData->distributionProfile->protocol))
			$distributionJobData->distributionProfile->protocol = $genericProviderAction->getProtocol();
		if(is_null($distributionJobData->distributionProfile->serverUrl))
			$distributionJobData->distributionProfile->serverUrl = $genericProviderAction->getServerAddress();
		if(is_null($distributionJobData->distributionProfile->serverPath))
			$distributionJobData->distributionProfile->serverPath = $genericProviderAction->getRemotePath();
		if(is_null($distributionJobData->distributionProfile->username))
			$distributionJobData->distributionProfile->username = $genericProviderAction->getRemoteUsername();
		if(is_null($distributionJobData->distributionProfile->password))
			$distributionJobData->distributionProfile->password = $genericProviderAction->getRemotePassword();
		if(is_null($distributionJobData->distributionProfile->ftpPassiveMode))
			$distributionJobData->distributionProfile->ftpPassiveMode = $genericProviderAction->getFtpPassiveMode();
	
		$entry = entryPeer::retrieveByPK($distributionJobData->entryDistribution->entryId);
		$mrss = kMrssManager::getEntryMrss($entry);
		if(!$mrss)
			return;
			
		$xml = new DOMDocument();
		if(!$xml->loadXML($mrss))
			return;
		
		$key = $genericProviderAction->getSyncKey(GenericDistributionProviderAction::FILE_SYNC_DISTRIBUTION_PROVIDER_ACTION_MRSS_TRANSFORMER);
		if(kFileSyncUtils::fileSync_exists($key))
		{
			$xslPath = kFileSyncUtils::getLocalFilePathForKey($key);
			if($xslPath)
			{
				$xsl = new DOMDocument();
				$xsl->load($xslPath);
				
				$proc = new XSLTProcessor;
				$proc->importStyleSheet($xsl);
				
				$xml = $proc->transformToDoc($xml);
				if(!$xml)
					return;
			}
		}
	
		$key = $genericProviderAction->getSyncKey(GenericDistributionProviderAction::FILE_SYNC_DISTRIBUTION_PROVIDER_ACTION_MRSS_VALIDATOR);
		if(kFileSyncUtils::fileSync_exists($key))
		{
			$xsdPath = kFileSyncUtils::getLocalFilePathForKey($key);
			if($xsdPath && !$xml->schemaValidate($xsdPath))		
				return;
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

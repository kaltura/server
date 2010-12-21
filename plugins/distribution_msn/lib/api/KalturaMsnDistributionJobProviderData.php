<?php
class KalturaMsnDistributionJobProviderData extends KalturaDistributionJobProviderData
{
	public function __construct(KalturaDistributionJobData $distributionJobData = null)
	{
		if(!$distributionJobData)
			return;
			
		if($distributionJobData instanceof KalturaDistributionSubmitJobData)
			$this->generateSubmitXML($distributionJobData);
			
//		if($distributionJobData instanceof KalturaDistributionDeleteJobData)
//		if($distributionJobData instanceof KalturaDistributionUpdateJobData)
	}
			
	public function generateSubmitXML(KalturaDistributionJobData $distributionJobData)
	{
		$entry = entryPeer::retrieveByPK($distributionJobData->entryDistribution->entryId);
		$mrss = kMrssManager::getEntryMrss($entry);
		if(!$mrss)
			return;
			
		$xml = new DOMDocument();
		if(!$xml->loadXML($mrss))
			return;
		
		$xslPath = dirname(__FILE__) . '/../../xml/submit.xsl';
		$xsl = new DOMDocument();
		$xsl->load($xslPath);
		
		$proc = new XSLTProcessor;
		$proc->importStyleSheet($xsl);
		
		$xml = $proc->transformToDoc($xml);
		if(!$xml)
			return;
			
		$xsdPath = dirname(__FILE__) . '/../../xml/submit.xsd';
		if($xsdPath && !$xml->schemaValidate($xsdPath))		
			return;
		
		$this->xml = $xml->saveXML();
	}
}

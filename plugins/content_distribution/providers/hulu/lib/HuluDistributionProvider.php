<?php
/**
 * @package plugins.huluDistribution
 * @subpackage lib
 */
class HuluDistributionProvider implements IDistributionProvider
{
	/**
	 * @var HuluDistributionProvider
	 */
	protected static $instance;
	
	protected function __construct()
	{
		
	}
	
	/**
	 * @return HuluDistributionProvider
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new HuluDistributionProvider();
			
		return self::$instance;
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionProvider::getType()
	 */
	public function getType()
	{
		return HuluDistributionPlugin::getDistributionProviderTypeCoreValue(HuluDistributionProviderType::HULU);
	}
	
	/**
	 * @return string
	 */
	public function getName()
	{
		return 'Hulu';
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::isDeleteEnabled()
	 */
	public function isDeleteEnabled()
	{
		return false; // TODO check if delete enabled
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::isUpdateEnabled()
	 */
	public function isUpdateEnabled()
	{
		return false; // TODO check if update enabled
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::isReportsEnabled()
	 */
	public function isReportsEnabled()
	{
		return false; // TODO check if reports enabled
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::isScheduleUpdateEnabled()
	 */
	public function isScheduleUpdateEnabled()
	{
		return true;
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::useDeleteInsteadOfUpdate()
	 */
	public function useDeleteInsteadOfUpdate()
	{
		return false;
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::getJobIntervalBeforeSunrise()
	 */
	public function getJobIntervalBeforeSunrise()
	{
		return 0;
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::getJobIntervalBeforeSunset()
	 */
	public function getJobIntervalBeforeSunset()
	{
		return 0;
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::getUpdateRequiredEntryFields()
	 */
	public function getUpdateRequiredEntryFields($distributionProfileId = null)
	{
		if(kConf::hasParam('hulu_update_required_entry_fields'))
			return kConf::get('hulu_update_required_entry_fields');
			
		return array();
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::getUpdateRequiredMetadataXPaths()
	 */
	public function getUpdateRequiredMetadataXPaths($distributionProfileId = null)
	{
		if(kConf::hasParam('hulu_update_required_metadata_xpaths'))
			return kConf::get('hulu_update_required_metadata_xpaths');
			
		return array();
	}
	
	/**
	 * @param string $entryId
	 * @param KalturaHuluDistributionJobProviderData $providerData
	 * @return string
	 */
	public static function generateDeleteXML($entryId, KalturaHuluDistributionJobProviderData $providerData)
	{
		// TODO create the delete.xsl
		// TODO create delete validation xsd
		$xslPath = realpath(dirname(__FILE__) . '/../') . '/xml/delete.xsl';
		$xml = self::generateXML($entryId, $providerData, $xslPath);
		if(!$xml)
		{
			KalturaLog::err("No XML returned for entry [$entryId]");
			return null;
		}
			
		return $xml->saveXML();
	}
	
	/**
	 * @param string $entryId
	 * @param KalturaHuluDistributionJobProviderData $providerData
	 * @return string
	 */
	public static function generateUpdateXML($entryId, KalturaHuluDistributionJobProviderData $providerData)
	{
		// TODO create the update.xsl
		// TODO create update validation xsd
		$xslPath = realpath(dirname(__FILE__) . '/../') . '/xml/update.xsl';
		$xml = self::generateXML($entryId, $providerData, $xslPath);
		if(!$xml)
		{
			KalturaLog::err("No XML returned for entry [$entryId]");
			return null;
		}
			
		return $xml->saveXML();
	}
	
	/**
	 * @param string $entryId
	 * @param KalturaHuluDistributionJobProviderData $providerData
	 * @return string
	 */
	public static function generateSubmitXML($entryId, KalturaHuluDistributionJobProviderData $providerData)
	{
		$xslPath = realpath(dirname(__FILE__) . '/../') . '/xml/submit.xsl';
		// TODO create submit validation xsd
		$xml = self::generateXML($entryId, $providerData, $xslPath);
		if(!$xml)
		{
			KalturaLog::err("No XML returned for entry [$entryId]");
			return null;
		}
		
		return $xml->saveXML();
	}
	
	/**
	 * @param string $entryId
	 * @param KalturaHuluDistributionJobProviderData $providerData
	 * @param string $xslPath
	 * @return DOMDocument
	 */
	public static function generateXML($entryId, KalturaHuluDistributionJobProviderData $providerData, $xslPath, $xsdPath = null)
	{
		$entry = entryPeer::retrieveByPKNoFilter($entryId);
		$mrss = kMrssManager::getEntryMrss($entry);
		if(!$mrss)
		{
			KalturaLog::err("No MRSS returned for entry [$entryId]");
			return null;
		}
			
		$xml = new DOMDocument();
		if(!$xml->loadXML($mrss))
		{
			KalturaLog::err("Could not load MRSS as XML for entry [$entryId]");
			return null;
		}
		
		if(!file_exists($xslPath))
		{
			KalturaLog::err("XSL file not found [$xslPath]");
			return null;
		}
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
			if($name && $providerData->$name)
			{
				$varNode->textContent = $providerData->$name;
				$varNode->appendChild($xsl->createTextNode($providerData->$name));
				KalturaLog::debug("Set variable [$name] to [{$providerData->$name}]");
			}
		}

		$proc = new XSLTProcessor;
		$proc->registerPHPFunctions();
		$proc->importStyleSheet($xsl);
		
		$xml = $proc->transformToDoc($xml);
		if(!$xml)
		{
			KalturaLog::err("XML Transformation failed");
			return null;
		}
			
		if($xsdPath && file_exists($xsdPath) && !$xml->schemaValidate($xsdPath))
		{
			KalturaLog::err("Schema validation failed");		
			return null;
		}
		
		return $xml;
	}
}
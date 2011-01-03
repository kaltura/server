<?php
class ComcastDistributionProvider implements IDistributionProvider
{
	/**
	 * @var ComcastDistributionProvider
	 */
	protected static $instance;
	
	protected function __construct()
	{
		
	}
	
	/**
	 * @return ComcastDistributionProvider
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new ComcastDistributionProvider();
			
		return self::$instance;
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionProvider::getType()
	 */
	public function getType()
	{
		return ComcastDistributionPlugin::getDistributionProviderTypeCoreValue(ComcastDistributionProviderType::COMCAST);
	}
	
	/**
	 * @return string
	 */
	public function getName()
	{
		return 'Comcast';
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::isDeleteEnabled()
	 */
	public function isDeleteEnabled()
	{
		return false; // TODO - check if delete supported
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::isUpdateEnabled()
	 */
	public function isUpdateEnabled()
	{
		return false; // TODO - check if update supported
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::isReportsEnabled()
	 */
	public function isReportsEnabled()
	{
		return false; // TODO - check if reports supported
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::isScheduleUpdateEnabled()
	 */
	public function isScheduleUpdateEnabled()
	{
		return false; // TODO - check if schedule supported
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
	public function getUpdateRequiredEntryFields()
	{
		if(kConf::hasParam('comcast_update_required_entry_fields'))
			return kConf::get('comcast_update_required_entry_fields');
			
		return array();
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::getUpdateRequiredMetadataXPaths()
	 */
	public function getUpdateRequiredMetadataXPaths()
	{
		if(kConf::hasParam('comcast_update_required_metadata_xpaths'))
			return kConf::get('comcast_update_required_metadata_xpaths');
			
		return array();
	}
	
	/**
	 * TODO - remove the method if delete not supported
	 * @param string $entryId
	 * @param KalturaComcastDistributionJobProviderData $providerData
	 * @return string
	 */
	public static function generateDeleteXML($entryId, KalturaComcastDistributionJobProviderData $providerData)
	{
		$xml = self::generateXML($entryId, $providerData);
		if(!$xml)
		{
			KalturaLog::err("No XML returned for entry [$entryId]");
			return null;
		}
			
		return $xml->saveXML();
	}
	
	/**
	 * TODO - remove the method if update not supported
	 * @param string $entryId
	 * @param KalturaComcastDistributionJobProviderData $providerData
	 * @return string
	 */
	public static function generateUpdateXML($entryId, KalturaComcastDistributionJobProviderData $providerData)
	{
		$xml = self::generateXML($entryId, $providerData);
		if(!$xml)
		{
			KalturaLog::err("No XML returned for entry [$entryId]");
			return null;
		}
	
		return $xml->saveXML();
	}
	
	/**
	 * @param string $entryId
	 * @param KalturaComcastDistributionJobProviderData $providerData
	 * @return string
	 */
	public static function generateSubmitXML($entryId, KalturaComcastDistributionJobProviderData $providerData)
	{
		$xml = self::generateXML($entryId, $providerData);
		if(!$xml)
		{
			KalturaLog::err("No XML returned for entry [$entryId]");
			return null;
		}
		
		return $xml->saveXML();
	}
	
	/**
	 * @param string $entryId
	 * @param KalturaComcastDistributionJobProviderData $providerData
	 * @return DOMDocument
	 */
	protected static function generateXML($entryId, KalturaComcastDistributionJobProviderData $providerData)
	{
		KalturaLog::debug("Generates XML for entry [$entryId]");
		
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
		
		$xslPath = realpath(dirname(__FILE__) . '/../') . '/xml/submit.xsl';
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
			
		// TODO create validation XSD
		$xsdPath = realpath(dirname(__FILE__) . '/../') . '/xml/submit.xsd';
		if(file_exists($xsdPath) && !$xml->schemaValidate($xsdPath))
		{
			KalturaLog::err("Schema validation failed");		
			return null;
		}
		
		return $xml;
	}
}
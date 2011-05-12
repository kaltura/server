<?php
/**
 * @package plugins.myspaceDistribution
 * @subpackage lib
 */
class MyspaceDistributionProvider implements IDistributionProvider
{
	/**
	 * @var MyspaceDistributionProvider
	 */
	protected static $instance;
	
	protected function __construct()
	{
		
	}
	
	/**
	 * @return MyspaceDistributionProvider
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new MyspaceDistributionProvider();
			
		return self::$instance;
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionProvider::getType()
	 */
	public function getType()
	{
		return MyspaceDistributionPlugin::getDistributionProviderTypeCoreValue(MyspaceDistributionProviderType::MYSPACE);
	}
	
	/**
	 * @return string
	 */
	public function getName()
	{
		return 'MYSPACE';
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::isDeleteEnabled()
	 */
	public function isDeleteEnabled()
	{
		return true;
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::isUpdateEnabled()
	 */
	public function isUpdateEnabled()
	{
		return true;
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::isMediaUpdateEnabled()
	 */
	public function isMediaUpdateEnabled()
	{
		return $this->isUpdateEnabled();
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::isReportsEnabled()
	 */
	public function isReportsEnabled()
	{
		return true;
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
		if(kConf::hasParam('myspace_update_required_entry_fields'))
			return kConf::get('myspace_update_required_entry_fields');
			
		return array();
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::getUpdateRequiredMetadataXPaths()
	 */
	public function getUpdateRequiredMetadataXPaths($distributionProfileId = null)
	{
		if(kConf::hasParam('myspace_update_required_metadata_xpaths'))
			return kConf::get('myspace_update_required_metadata_xpaths');
			
		return array();
	}
	
	/**
	 * @param string $entryId
	 * @param KalturaMyspaceDistributionJobProviderData $providerData
	 * @return string
	 */
	public static function generateDeleteXML($entryId, KalturaMyspaceDistributionJobProviderData $providerData)
	{
		$providerData->$deleteOp='1';
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
	 * @param KalturaMyspaceDistributionJobProviderData $providerData
	 * @return string
	 */
	public static function generateUpdateXML($entryId, KalturaMyspaceDistributionJobProviderData $providerData)
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
	 * @param KalturaMyspaceDistributionJobProviderData $providerData
	 * @return string
	 */
	public static function generateSubmitXML($entryId, KalturaMyspaceDistributionJobProviderData $providerData)
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
	 * @param KalturaMyspaceDistributionJobProviderData $providerData
	 * @return DOMDocument
	 */
	public static function generateXML($entryId, KalturaMyspaceDistributionJobProviderData $providerData)
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
		return $xml;
		
	}
	
		/**
	 * @param string $previousFile
	 * @param KalturaMyspaceDistributionJobProviderData $providerData
	 * @return String
	 */
	public static function generateCombinedXML($previousFile, KalturaMyspaceDistributionJobProviderData $providerData)
	{
		$xml = new DOMDocument();
		if(!$xml->loadXML($providerData->xml))
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
			if ($name == "flavourId")
			{
				continue; //local variable in the xsl
			}
			else if ($name == "existingFile")
			{
				$varNode->textContent = $previousFile;
				$varNode->appendChild($xsl->createTextNode($previousFile));
				KalturaLog::debug("Set variable [existingFile] to [{$previousFile}]");
			}
			else if($name && $providerData->$name)
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
		
		return $xml->saveXML();
	
	}

}
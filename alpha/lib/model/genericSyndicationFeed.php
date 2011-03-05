<?php
/**
 * Subclass for representing a row from the 'syndication_feed' table for type generic syndication.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class genericSyndicationFeed extends syndicationFeed implements ISyncableFile
{
	const FILE_SYNC_SYNDICATION_FEED_XSLT = 1;
	
	const ITEMS_PLACEHOLDER = '<ITEMS_PLACEHOLDER>';
	
	private static $xslItem = null;
	private static $xslMrss = null;
	
	
		
	/**
	 * @var FileSync
	 */
	private $m_file_sync;
	
	/* (non-PHPdoc)
	 * @see lib/model/ISyncableFile#getFileSync()
	 */
	public function getFileSync ( )
	{
		return $this->m_file_sync; 
	}
	
	/* (non-PHPdoc)
	 * @see lib/model/ISyncableFile#setFileSync()
	 */
	public function setFileSync ( FileSync $file_sync )
	{
		 $this->m_file_sync = $file_sync;
	}
	
	/**
	 * @param int $sub_type
	 * @throws FileSyncException
	 */
	private static function validateFileSyncSubType($sub_type)
	{
		$valid_sub_types = array(
			self::FILE_SYNC_SYNDICATION_FEED_XSLT,
		);
		
		if(! in_array($sub_type, $valid_sub_types))
			throw new FileSyncException(FileSyncObjectType::SYNDICATION_FEED, $sub_type, $valid_sub_types);
	}
	
	/* (non-PHPdoc)
	 * @see lib/model/ISyncableFile#getSyncKey()
	 */
	public function getSyncKey($sub_type, $version = null)
	{
		self::validateFileSyncSubType($sub_type);
		if(!$version)
			$version = $this->getVersion();
		
		$key = new FileSyncKey();
		$key->object_type = FileSyncObjectType::SYNDICATION_FEED;
		$key->object_sub_type = $sub_type;
		$key->object_id = $this->getId();
		$key->version = $version;
		$key->partner_id = $this->getPartnerId();
		
		return $key;
	}
	
	public function incrementVersion()
	{
		$this->setVersion($this->getVersion() + 1);
	}
	
	/* (non-PHPdoc)
	 * @see lib/model/ISyncableFile#generateFileName()
	 */
	public function generateFileName($sub_type, $version = null)
	{
		self::validateFileSyncSubType($sub_type);
		
		if(!$version)
			$version = $this->getVersion();
			
		return $this->getId(). "_" . "version_$version.xml";
	}
	
	/* (non-PHPdoc)
	 * @see metadata/lib/model/om/BaseMetadata#preInsert()
	 */
	public function preInsert(PropelPDO $con = null)
	{
		$this->setVersion(1);
		return parent::preInsert($con);
	}
	
	public function getVersion()
	{
		$this->getFromCustomData("version",null,0);
	}
	
	public function setVersion($value)
	{
		$this->putInCustomData("version",$value);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/model/ISyncableFile#generateFilePathArr()
	 */
	public function generateFilePathArr($sub_type, $version = null)
	{
		self::validateFileSyncSubType ( $sub_type );
		
		if(!$version)
			$version = $this->getVersion();
		
		$dir = (intval($this->getId() / 1000000)) . '/' . (intval($this->getId() / 1000) % 1000);
		$path =  "/content/syndication/data/$dir/" . $this->generateFileName($sub_type, $version);

		return array(myContentStorage::getFSContentRootPath(), $path); 
	}
	
	
		/*
	 * @params string $xslt
	 * @return string
	 */
	public static function getKalturaItemXslt($xslt)
	{
		if(self::$xslItem)
			return self::$xslItem;
			
		self::$xslItem = self::createKalturaItemXslt($xslt);
		return self::$xslItem;
	}
	
	
	/*
	 * @params string $xslt
	 * @return string
	 */
	public static function getKalturaMrssXslt($xslt)
	{
		if(self::$xslMrss)
			return self::$xslMrss;
			
		self::$xslMrss = self::createKalturaMrssXslt($xslt);
		return self::$xslMrss;
	}
	
		private static function getKalturaMrssXml($title, $link = null, $description = null)
	{
		$mrss = kMrssManager::getMrssXml($title, $link, $description);
		
		foreach ($mrss->children() as $second_gen) {
			if ($second_gen->getName() == 'channel')
				$second_gen->addChild('items',ITEMS_PLACEHOLDER);
		}
		
		return $mrss->asXML();
	}
	
	/**
	 * 
	 * @param entry $entry
	 * @param KalturaBaseSyndicationFeed $syndicationFeed
	 */
	private static function getKalturaEntryMrssXml(entry $entry, KalturaBaseSyndicationFeed  $syndicationFeed)
	{
		$mrss = kMrssManager::getEntryMrssXml($entry);
		
		if(!$mrss)
		{
			KalturaLog::err("No MRSS returned for entry [".$entry->getId()."]");
			return null;
		}
		
		if ((!is_null($syndicationFeed))  && !is_null($syndicationFeed->playerUiconfId))
		{
			$host = myPartnerUtils::getCdnHost($entry->getPartnerId());
			
			$playerUrl = 'http://'.$host.
							'/kwidget/wid/_'.$entry->getPartnerId().
							'/entry_id/'.$entry->getId().$syndicationFeed->playerUiconfId;
			$mrss->addChild('player',$playerUrl);
		}
		
		return $mrss->asXML();
	}
	
	/**
	 * @param string $title
	 * @param string $link
	 * @param string $description
	 * @return string
	 */
	public static function getMrssHeader($title, $link = null, $description = null, $xslt = null)
	{
		$mrss = self::getKalturaMrssXml($title, $link, $description);
		
		if (!is_null($xslt))
		{
			$kalturaXslt = self::getKalturaMrssXslt($xslt);
			$mrss = self::transformXmlUsingXslt($mrss, $kalturaXslt);
		}
		
		$divideHeaderFromFooter = strpos($mrss,ITEMS_PLACEHOLDER);		
		$mrss = substr($mrss,0,$divideHeaderFromFooter);
		
		return $mrss;
	}
	
	/**
	 * @param string $title
	 * @param string $link
	 * @param string $description
	 * @return string
	 */
	public static function getMrssFooter($title, $link = null, $description = null, $xslt = null)
	{
		$mrss = self::getKalturaMrssXml($title, $link, $description);
		
		if (!is_null($xslt))
		{
			$kalturaXslt = self::getKalturaMrssXslt($xslt);
			$mrss = self::transformXmlUsingXslt($mrss, $kalturaXslt);
		}
		
		$divideHeaderFromFooter = strpos($mrss,ITEMS_PLACEHOLDER) + strlen(ITEMS_PLACEHOLDER);
		
		
		$mrss = substr($mrss,$divideHeaderFromFooter);	
		return $mrss;
	}
	
	/**
	 * @param string $mrss
	 * @return string
	 */
	private static function removeXmlHeader($mrss)
	{
		$position = strpos($mrss,'<?xml version="1.0"?>');
		if($position !== false){
			$divideHeaderFromFooter = $position + strlen('<?xml version="1.0"?>') + 1;
			$mrss = substr($mrss,$divideHeaderFromFooter);
		}
		
		$position = strpos($mrss,'<?xml version="1.0" encoding="UTF-8"?>');
		if($position !== false){
			$divideHeaderFromFooter = $position + strlen('<?xml version="1.0" encoding="UTF-8"?>') + 1;
			$mrss = substr($mrss,$divideHeaderFromFooter);
		}
		
		return $mrss;		
	}
	
		/**
	 * @param entry $entry
	 * @param KalturaBaseSyndicationFeed $syndicationFeed
	 * @param string $xslt
	 * @return string
	 */
	public static function getKalturaEntryMrss(entry $entry, KalturaBaseSyndicationFeed $syndicationFeed)
	{
		
		$entryMrss =  self::getKalturaEntryMrssXml($entry, $syndicationFeed);
		
		if(!$entryMrss)
		{
			KalturaLog::err("No MRSS returned for entry [".$entry->getId()."]");
			return null;
		}
		
		
		if ($syndicationFeed instanceof KalturaXsltSyndicationFeed)
		{
			$itemXslt = self::getKalturaItemXslt($syndicationFeed->xslt);
			$entryMrss = self::transformXmlUsingXslt($entryMrss, $itemXslt);
		}
		$entryMrss = self::removeXmlHeader($entryMrss);
		
		return $entryMrss;
	}
	
	
	/**
	 * return xlts with item template only when given xslt compatible with kaltura feed
	 * @param string $xslt
	 * @return string $xslt
	 */
	private function createKalturaItemXslt($xslt)
	{
		$xsl = new DOMDocument();
		if(!$xsl->loadXML($xslt))
		{
			KalturaLog::debug("Could not load xslt");
			return null;
		}
		
		$xpath = new DOMXpath($xsl);
		$xslStylesheet = $xpath->query("//xsl:stylesheet");		
		$rss = $xpath->query("//xsl:template[@name='rss']");		
		$xslStylesheet->item(0)->removeChild($rss->item(0));	
	
		return $xsl->saveXML();
	}
	
	/**
	 * return xlts with item place holder only when given xslt compatible with kaltura feed
	 * @param string $xslt
	 * @return string $xslt
	 */
	private function createKalturaMrssXslt($xslt)
	{
		$xsl = new DOMDocument();
		if(!$xsl->loadXML($xslt))
		{
			KalturaLog::debug("Could not load xslt");
			return null;
		}
		
		$xpath = new DOMXpath($xsl);
		
		//remove items template
		$xslStylesheet = $xpath->query("//xsl:stylesheet");
		$item = $xpath->query("//xsl:template[@name='item']");
		$item->item(0)->parentNode->removeChild($item->item(0));
		
		//add place holder for items
		$items = $xpath->query("//xsl:apply-templates[@name='item']"); 
		$itemPlaceHolderNode = $xsl->createTextNode(ITEMS_PLACEHOLDER);
		$items->item(0)->parentNode->replaceChild($itemPlaceHolderNode,$items->item(0));
	
		return $xsl->saveXML();
	}
	
	/**
	 * 
	 * @param string $xml
	 * @param string $xslt
	 * @return string  
	 */
	private static function transformXmlUsingXslt($xmlStr, $xslt)
	{
					
		$xml = new DOMDocument();
		if(!$xml->loadXML($xmlStr))
		{
			KalturaLog::debug("Could not load xmlStr");
			return null;
		}
		
		$xsl = new DOMDocument();
		if(!$xsl->loadXML($xslt))
		{
			KalturaLog::debug("Could not load xslt");
			return null;
		}

		$proc = new XSLTProcessor;
		$proc->registerPHPFunctions();
		$proc->importStyleSheet($xsl);
		
		$xml = $proc->transformToDoc($xml);
		$xml->documentElement->removeAttributeNS('http://php.net/xsl', 'php');
		
		if(!$xml)
		{
			KalturaLog::err("XML Transformation failed");
			return null;
		}
				
		return $xml->saveXML();
	}	
	
	
	
}
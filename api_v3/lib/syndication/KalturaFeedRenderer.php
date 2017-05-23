<?php

class KalturaFeedRenderer extends SyndicationFeedRenderer{
	
	const ITEMS_PLACEHOLDER = 'ITEMS_PLACEHOLDER';
	
	protected $kalturaXslt = null;
	protected $kalturaXsltItem = null;

	private $state = null;

	public function init($syndicationFeed, $syndicationFeedDB, $mimeType) {
		parent::init($syndicationFeed, $syndicationFeedDB, $mimeType);

		$xslt = null;
		if (isset($syndicationFeed->xslt))
			$xslt = $syndicationFeed->xslt;
		if (in_array($syndicationFeedDB->getType(), array(syndicationFeedType::KALTURA_XSLT, syndicationFeedType::ROKU_DIRECT_PUBLISHER, syndicationFeedType::OPERA_TV_SNAP)) && !is_null($xslt)) {
			$this->kalturaXslt = $this->createKalturaMrssXslt($xslt);
			$this->kalturaXsltItem = $this->createKalturaItemXslt($xslt);
		}
	}
	
	public function shouldEnableCache() {
		$mrssParams = $this->syndicationFeedDB->getMrssParameters();
		if ($mrssParams && $mrssParams->getItemXpathsToExtend()) 
			return false;
		
		return true;
	}

	public function handleHeader() {
		
		$mrss = $this->getKalturaMrssXml($this->syndicationFeed->name, $this->syndicationFeed->feedLandingPage, $this->syndicationFeed->feedDescription);
		if($this->kalturaXslt)
			$mrss = kXml::transformXmlUsingXslt($mrss, $this->kalturaXslt);
		
		$divideHeaderFromFooter = strpos($mrss, self::ITEMS_PLACEHOLDER);
		$mrss = substr($mrss,0,$divideHeaderFromFooter);
		
		$addXmlHeader =  $this->syndicationFeedDB->getAddXmlHeader();
			
		if (is_null($addXmlHeader) || $addXmlHeader == false)
			$mrss = $this->removeXmlHeader($mrss);
		
		return $mrss;
	}

	public function handleBody($entry, $e = null, $flavorAssetUrl = null) {
		$entryMrss =  $this->getMrssEntryXml($entry, $this->syndicationFeedDB,  $this->syndicationFeed->landingPage);
		
		if(!$entryMrss) {
			KalturaLog::err("No MRSS returned for entry [".$entry->getId()."]");
			return null;
		}
		
		return $entryMrss;
	}
	
	public function finalize($entryMrss, $moreItems) {
		if ($this->kalturaXsltItem)
		{
			//syndication parameters to pass to XSLT
			$xslParams = array();
			$xslParams[XsltParameterName::KALTURA_HAS_NEXT_ITEM] = $moreItems;
			$xslParams[XsltParameterName::KALTURA_SYNDICATION_FEED_FLAVOR_PARAM_ID] = $this->syndicationFeedDB->getFlavorParamId();
			$xslParams[XsltParameterName::KALTURA_SYNDICATION_FEED_PARTNER_ID] = $this->syndicationFeed->partnerId;

			$entryMrss = kXml::transformXmlUsingXslt($entryMrss, $this->kalturaXsltItem, $xslParams);
			$entryMrss = $this->removeNamespaces($entryMrss);
		}
		$entryMrss = $this->removeXmlHeader($entryMrss);

		return $entryMrss;
	}
	
	public function handleFooter() {
		$mrss = $this->getKalturaMrssXml($this->syndicationFeed->name, $this->syndicationFeed->feedLandingPage, $this->syndicationFeed->feedDescription, true);
	
		if($this->kalturaXslt)
			$mrss = kXml::transformXmlUsingXslt($mrss, $this->kalturaXslt);
	
		$divideHeaderFromFooter = strpos($mrss, self::ITEMS_PLACEHOLDER) + strlen(self::ITEMS_PLACEHOLDER);
		$mrss = substr($mrss,$divideHeaderFromFooter);
	
		return $mrss;
	}

	private function getKalturaMrssXml($title, $link = null, $description = null, $addNextLink = false)
	{
		$mrss = kMrssManager::getMrssXml($title, $link, $description);
	
		foreach ($mrss->children() as $second_gen) {
			if ($second_gen->getName() == 'channel')
				$second_gen->addChild('items', self::ITEMS_PLACEHOLDER);
		}

		if($this->state && $addNextLink)
		{
			$id = $this->syndicationFeed->id;
			$partnerId = $this->syndicationFeed->partnerId;

			$host = kConf::get('www_host');
			if(is_null(parse_url($host ,PHP_URL_SCHEME)))
			{
				$host = infraRequestUtils::getProtocol() . "://" . $host;
			}

			$hrefLink = $host . "/api_v3/getFeed.php?partnerId=$partnerId&feedId=$id";
			if(kCurrentContext::$ks)
				$hrefLink .= "&ks=" . kCurrentContext::$ks;
			$hrefLink .= "&state=" . $this->state;

			$nextLink = $mrss->addChild('link');
			$nextLink->addAttribute("rel", "next");
			$nextLink->addAttribute("href", $hrefLink);
		}
	
		return $mrss->asXML();
	}
	
	/**
	 * return xlts with item place holder only when given xslt compatible with kaltura feed
	 * @param string $xslt
	 * @return string $xslt
	 */
	private function createKalturaMrssXslt($xslt)
	{
		$xsl = new DOMDocument();
		if(!@$xsl->loadXML($xslt))
		{
			KalturaLog::err("Could not load xslt");
			return null;
		}
	
		$xpath = new DOMXPath($xsl);
	
		//remove items template
		$xslStylesheet = $xpath->query("//xsl:stylesheet");
		$item = $xpath->query("//xsl:template[@name='item']");
		$item->item(0)->parentNode->removeChild($item->item(0));
	
		//add place holder for items
		$items = $xpath->query("//xsl:apply-templates[@name='item']");
		$itemPlaceHolderNode = $xsl->createTextNode(self::ITEMS_PLACEHOLDER);
		$items->item(0)->parentNode->replaceChild($itemPlaceHolderNode,$items->item(0));
	
		return $xsl->saveXML();
	}
	
	/**
	 * return xlts with item template only when given xslt compatible with kaltura feed
	 * @param string $xslt
	 * @return string $xslt
	 */
	private function createKalturaItemXslt($xslt)
	{
		$xsl = new DOMDocument();
		if(!@$xsl->loadXML($xslt))
		{
			KalturaLog::err("Could not load xslt");
			return null;
		}
	
		$xpath = new DOMXPath($xsl);
		$xslStylesheet = $xpath->query("//xsl:stylesheet");
		$rss = $xpath->query("//xsl:template[@name='rss']");
		$xslStylesheet->item(0)->removeChild($rss->item(0));
	
		return $xsl->saveXML();
	}
	
	/**
	 * @param $entry
	 * @param $syndicationFeed
	 * @return string
	 */
	private function getMrssEntryXml(entry $entry, syndicationFeed $syndicationFeed = null, $link = null)
	{
		if ($syndicationFeed->getMrssParameters())
			$mrssParams = clone $syndicationFeed->getMrssParameters();
		else
			$mrssParams = new kMrssParameters;
		$mrssParams->setLink($link);
		$mrssParams->setFilterByFlavorParams($syndicationFeed->getFlavorParamId());
		$mrssParams->setIncludePlayerTag(true);
		$mrssParams->setPlayerUiconfId($syndicationFeed->getPlayerUiconfId());
		$mrssParams->setStorageId($syndicationFeed->getStorageId());
		$mrssParams->setServePlayManifest($syndicationFeed->getServePlayManifest());
		$mrssParams->setPlayManifestClientTag('feed:' . $syndicationFeed->getId());

		$features = null;
		if ($syndicationFeed->getUseCategoryEntries())
		{
			KalturaLog::info("Getting entry's associated categories from the category_entry table");
			$features = array (ObjectFeatureType::CATEGORY_ENTRIES);
		}
		
		$mrss = kMrssManager::getEntryMrssXml($entry, null, $mrssParams, $features);
	
		if(!$mrss)
		{
			KalturaLog::err("No MRSS returned for entry [".$entry->getId()."]");
			return null;
		}
	
		return $mrss->asXML();
	}
	
	/**
	 * @param string $mrss
	 * @return string
	 */
	private function removeXmlHeader($mrss)
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
	 *
	 * @param stinr $xmlStr
	 * @return string
	 */
	private function removeNamespaces($xmlStr)
	{
		//	return preg_replace("/<.*(xmlns *= *[\"'].[^\"']*[\"']).[^>]*>/i", "", $xmlStr);
		//return preg_replace("/ xmlns:[a-zA-Z0-9_]{1,}=[\"'].[^\"']*[\"']/", "", $xmlStr);
		return preg_replace("/ xmlns:[^= ]{1,}=[\"][^\"]*[\"]/i", "", $xmlStr);
	}

	public function setState($state)
	{
		$this->state = $state;
	}
}


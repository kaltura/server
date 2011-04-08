<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaGenericXsltSyndicationFeed extends KalturaGenericSyndicationFeed
{
	/**
	*
	* @var string
	*/
	public $xslt;
	
	private static $mapBetweenObjects = array
	(
   
	);
	
	public function validateXslt()
	{
		$xsl = new DOMDocument();
		if(!$xsl->loadXML($this->xslt))
		{
			KalturaLog::debug("Could not load xslt");
			throw new KalturaAPIException(KalturaErrors::INVALID_XSLT, $this->xslt);
		}
		
		$xpath = new DOMXpath($xsl);
		
		$xslStylesheet = $xpath->query("//xsl:stylesheet");
		$rss = $xpath->query("//xsl:template[@name='rss']");
		if ($rss->length == 0)
			throw new KalturaAPIException(KalturaErrors::INVALID_XSLT_MISSING_TEMPLATE_RSS, $this->xslt);
		
		$item = $xpath->query("//xsl:template[@name='item']");
		if ($item->length == 0)
			throw new KalturaAPIException(KalturaErrors::INVALID_XSLT_MISSING_TEMPLATE_ITEM, $this->xslt);
		
		$items = $xpath->query("//xsl:apply-templates[@name='item']"); 
		if ($items->length == 0)
			throw new KalturaAPIException(KalturaErrors::INVALID_XSLT_MISSING_APPLY_TEMPLATES_ITEM, $this->xslt);

		return true;
	}
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
        
    function __construct()
	{
		$this->type = KalturaSyndicationFeedType::KALTURA_XSLT;
	}
	
	public function fromObject($source_object)
	{
		parent::fromObject($source_object);

		$key = $source_object->getSyncKey(genericSyndicationFeed::FILE_SYNC_SYNDICATION_FEED_XSLT);
		$this->xslt = kFileSyncUtils::file_get_contents($key, true, false);
	}
	
}
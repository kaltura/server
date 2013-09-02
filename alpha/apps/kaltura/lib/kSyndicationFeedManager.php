<?php
class kSyndicationFeedManager
{
	/*
	 * @param string $xsltStr
	 */
	public static function validateXsl($xsltStr)
	{
		$xsl = new DOMDocument();
		if(!@$xsl->loadXML($xsltStr))
		{
			KalturaLog::err("Invalid XSLT structure");
			throw new kCoreException("Invalid XSLT", kCoreException::INVALID_XSLT);
		}
		
		$xpath = new DOMXPath($xsl);
		
		$xslStylesheet = $xpath->query("//xsl:stylesheet");
		$rss = $xpath->query("//xsl:template[@name='rss']");
		if ($rss->length == 0)
		    throw new kCoreException("Invalid XSLT structure - missing template rss", kCoreException::INVALID_XSLT);
		
		$item = $xpath->query("//xsl:template[@name='item']");
		if ($item->length == 0)
		    throw new kCoreException("Invalid XSLT structure - missing template item", kCoreException::INVALID_XSLT);
		
		$items = $xpath->query("//xsl:apply-templates[@name='item']"); 
		if ($items->length == 0)
		    throw new kCoreException("Invalid XSLT structure - missing template apply-templates item", kCoreException::INVALID_XSLT);

		return true;
	}
}
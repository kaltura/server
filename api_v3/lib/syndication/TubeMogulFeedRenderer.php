<?php

class TubeMogulFeedRenderer extends SyndicationFeedRenderer{

	public function handleHeader() {
		header ("content-type: text/xml; charset=utf-8");
		$this->writeOpenXmlNode('rss', 0, array('version'=>"2.0", 'xmlns:media'=>"http://search.yahoo.com/mrss/", 'xmlns:tm'=>"http://www.tubemogul.com/mrss"));
		$this->writeOpenXmlNode('channel',1);
	}

	public function handleBody($entry, $e = null, $flavorAssetUrl = null) {
		$entryDescription = $this->stringToSafeXml($e->description);
		if(!$entryDescription) 
			$entryDescription = $this->stringToSafeXml($e->name);
		$entryTags = $this->stringToSafeXml($e->tags);
		if(!$entryTags) 
			$entryTags = $this->stringToSafeXml(str_replace(' ', ', ', $e->name));
		
		$this->writeOpenXmlNode('item',2);
		$this->writeFullXmlNode('pubDate',date('Y-m-d',$e->createdAt).'T'.date('H:i:sO',$e->createdAt),3);
		$this->writeFullXmlNode('media:title', $this->stringToSafeXml($e->name), 3);
		$this->writeFullXmlNode('media:description', $entryDescription,3);
		$this->writeFullXmlNode('media:keywords', $entryTags, 3);
		
		$categories = explode(',', $this->syndicationFeed->categories);
		foreach($categories as $category)
		{
			$categoryId = KalturaTubeMogulSyndicationFeed::getCategoryId($category);
			$this->writeFullXmlNode('media:category', $categoryId, 3, array( 'scheme'=>"http://www.tubemogul.com"));
			break;
		}
		
		$this->writeFullXmlNode('media:content', '', 3, array('url'=> $flavorAssetUrl));
		$this->writeClosingXmlNode('item',1);
		
		return null;
	}

	public function handleFooter() {
		$this->writeClosingXmlNode('channel',1);
		$this->writeClosingXmlNode('rss');
	}

	public function finalize($entryMrss, $moreItems) {
		return;		
	}
}

?>
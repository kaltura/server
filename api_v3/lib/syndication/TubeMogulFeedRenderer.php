<?php

class TubeMogulFeedRenderer extends SyndicationFeedRenderer{

	public function handleHeader() {
		$res = $this->writeOpenXmlNode('rss', 0, array('version'=>"2.0", 'xmlns:media'=>"http://search.yahoo.com/mrss/", 'xmlns:tm'=>"http://www.tubemogul.com/mrss"));
		$res .= $this->writeOpenXmlNode('channel',1);
		return $res;
	}

	public function handleBody($entry, $e = null, $flavorAssetUrl = null) {
		$entryDescription = $this->stringToSafeXml($e->description);
		if(!$entryDescription) 
			$entryDescription = $this->stringToSafeXml($e->name);
		$entryTags = $this->stringToSafeXml($e->tags);
		if(!$entryTags) 
			$entryTags = $this->stringToSafeXml(str_replace(' ', ', ', $e->name));
		
		$res = '';
		$res .= $this->writeOpenXmlNode('item',2);
		$res .= $this->writeFullXmlNode('pubDate',date('Y-m-d',$e->createdAt).'T'.date('H:i:sO',$e->createdAt),3);
		$res .= $this->writeFullXmlNode('media:title', $this->stringToSafeXml($e->name), 3);
		$res .= $this->writeFullXmlNode('media:description', $entryDescription,3);
		$res .= $this->writeFullXmlNode('media:keywords', $entryTags, 3);
		
		$categories = explode(',', $this->syndicationFeed->categories);
		foreach($categories as $category)
		{
			$categoryId = KalturaTubeMogulSyndicationFeed::getCategoryId($category);
			$res .= $this->writeFullXmlNode('media:category', $categoryId, 3, array( 'scheme'=>"http://www.tubemogul.com"));
			break;
		}
		
		$res .= $this->writeFullXmlNode('media:content', '', 3, array('url'=> $flavorAssetUrl));
		$res .= $this->writeClosingXmlNode('item',1);
		
		return $res;
	}

	public function handleFooter() {
		$res = $this->writeClosingXmlNode('channel',1);
		$res .= $this->writeClosingXmlNode('rss');
		return $res;
	}

}

?>
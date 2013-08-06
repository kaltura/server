<?php

class YahooFeedRenderer extends SyndicationFeedRenderer {

	public function handleHeader() {
		header ("content-type: text/xml; charset=utf-8");
		$this->writeOpenXmlNode('rss', 0, array('version' => "2.0",  'xmlns:media' => "http://search.yahoo.com/mrss/", 'xmlns:dcterms' => "http://purl.org/dc/terms/"));
		$this->writeOpenXmlNode('channel',1);
		$this->writeFullXmlNode('title', $this->stringToSafeXml($this->syndicationFeed->name), 2);
		$this->writeFullXmlNode('link', $this->syndicationFeed->feedLandingPage, 2);
		$this->writeFullXmlNode('description', $this->stringToSafeXml($this->syndicationFeed->feedDescription), 2);
		
	}

	public function handleBody($entry, $e = null, $flavorAssetUrl = null) {
		$this->writeOpenXmlNode('item',2); // open ITEM
		$this->writeFullXmlNode('title', $this->stringToSafeXml($e->name), 3);
		$this->writeFullXmlNode('link', $this->syndicationFeed->landingPage.$e->id, 3);
		$this->writeOpenXmlNode('media:content', 3, array( 'url' => $flavorAssetUrl));
		$this->writeFullXmlNode('media:title', $this->stringToSafeXml($e->name), 4);
		$this->writeFullXmlNode('media:description', $this->stringToSafeXml($e->description), 4);
		$this->writeFullXmlNode('media:keywords', $this->stringToSafeXml($e->tags), 4);
		$this->writeFullXmlNode('media:thumbnail', '', 4, array('url'=>$e->thumbnailUrl));
		$categories = explode(',', $this->syndicationFeed->categories);
		foreach($categories as $category)
		{
			if(!$category) continue;
			$this->writeFullXmlNode('media:category',$category,4, array('scheme'=>"http://search.yahoo.com/mrss/category_schema"));
		}
		if($this->syndicationFeed->allowEmbed)
		{
			$this->writeFullXmlNode('media:player', null, 4, array('url'=>$this->getPlayerUrl($e->id)));
		}
		// TODO - add restirction on entry ???? media:restriction
		// TODO - consider adding media:community
		$this->writeFullXmlNode('media:rating',$this->syndicationFeed->adultContent, 4, array( 'scheme' => "urn:simple"));
		$this->writeClosingXmlNode('media:content',3);
		$this->writeClosingXmlNode('item',2); // close ITEM
		
		return null;
	}

	public function handleFooter() {
		$this->writeClosingXmlNode('channel',1); // close CHANNEL
		$this->writeClosingXmlNode('rss'); // close RSS
	}

	public function finalize($entryMrss, $moreItems) {
		return;		
	}
}

?>
<?php

class YahooFeedRenderer extends SyndicationFeedRenderer {

	public function handleHeader() {
		$res = '';
		$res .= $this->writeOpenXmlNode('rss', 0, array('version' => "2.0",  'xmlns:media' => "http://search.yahoo.com/mrss/", 'xmlns:dcterms' => "http://purl.org/dc/terms/"));
		$res .= $this->writeOpenXmlNode('channel',1);
		$res .= $this->writeFullXmlNode('title', $this->stringToSafeXml($this->syndicationFeed->name), 2);
		$res .= $this->writeFullXmlNode('link', $this->syndicationFeed->feedLandingPage, 2);
		$res .= $this->writeFullXmlNode('description', $this->stringToSafeXml($this->syndicationFeed->feedDescription), 2);
		return $res;
	}

	public function handleBody($entry, $e = null, $flavorAssetUrl = null) {
		$res = '';
		$res .= $this->writeOpenXmlNode('item',2); // open ITEM
		$res .= $this->writeFullXmlNode('title', $this->stringToSafeXml($e->name), 3);
		$res .= $this->writeFullXmlNode('link', $this->syndicationFeed->landingPage.$e->id, 3);
		$res .= $this->writeOpenXmlNode('media:content', 3, array( 'url' => $flavorAssetUrl));
		$res .= $this->writeFullXmlNode('media:title', $this->stringToSafeXml($e->name), 4);
		$res .= $this->writeFullXmlNode('media:description', $this->stringToSafeXml($e->description), 4);
		$res .= $this->writeFullXmlNode('media:keywords', $this->stringToSafeXml($e->tags), 4);
		$res .= $this->writeFullXmlNode('media:thumbnail', '', 4, array('url'=>$e->thumbnailUrl));
		$categories = explode(',', $this->syndicationFeed->categories);
		foreach($categories as $category)
		{
			if(!$category) continue;
			$res .= $this->writeFullXmlNode('media:category',$category,4, array('scheme'=>"http://search.yahoo.com/mrss/category_schema"));
		}
		if($this->syndicationFeed->allowEmbed)
		{
			$res .= $this->writeFullXmlNode('media:player', null, 4, array('url'=>$this->getPlayerUrl($e->id)));
		}
		// TODO - add restirction on entry ???? media:restriction
		// TODO - consider adding media:community
		$res .= $this->writeFullXmlNode('media:rating',$this->syndicationFeed->adultContent, 4, array( 'scheme' => "urn:simple"));
		$res .= $this->writeClosingXmlNode('media:content',3);
		$res .= $this->writeClosingXmlNode('item',2); // close ITEM
		
		return $res;
	}

	public function handleFooter() {
		$res = $this->writeClosingXmlNode('channel',1); // close CHANNEL
		$res .= $this->writeClosingXmlNode('rss'); // close RSS
		return $res;
	}
}

?>
<?php

class GoogleVideoFeedRenderer extends SyndicationFeedRenderer{
	
	public function handleHeader() {
		header ("content-type: text/xml; charset=utf-8");
			
		$this->writeOpenXmlNode('urlset', 0, array( 'xmlns' => "http://www.sitemaps.org/schemas/sitemap/0.9", 
			'xmlns:video' => "http://www.google.com/schemas/sitemap-video/1.1" ));
	}

	public function handleBody($entry, $e = null, $flavorAssetUrl = null) {
		$this->writeOpenXmlNode('url',1);
		$this->writeFullXmlNode('loc', $this->syndicationFeed->landingPage.$e->id, 2);
		$this->writeOpenXmlNode('video:video', 2);
		$this->writeFullXmlNode('video:content_loc', $flavorAssetUrl, 3);
		if($this->syndicationFeed->allowEmbed)
		{
			$this->writeFullXmlNode('video:player_loc', $this->getPlayerUrl($e->id), 3, array('allow_embed' => 'yes'));
		}
		$this->writeFullXmlNode('video:thumbnail_loc', $e->thumbnailUrl . '/width/480', 3);
		$this->writeFullXmlNode('video:title', $this->stringToSafeXml($e->name), 3);
		$this->writeFullXmlNode('video:description', $this->stringToSafeXml($e->description), 3);
		$this->writeFullXmlNode('video:view_count', $e->views, 3);
		$this->writeFullXmlNode('video:publication_date', date('Y-m-d',$e->createdAt).'T'.date('H:i:sP',$e->createdAt), 3);
		$tags = explode(',', $e->tags);
		foreach($tags as $tag)
		{
			if(!$tag) continue;
			$this->writeFullXmlNode('video:tag', rtrim(ltrim($this->stringToSafeXml($tag))), 3);
		}
		$this->writeFullXmlNode('video:category', $this->stringToSafeXml($e->categories), 3);
		if($this->syndicationFeed->adultContent == KalturaGoogleSyndicationFeedAdultValues::NO)
		{
			$familyFriendly = KalturaGoogleSyndicationFeedAdultValues::YES;
		}
		else
		{
			$familyFriendly = KalturaGoogleSyndicationFeedAdultValues::NO;
		}
		$this->writeFullXmlNode('video:family_friendly', $familyFriendly, 3);
		$this->writeFullXmlNode('video:duration', $e->duration, 3);
		$this->writeClosingXmlNode('video:video', 2);
		$this->writeClosingXmlNode('url', 1);
		
		return null;
	}

	public function handleFooter() {
		$this->writeClosingXmlNode('urlset');
	}

	public function finalize($entryMrss, $moreItems) {
		return;	
	}
}

?>
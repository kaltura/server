<?php

class GoogleVideoFeedRenderer extends SyndicationFeedRenderer{
	
	public function handleHeader() {
		return $this->writeOpenXmlNode('urlset', 0, array( 'xmlns' => "http://www.sitemaps.org/schemas/sitemap/0.9", 
			'xmlns:video' => "http://www.google.com/schemas/sitemap-video/1.1" ));
	}

	public function handleBody($entry, $e = null, $flavorAssetUrl = null) {
		$res = '';
		$res .= $this->writeOpenXmlNode('url',1);
		$res .= $this->writeFullXmlNode('loc', $this->syndicationFeed->landingPage.$e->id, 2);
		$res .= $this->writeOpenXmlNode('video:video', 2);
		$res .= $this->writeFullXmlNode('video:content_loc', $flavorAssetUrl, 3);
		if($this->syndicationFeed->allowEmbed)
		{
			$res .= $this->writeFullXmlNode('video:player_loc', $this->getPlayerUrl($e->id), 3, array('allow_embed' => 'yes'));
		}
		$res .= $this->writeFullXmlNode('video:thumbnail_loc', $e->thumbnailUrl . '/width/480', 3);
		$res .= $this->writeFullXmlNode('video:title', $this->stringToSafeXml($e->name), 3);
		$res .= $this->writeFullXmlNode('video:description', $this->stringToSafeXml($e->description), 3);
		$res .= $this->writeFullXmlNode('video:view_count', $e->views, 3);
		$res .= $this->writeFullXmlNode('video:publication_date', date('Y-m-d',$e->createdAt).'T'.date('H:i:sP',$e->createdAt), 3);
		$tags = explode(',', $e->tags);
		foreach($tags as $tag)
		{
			if(!$tag) continue;
			$res .= $this->writeFullXmlNode('video:tag', rtrim(ltrim($this->stringToSafeXml($tag))), 3);
		}
		$res .= $this->writeFullXmlNode('video:category', $this->stringToSafeXml($e->categories), 3);
		if($this->syndicationFeed->adultContent == KalturaGoogleSyndicationFeedAdultValues::NO)
		{
			$familyFriendly = KalturaGoogleSyndicationFeedAdultValues::YES;
		}
		else
		{
			$familyFriendly = KalturaGoogleSyndicationFeedAdultValues::NO;
		}
		$res .= $this->writeFullXmlNode('video:family_friendly', $familyFriendly, 3);
		$res .= $this->writeFullXmlNode('video:duration', $e->duration, 3);
		$res .= $this->writeClosingXmlNode('video:video', 2);
		$res .= $this->writeClosingXmlNode('url', 1);
		
		return $res;
	}

	public function handleFooter() {
		return $this->writeClosingXmlNode('urlset');
	}

}

?>
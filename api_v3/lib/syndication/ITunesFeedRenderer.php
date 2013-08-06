<?php

class ITunesFeedRenderer extends SyndicationFeedRenderer {

	public function handleHeader() {
		if(is_null($this->mimeType))
		{
			$flavor = assetParamsPeer::retrieveByPK($this->syndicationFeed->flavorParamId);
			if(!$flavor)
				throw new Exception("flavor not found for id " . $this->syndicationFeed->flavorParamId);
		
			switch($flavor->getFormat())
			{
				case 'mp4':
					$this->mimeType = 'video/mp4';
					break;
				case 'm4v':
					$this->mimeType = 'video/x-m4v';
					break;
				case 'mov':
					$this->mimeType = 'video/quicktime';
					break;
				default:
					$this->mimeType = 'video/mp4';
			}
		}
		
		$partner = PartnerPeer::retrieveByPK($this->syndicationFeed->partnerId);
		header ("content-type: text/xml; charset=utf-8");
		'<?xml version="1.0" encoding="utf-8"?>'.PHP_EOL;
		$this->writeOpenXmlNode('rss', 0, array('xmlns:itunes'=>"http://www.itunes.com/dtds/podcast-1.0.dtd",  'version'=>"2.0"));
		$this->writeOpenXmlNode('channel', 1);
		$this->writeFullXmlNode('title', $this->stringToSafeXml($this->syndicationFeed->name), 2);
		$this->writeFullXmlNode('link', $this->syndicationFeed->feedLandingPage, 2);
		$this->writeFullXmlNode('language', $this->syndicationFeed->language, 2);
		$this->writeFullXmlNode('copyright', $partner->getName(), 2);
		$this->writeFullXmlNode('itunes:subtitle', $this->syndicationFeed->name, 2);
		$this->writeFullXmlNode('itunes:author', $this->syndicationFeed->feedAuthor, 2);
		$this->writeFullXmlNode('itunes:summary', $this->syndicationFeed->feedDescription, 2);
		$this->writeFullXmlNode('description', $this->syndicationFeed->feedDescription, 2);
		$this->writeOpenXmlNode('itunes:owner', 2);
		$this->writeFullXmlNode('itunes:name', $this->syndicationFeed->ownerName, 3);
		$this->writeFullXmlNode('itunes:email', $this->syndicationFeed->ownerEmail, 3);
		$this->writeClosingXmlNode('itunes:owner', 2);

		if($this->syndicationFeed->feedImageUrl)
		{
			$this->writeOpenXmlNode('image', 2);
			$this->writeFullXmlNode('link', $this->syndicationFeed->feedLandingPage,3);
			$this->writeFullXmlNode('url', $this->syndicationFeed->feedLandingPage, 3);
			$this->writeFullXmlNode('title', $this->syndicationFeed->name, 3);
			$this->writeClosingXmlNode('image', 2);
			$this->writeFullXmlNode('itunes:image', '', 2, array( 'href'=> $this->syndicationFeed->feedImageUrl));
		}

		$categories = explode(',', $this->syndicationFeed->categories);
		$catTree = array();
		foreach($categories as $category)
		{
			if(!$category) continue;
			if(strpos($category, '/')) // category & subcategory
			{
				$category_parts = explode('/', $category);
				$catTree[$category_parts[0]][] = $category_parts[1];
			}
			else
			{
				$this->writeFullXmlNode('itunes:category', '', 2, array( 'text'=> $category ));
			}
		}
		
		foreach($catTree as $topCat => $subCats)
		{
			if(!$topCat) continue;
			$this->writeOpenXmlNode('itunes:category', 2, array( 'text' => $topCat ));
			foreach($subCats as $cat)
			{
				if(!$cat) continue;
				$this->writeFullXmlNode('itunes:category', '', 3, array( 'text'=> $cat ));
			}
			$this->writeClosingXmlNode('itunes:category', 2);
		}
	}

	public function handleBody($entry, $e = null, $flavorAssetUrl = null) {
		$this->writeOpenXmlNode('item',2);
		$this->writeFullXmlNode('title', $this->stringToSafeXml($e->name), 3);
		$this->writeFullXmlNode('link', $this->syndicationFeed->landingPage.$e->id, 3);
		$this->writeFullXmlNode('guid', $flavorAssetUrl, 3);
		$this->writeFullXmlNode('pubDate', date('r',$e->createdAt), 3);
		$this->writeFullXmlNode('description', $this->stringToSafeXml($e->description), 3);

		$enclosure_attr = array(
			'url'=> $flavorAssetUrl,
			//'length'=>$entry->getLengthInMsecs(), removed by Noa, 25/08/10: we'll need to place here file size (of flavor asset).
			'type'=> $this->mimeType,
		);
		$this->writeFullXmlNode('enclosure', '', 3, $enclosure_attr);
		
		$kuser = $entry->getkuser();
		if($kuser && $kuser->getScreenName())
			$this->writeFullXmlNode('itunes:author', $this->stringToSafeXml($kuser->getScreenName()), 3);
			
		if($e->description)
		{
			$this->writeFullXmlNode('itunes:subtitle', $this->stringToSafeXml($e->description), 3);
			$this->writeFullXmlNode('itunes:summary', $this->stringToSafeXml($e->description), 3);
		}
		$this->writeFullXmlNode('itunes:duration', $this->secondsToWords($e->duration), 3);
		$this->writeFullXmlNode('itunes:explicit', $this->syndicationFeed->adultContent, 3);
		$this->writeFullXmlNode('itunes:image', '', 3, array( 'href' => $e->thumbnailUrl.'/width/600/height/600/ext.jpg'));
		if($e->tags)
			$this->writeFullXmlNode('itunes:keywords', $this->stringToSafeXml($e->tags), 3);
		$this->writeClosingXmlNode('item',2);
		
		return null;
	}

	public function handleFooter() {
		$this->writeClosingXmlNode('channel', 1);
		$this->writeClosingXmlNode('rss');
	}

	public function finalize($entryMrss, $moreItems) {
		return;		
	}


}

?>
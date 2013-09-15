<?php

class ITunesFeedRenderer extends SyndicationFeedRenderer {
	
  	const ENFORCE_ORDER_PLACE_HOLDER = "ITEM_ORDER_IN_FEED";
  
  	/**
   	 * The index of the entry in the feed created
   	 * @var int
   	 */
  	private $feedItemOrderIndex = 1;
  
  	/**
   	 * True if keep order is needed
   	 * @var bool
   	 */
  	private $enforceOrder = false;
  
  	public function init($syndicationFeed, $syndicationFeedDB, $mimeType) {
    	parent::init($syndicationFeed, $syndicationFeedDB, $mimeType);
    
    	$this->enforceOrder = $syndicationFeed->enforceOrder;
  	}

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
		
		$res = '';
		$res .= $this->writeOpenXmlNode('rss', 0, array('xmlns:itunes'=>"http://www.itunes.com/dtds/podcast-1.0.dtd",  'version'=>"2.0"));
		$res .= $this->writeOpenXmlNode('channel', 1);
		$res .= $this->writeFullXmlNode('title', $this->stringToSafeXml($this->syndicationFeed->name), 2);
		$res .= $this->writeFullXmlNode('link', $this->syndicationFeed->feedLandingPage, 2);
		$res .= $this->writeFullXmlNode('language', $this->syndicationFeed->language, 2);
		$res .= $this->writeFullXmlNode('copyright', $partner->getName(), 2);
		$res .= $this->writeFullXmlNode('itunes:subtitle', $this->syndicationFeed->name, 2);
		$res .= $this->writeFullXmlNode('itunes:author', $this->syndicationFeed->feedAuthor, 2);
		$res .= $this->writeFullXmlNode('itunes:summary', $this->syndicationFeed->feedDescription, 2);
		$res .= $this->writeFullXmlNode('description', $this->syndicationFeed->feedDescription, 2);
		$res .= $this->writeOpenXmlNode('itunes:owner', 2);
		$res .= $this->writeFullXmlNode('itunes:name', $this->syndicationFeed->ownerName, 3);
		$res .= $this->writeFullXmlNode('itunes:email', $this->syndicationFeed->ownerEmail, 3);
		$res .= $this->writeClosingXmlNode('itunes:owner', 2);

		if($this->syndicationFeed->feedImageUrl)
		{
			$res .= $this->writeOpenXmlNode('image', 2);
			$res .= $this->writeFullXmlNode('link', $this->syndicationFeed->feedLandingPage,3);
			$res .= $this->writeFullXmlNode('url', $this->syndicationFeed->feedLandingPage, 3);
			$res .= $this->writeFullXmlNode('title', $this->syndicationFeed->name, 3);
			$res .= $this->writeClosingXmlNode('image', 2);
			$res .= $this->writeFullXmlNode('itunes:image', '', 2, array( 'href'=> $this->syndicationFeed->feedImageUrl));
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
				$res .= $this->writeFullXmlNode('itunes:category', '', 2, array( 'text'=> $category ));
			}
		}
		
		foreach($catTree as $topCat => $subCats)
		{
			if(!$topCat) continue;
			$res .= $this->writeOpenXmlNode('itunes:category', 2, array( 'text' => $topCat ));
			foreach($subCats as $cat)
			{
				if(!$cat) continue;
				$res .= $this->writeFullXmlNode('itunes:category', '', 3, array( 'text'=> $cat ));
			}
			$res .= $this->writeClosingXmlNode('itunes:category', 2);
		}
		
		return $res;
	}

	public function handleBody($entry, $e = null, $flavorAssetUrl = null) {
		
		$res = '';
		$res .= $this->writeOpenXmlNode('item',2);
		$res .= $this->writeFullXmlNode('title', $this->stringToSafeXml($e->name), 3);
		$res .= $this->writeFullXmlNode('link', $this->syndicationFeed->landingPage.$e->id, 3);
		$res .= $this->writeFullXmlNode('guid', $flavorAssetUrl, 3);
		$res .= $this->writeFullXmlNode('pubDate', date('r',$e->createdAt), 3);
		$res .= $this->writeFullXmlNode('description', $this->stringToSafeXml($e->description), 3);

		$enclosure_attr = array(
			'url'=> $flavorAssetUrl,
			//'length'=>$entry->getLengthInMsecs(), removed by Noa, 25/08/10: we'll need to place here file size (of flavor asset).
			'type'=> $this->mimeType,
		);
		$res .= $this->writeFullXmlNode('enclosure', '', 3, $enclosure_attr);
		
		$kuser = $entry->getkuser();
		if($kuser && $kuser->getScreenName())
			$res .= $this->writeFullXmlNode('itunes:author', $this->stringToSafeXml($kuser->getScreenName()), 3);
			
		if($this->enforceOrder)
			$res .= $this->writeFullXmlNode('itunes:order', self::ENFORCE_ORDER_PLACE_HOLDER, 3);
			
		if($e->description)
		{
			$res .= $this->writeFullXmlNode('itunes:subtitle', $this->stringToSafeXml($e->description), 3);
			$res .= $this->writeFullXmlNode('itunes:summary', $this->stringToSafeXml($e->description), 3);
		}
		$res .= $this->writeFullXmlNode('itunes:duration', $this->secondsToWords($e->duration), 3);
		$res .= $this->writeFullXmlNode('itunes:explicit', $this->syndicationFeed->adultContent, 3);
		$res .= $this->writeFullXmlNode('itunes:image', '', 3, array( 'href' => $e->thumbnailUrl.'/width/600/height/600/ext.jpg'));
		if($e->tags)
			$res .= $this->writeFullXmlNode('itunes:keywords', $this->stringToSafeXml($e->tags), 3);
		$res .= $this->writeClosingXmlNode('item',2);
		
		return $res;
	}

	public function handleFooter() {
		$res = '';
		$res .= $this->writeClosingXmlNode('channel', 1);
		$res .= $this->writeClosingXmlNode('rss');
		return $res;
	}
	
	public function finalize($entryMrss, $moreItems) {
		if($this->enforceOrder)
		{
			$entryMrss = str_replace(self::ENFORCE_ORDER_PLACE_HOLDER, $this->feedItemOrderIndex, $entryMrss);
			$this->feedItemOrderIndex++;
		} 
  
		return $entryMrss;
	}

}


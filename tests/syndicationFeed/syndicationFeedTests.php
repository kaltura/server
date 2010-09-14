<?php
//require_once("tests/bootstrapTests.php");
require_once(dirname(__FILE__)."/../bootstrapTests.php");

class SyndicationFeedTests extends PHPUnit_Framework_TestCase 
{
	private $createdFeeds;
	private $unique_test_name;
	private $adminKs;
	
	public function setUp() 
	{
		$this->createdFeeds = array();
		$this->unique_test_name = 'SyndicationFeedTests:' . time();
		$this->adminKs = KalturaTestsHelpers::getAdminKs(KalturaTestsHelpers::getPartner()->getId());
	}
	
	public function tearDown() 
	{
		parent::tearDown();
		
		foreach($this->createdFeeds as $feedId)
		{
			$feed = syndicationFeedPeer::retrieveByPKNoFilter($feedId);
				
			if ($feed)
				$feed->delete();
		}
		$this->createdFeeds = array();
	}
	
	public function testAdd()
	{
	    $syndicationFeed = $this->prepareSyndicationFeed();
	 
	    $syndicationFeedService = KalturaTestsHelpers::getServiceInitializedForAction("syndicationFeed", "add", null, null, $this->adminKs);   
		$newSyndicationFeed = $syndicationFeedService->addAction(clone $syndicationFeed);
		$this->createdFeeds[] = $newSyndicationFeed->id;
		
		$this->assertType(get_class($syndicationFeed), $newSyndicationFeed);
		$this->assertSyndicationFeed($syndicationFeed, $newSyndicationFeed);
		
		return $newSyndicationFeed;
	}
	
	public function testGet()
	{
		$syndicationFeed = $this->testAdd();
		
	    $syndicationFeedService = KalturaTestsHelpers::getServiceInitializedForAction("syndicationFeed", "get", null, null, $this->adminKs);   
		$getSyndicationFeed = $syndicationFeedService->getAction($syndicationFeed->id);
		
		$this->assertNotNull($getSyndicationFeed);
		$this->assertSyndicationFeed($syndicationFeed, $getSyndicationFeed);
	}
	
	public function testUpdate()
	{
		$newSyndicationFeed = $this->testAdd();
		
	    $syndicationFeedService = KalturaTestsHelpers::getServiceInitializedForAction("syndicationFeed", "update", null, null, $this->adminKs);
	    
		$syndicationFeed = $this->prepareSyndicationFeed($newSyndicationFeed);
		
		$updatedSyndicationFeed = $syndicationFeedService->updateAction($newSyndicationFeed->id, clone $syndicationFeed);
		
		$this->assertEquals($updatedSyndicationFeed->id, $newSyndicationFeed->id);
		$this->assertEquals($updatedSyndicationFeed->partnerId, $newSyndicationFeed->partnerId);
		$this->assertEquals($updatedSyndicationFeed->name, $syndicationFeed->name);
		
		$syndicationFeed->partnerId = $newSyndicationFeed->partnerId;
		
		$this->assertSyndicationFeed($updatedSyndicationFeed, $syndicationFeed);
	}
	
	public function testDelete()
	{
		$newSyndicationFeed = $this->testAdd();
		
	    $syndicationFeedService = KalturaTestsHelpers::getServiceInitializedForAction("syndicationFeed", "delete", null, null, $this->adminKs);
	    
		$syndicationFeedService->deleteAction($newSyndicationFeed->id);
		
		try{
			$syndicationFeedGet = $syndicationFeedService->getAction($newSyndicationFeed->id);
		}
		catch(KalturaAPIException $e)
		{
			return;
		}
		
		$this->assertNull($syndicationFeedGet);
	}
	
	public function testList()
	{
		$addedItems = array();
		for($i = 0; $i < 5; $i++)
		{
			$newSyndicationFeed = $this->testAdd();
			$addedItems[$newSyndicationFeed->id] = $newSyndicationFeed; 
		}
		
		$filter = new KalturaBaseSyndicationFeedFilter();
		
		$pager = new KalturaFilterPager();
		$pager->pageSize = 10;
		$pager->pageIndex = 0;
		
	    $syndicationFeedService = KalturaTestsHelpers::getServiceInitializedForAction("syndicationFeed", "list", null, null, $this->adminKs);
	    $newList = $syndicationFeedService->listAction($filter, $pager);
	
		$this->assertEquals(count($addedItems), $newList->totalCount);
		
		foreach($newList->objects as $syndicationFeed)
			$this->assertArrayHasKey($syndicationFeed->id, $addedItems);
	}
	
	private function assertSyndicationFeed($expectedSyndicationFeed, $actualSyndicationFeed)
	{
		$this->assertEquals($expectedSyndicationFeed->partnerId, $actualSyndicationFeed->partnerId);
		$this->assertEquals($expectedSyndicationFeed->categories, $actualSyndicationFeed->categories);
		$this->assertEquals($expectedSyndicationFeed->type, $actualSyndicationFeed->type);
		$this->assertEquals($expectedSyndicationFeed->name, $actualSyndicationFeed->name);
		$this->assertEquals($expectedSyndicationFeed->landingPage, $actualSyndicationFeed->landingPage);
		$this->assertEquals($expectedSyndicationFeed->playlistId, $actualSyndicationFeed->playlistId);
		$this->assertEquals($expectedSyndicationFeed->allowEmbed, $actualSyndicationFeed->allowEmbed);
		$this->assertEquals($expectedSyndicationFeed->playerUiconfId, $actualSyndicationFeed->playerUiconfId);
		$this->assertEquals($expectedSyndicationFeed->flavorParamId, $actualSyndicationFeed->flavorParamId);
		$this->assertEquals($expectedSyndicationFeed->transcodeExistingContent, $actualSyndicationFeed->transcodeExistingContent);
		$this->assertEquals($expectedSyndicationFeed->addToDefaultConversionProfile, $actualSyndicationFeed->addToDefaultConversionProfile);
		$this->assertSyndicationFeedByType($expectedSyndicationFeed, $actualSyndicationFeed);
	}
	
	private function assertSyndicationFeedByType($expectedSyndicationFeed, $actualSyndicationFeed)
	{
		switch($expectedSyndicationFeed->type)
		{
			case KalturaSyndicationFeedType::ITUNES:
				// itunes specific:
				$this->assertEquals($expectedSyndicationFeed->feedDescription, $actualSyndicationFeed->feedDescription);
				$this->assertEquals($expectedSyndicationFeed->language, $actualSyndicationFeed->language);
				$this->assertEquals($expectedSyndicationFeed->feedLandingPage, $actualSyndicationFeed->feedLandingPage);
				$this->assertEquals($expectedSyndicationFeed->ownerName, $actualSyndicationFeed->ownerName);
				$this->assertEquals($expectedSyndicationFeed->ownerEmail, $actualSyndicationFeed->ownerEmail);
				$this->assertEquals($expectedSyndicationFeed->feedImageUrl, $actualSyndicationFeed->feedImageUrl);
				$this->assertEquals($expectedSyndicationFeed->categories, $actualSyndicationFeed->categories);
				$this->assertEquals($expectedSyndicationFeed->adultContent, $actualSyndicationFeed->adultContent);
				break;
			case KalturaSyndicationFeedType::GOOGLE_VIDEO:
				$this->assertEquals($expectedSyndicationFeed->adultContent, $actualSyndicationFeed->adultContent);
				break;
			case KalturaSyndicationFeedType::TUBE_MOGUL:
				$this->assertEquals($expectedSyndicationFeed->categories, $actualSyndicationFeed->categories);
				break;
			case KalturaSyndicationFeedType::YAHOO:
				$this->assertEquals($expectedSyndicationFeed->adultContent, $actualSyndicationFeed->adultContent);
				$this->assertEquals($expectedSyndicationFeed->categories, $actualSyndicationFeed->categories);
				break;
		}
	}
	
	private function prepareSyndicationFeed($syndicationFeed = null)
	{
		if(is_null($syndicationFeed))
		{
			$randomType = rand(1,4);
			$syndicationFeed = KalturaSyndicationFeedFactory::getInstanceByType($randomType);
			$syndicationFeed->partnerId = KalturaTestsHelpers::getPartner()->getId();
			$syndicationFeed->createdAt = time();
		}
		else
		{
			$syndicationFeed = clone $syndicationFeed;
			
			$syndicationFeed->id = null;
			$syndicationFeed->partnerId = null;
			$syndicationFeed->status = null;
			// do not reset type, we need to keep it for validation between the different types
			//$syndicationFeed->type = null;
			$syndicationFeed->feedUrl = null;
			$syndicationFeed->createdAt = null;
			
			$syndicationFeed->categories = null;
			$syndicationFeed->name = null;
			$syndicationFeed->landingPage = null;
			$syndicationFeed->playlistId = null;
			$syndicationFeed->allowEmbed = null;
			$syndicationFeed->playerUiconfId = null;
			$syndicationFeed->flavorParamId = null;
			$syndicationFeed->transcodeExistingContent = null;
			$syndicationFeed->addToDefaultConversionProfile = null;
			
			$this->setFieldsBySyndicationType($syndicationFeed, true);
		}
		
		$syndicationFeed->name = $this->unique_test_name;
		$syndicationFeed->playlistId = $this->randomizePlaylistId();
		$syndicationFeed->flavorParamId = $this->randomizeFlavorParamId();
		$syndicationFeed->playerUiconfId = $this->randomizePlayerUiconfId();
		$syndicationFeed->transcodeExistingContent = (rand(0,1))? true: false;
		$syndicationFeed->addToDefaultConversionProfile = (rand(0,1))? true: false;
		$syndicationFeed->allowEmbed = (rand(0,1))? true: false;
		$syndicationFeed->landingPage = 'http://www.'.KalturaTestsHelpers::getRandomString(10).'.'.KalturaTestsHelpers::getRandomString(3).'/';
		
		$this->setFieldsBySyndicationType($syndicationFeed);
		
		return $syndicationFeed;
	}
	
	public function setFieldsBySyndicationType(&$syndicationFeed, $reset = false)
	{
		switch($syndicationFeed->type)
		{
			case KalturaSyndicationFeedType::ITUNES:
				// itunes specific:
				$syndicationFeed->feedDescription = ($reset)? null:KalturaTestsHelpers::getRandomString(50);
				$syndicationFeed->language = ($reset)? null:KalturaTestsHelpers::getRandomString(2);
				$syndicationFeed->feedLandingPage = ($reset)? null:'http://www.'.KalturaTestsHelpers::getRandomString(10).'.com';
				$syndicationFeed->ownerName = ($reset)? null:KalturaTestsHelpers::getRandomString(10);
				$syndicationFeed->ownerEmail = ($reset)? null:$syndicationFeed->ownerName.'@'.KalturaTestsHelpers::getRandomString(10).'.com';
				$syndicationFeed->feedImageUrl = ($reset)? null:'http://www.'.KalturaTestsHelpers::getRandomString(10).'.'.KalturaTestsHelpers::getRandomString(3).'/image.jpg';
				$syndicationFeed->feedAuthor = ($reset)? null:KalturaTestsHelpers::getRandomString(50);
				$syndicationFeed->categories = ($reset)? null:$this->generateCategories($syndicationFeed->type);
				$syndicationFeed->adultContent = ($reset)? null:$this->generateAdultItunes();
				break;
			case KalturaSyndicationFeedType::GOOGLE_VIDEO:
				$syndicationFeed->adultContent = ($reset)? null:$this->generateAdultGoogle();
				break;
			case KalturaSyndicationFeedType::TUBE_MOGUL:
				$syndicationFeed->categories = ($reset)? null:$this->generateCategories($syndicationFeed->type);
				break;
			case KalturaSyndicationFeedType::YAHOO:
				$syndicationFeed->feedDescription = ($reset)? null:KalturaTestsHelpers::getRandomString(50);
				$syndicationFeed->adultContent = ($reset)? null:$this->generateAdultYahoo();
				$syndicationFeed->feedLandingPage = ($reset)? null:'http://www.'.KalturaTestsHelpers::getRandomString(10).'.com';
				$syndicationFeed->categories = ($reset)? null:$this->generateCategories($syndicationFeed->type);
				break;
		}
		return $syndicationFeed;
	}
	
	public function generateCategories($type)
	{
		switch($type)
		{
			case KalturaSyndicationFeedType::ITUNES:
				$categoriesEnum = new KalturaTypeReflector('KalturaITunesSyndicationFeedCategories');
				break;
			case KalturaSyndicationFeedType::TUBE_MOGUL:
				$categoriesEnum = new KalturaTypeReflector('KalturaTubeMogulSyndicationFeedCategories');
				break;
			case KalturaSyndicationFeedType::YAHOO:
				$categoriesEnum = new KalturaTypeReflector('KalturaYahooSyndicationFeedCategories');
				break;
			default: return '';
		}
		$categories = $categoriesEnum->getConstants();
		$count = rand(1,5);
		for(;$count >= 0; $count--)
		{
			$cat_id = rand(0, count($categories)-1);
			$temp_cat = $categories[$cat_id];
			$cats[] = $temp_cat->getDefaultValue();
		}
			
		return implode(',', $cats);
	}
	
	public function generateAdultItunes()
	{
		$num = rand(1,3);
		if($num == 1) return KalturaITunesSyndicationFeedAdultValues::YES;
		if($num == 2) return KalturaITunesSyndicationFeedAdultValues::CLEAN;
		if($num == 3) return KalturaITunesSyndicationFeedAdultValues::NO;
	}
	
	public function generateAdultGoogle()
	{
		$num = rand(1,10);
		if($num%2) return KalturaGoogleSyndicationFeedAdultValues::YES;
		else return KalturaGoogleSyndicationFeedAdultValues::NO;
	}
	
	public function generateAdultYahoo()
	{
		$num = rand(1,10);
		if($num%2) return KalturaYahooSyndicationFeedAdultValues::ADULT;
		else return KalturaYahooSyndicationFeedAdultValues::NON_ADULT;
	}
	
	public function randomizePlaylistId()
	{
		$should_playlist = rand(0,2);
		if(!$should_playlist) return null;
		
		$c = new Criteria();
		$c->addAnd(entryPeer::PARTNER_ID, KalturaTestsHelpers::getPartner()->getId());
		$c->addAnd(entryPeer::TYPE, entry::ENTRY_TYPE_PLAYLIST);
		$c->addAnd(entryPeer::STATUS, entry::ENTRY_STATUS_READY);
		$c->setLimit(100);
		$c->setOffset(rand(1,4));
		$c->addSelectColumn(entryPeer::ID);
		$playlists = entryPeer::doSelect($c);
		shuffle($playlists);
		$item = rand(0,count($playlists)-1);
		return $playlists[$item];
	}
	
	public function randomizeFlavorParamId()
	{
		return rand(1,55);
	}
	
	public function randomizePlayerUiconfId()
	{
		return rand(100,666);
	}
}
?>
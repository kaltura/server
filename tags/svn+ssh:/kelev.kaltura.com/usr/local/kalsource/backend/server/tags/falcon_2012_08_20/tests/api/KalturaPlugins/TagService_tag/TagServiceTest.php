<?php

require_once(dirname(__FILE__) . '/../../../bootstrap.php');

/**
 * tag service test case.
 */
class TagServiceTest extends TagServiceTestBase
{
	/**
	 * Tests tag->search action
	 * @param KalturaTagFilter $tagFilter
	 * @param KalturaFilterPager $pager
	 * @param KalturaTagListResponse $reference
	 * @dataProvider provideData
	 */
	public function testSearch(KalturaTagFilter $tagFilter, KalturaFilterPager $pager , KalturaTagListResponse $reference)
	{
	    
	    $tagPlugin = KalturaTagSearchClientPlugin::get($this->client);
		$resultObject = $tagPlugin->tag->search($tagFilter, $pager);
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaTagListResponse', $resultObject);
		else
			$this->assertType('KalturaTagListResponse', $resultObject);
			
		$this->assertAPIObjects($reference, $resultObject, array('totalCount'));
	}
	
	/**
	 * 
	 * Test to see whether if several entries are tagged with a string, only 1 tag is created on the DB.
	 */
	public function testTagUniqueness ()
	{
	    $numOfEntries = rand(0, 10);
	    $tagString = uniqid("autotag");
	    
	    for ($i = 0; $i < $numOfEntries; $i++)
	    {
	        $entry = new KalturaBaseEntry();
	        $entry->name = "entry_$i";
	        $entry->tags .= $tagString;
	        $entry = $this->client->baseEntry->add($entry);
	    }
	    
	    $tagFilter = new KalturaTagFilter();
	    $tagFilter->tagStartsWith = $tagString;
	    $tagFilter->objectTypeEqual = KalturaTaggedObjectType::ENTRY;
	    
	    $tagPlugin = KalturaTagSearchClientPlugin::get($this->client);
	    $searchResult = $tagPlugin->tag->search($tagFilter);
	    
	    $this->assertEquals(1, count($searchResult->objects));
	    
	    $this->assertEquals($numOfEntries, $searchResult->objects[0]->instanceCount);
	    
	}
	
	
    
	

	/**
	 * Tests tag->search action
	 * @param array<string, int> $tagsConfig array<tag suffix, count in different entries>
	 * @dataProvider provideData
	 */
	public function testAddAndSearch(array $tagsConfig)
	{
	    $time = time();
	    
	    $entries = array();
	    for($i = 0; $i < max($tagsConfig); $i++)
	    {
	        $entries[] = new KalturaMediaEntry();
	    }
	    
	    $tags = array();
	    $count = 0;
	    foreach($tagsConfig as $suffix=> $tagCount)
	    {
    	    $tag = $time . $suffix;
    	    for($i = 0; $i < $tagCount; $i++)
    	    {
    	        $count++;
    	        if ($entries[$i]->tags !== "")
    	        {
    	            $entries[$i]->tags .= ",$tag";
    	        }
    	        else 
    	        {
    	            $entries[$i]->tags .= "$tag";
    	        }
    	    }
	    }
	    
	    foreach ($entries as $index=> $entry)
	    {
	        $entry->name = $time.$index;
	        $entry->mediaType = KalturaMediaType::VIDEO;
	        $this->client->media->add($entry);
	    }
	    
	    $tagPlugin = KalturaTagSearchClientPlugin::get($this->client);
	    foreach ($tagsConfig as $suffix=>$tagCount)
	    {
	        $tagFilter = new KalturaTagFilter();
	        $tagFilter->tagStartsWith = $time.$suffix;
	        $tagFilter->objectTypeEqual = KalturaTaggedObjectType::ENTRY;
	        
	        $resultObject = $tagPlugin->tag->search($tagFilter);
	        
	        foreach ($resultObject->objects as $kalturaTag)
	        {
	            $this->assertEquals(intval($tagCount), $kalturaTag->instanceCount);
	            
	            $this->assertGreaterThanOrEqual(1, $kalturaTag->instanceCount);
	            
	            //check that the amount of results is 1 every time
	        }
	    }	    
	}
	
	
	/**
	 * Test for deletion of tags from the system
	 * @var int $numTestTags
	 * @dataProvider provideData
	 */
    public function testDeleteTags($numTestTags)
	{
	    $suffix= uniqid();
	    
	    $entries = $this->addTaggedEntries($numTestTags, $suffix);
	    
	    $tagPlugin = KalturaTagSearchClientPlugin::get($this->client);
	    for ($i=0; $i < count($entries); $i++)
	    {
	        $this->client->media->delete($entries[$i]->id);
	        
	        for ($j = $i; $j < count($entries); $j++)
	        {
	            $tagFilter = new KalturaTagFilter();
	            $tagFilter->tagStartsWith = ($j.$suffix);
	            $tagFilter->objectTypeEqual = KalturaTaggedObjectType::ENTRY;
	            $searchResult = $tagPlugin->tag->search($tagFilter);
    	        if ($j > $i)
    	        {
    	            //will always return only 1 result
    	            $this->assertEquals($j-$i ,$searchResult->objects[0]->instanceCount,"Failed asserting equality for tag  $j deleted entry $i");
    	        }
    	        
    	        else 
    	        {
    	            $this->assertEquals(0, count($searchResult->objects));
    	        }
	        }
	    }
	    
	}
	
	
	
	/**
	 * Tests deletion of tags, each of which is exclusive to a single entry
	 * @param int $numOfTags
	 * @dataProvider provideData
	 */
	public function testMutuallyExclusiveDelete ($numOfTags)
	{
	    $tagsArr = array();
	    for ($i=0; $i<$numOfTags; $i++)
	    {
	        $tagsArr[] = uniqid();
	    }
	    
	    //Add entries
	    $entries = array();
	    foreach ($tagsArr as $tag)
	    {
	        $entry = new KalturaBaseEntry();
	        
	        $entry->name = "entryTest_$tag";
	        
	        $entry->tags.= $tag;
	        
	        $entries[] = $this->client->baseEntry->add($entry);
	    }
	    
	    $indexToDelete = rand(0, count($tagsArr)-1);
	    
	    $tagPlugin = KalturaTagSearchClientPlugin::get($this->client);
	    
	    $this->client->baseEntry->delete($entries[$indexToDelete]->id);
	    
	    foreach ($tagsArr as $tag)
	    {
	        $tagFilter = new KalturaTagFilter();
	        
	        $tagFilter->objectTypeEqual = KalturaTaggedObjectType::ENTRY;
	        
	        $tagFilter->tagStartsWith = $tag;
	        
	        $searchResult = $tagPlugin->tag->search($tagFilter);
	        
	        if ($tag == $entries[$indexToDelete]->tags)
	        {
	            $this->assertEquals(0, count($searchResult->objects));
	        }
	        else 
	        {
	            $this->assertEquals(1, $searchResult->objects[0]->instanceCount);
	        }
	    }
	}
	
//	public function testUpdateEntryTags (array $tagsToAdd, array $tagsToUpdate, array $refereceResult)
//	{
//	    
//	}
	
/**
	 * 
	 * Check whether when searching tags receive expected results
	 * @param array $tagsToAdd
	 * @param array $tagsToSearch
	 * @param array $expectedResults
	 * @dataProvider provideData
	 */
	public function testAdvancedSearch (array $tagsToAdd, array $tagsToSearch, array $expectedResults)
	{
	    $entry = new KalturaBaseEntry();
	        
	    $entry->name = "entry_testAdSearch";
	    foreach ($tagsToAdd as $tagToAdd)
	    {
            if ($entry->tags && $entry->tags != "")
            {
                $entry->tags .= ",".$tagToAdd;
            }
            else
            {
                $entry->tags .= $tagToAdd;
            }
	        
	    }
	    
	    $this->client->baseEntry->add($entry);
	    
	    $tagClient = KalturaTagSearchClientPlugin::get($this->client);
	    
	    foreach ($tagsToSearch as $index=>$tagToSearch)
	    {
	        try
	        {
    	        $tagFilter = new KalturaTagFilter();
    	        $tagFilter->objectTypeEqual = KalturaTaggedObjectType::ENTRY;
    	        $tagFilter->tagStartsWith = $tagToSearch;
    	        $searchResult = $tagClient->tag->search($tagFilter);
	        }
	        catch (Exception $e)
	        {
	            if ($e->getCode() == "PROPERTY_VALIDATION_MIN_LENGTH")
	            {
	                $this->assertEquals("error", $expectedResults[$index]);
	                return;
	            }
	        }
	        
	        $this->assertEquals($expectedResults[$index], $searchResult->totalCount);
	        
	    }
	    
	    
	}
	
	protected function addTaggedEntries ($numTestTags, $suffix)
	{
	    $entries = array();
	    for ($i=0; $i< $numTestTags; $i++)
	    {
	        $entry = new KalturaMediaEntry();
	        $entry->name = "entry_".$i;
	        $entry->mediaType = KalturaMediaType::VIDEO;
	        $entries[$i] = $entry;
	        
	        $tagName = $i.$suffix;
	        for ($j = 0; $j<=$i; $j++)
	        {
	            if ($entries[$j]->tags == "")
	            {
	                $entries[$j]->tags .= $tagName;
	            }
	            else 
	            {
	                $entries[$j]->tags .= ",$tagName";
	            }
	        }
	    }
	    
	    foreach ($entries as $index=>$entry)
	    {
	        $entries[$index] = $this->client->media->add($entry);
	    }
	    
	    return $entries;
	}
	
	protected function validateInstanceCountPositive (array $tags)
	{
	    foreach ($tags as $tag)
	    {
	        $this->assertGreaterThanOrEqual(1, $tag->instanceCount);
	    }
	}
	
}


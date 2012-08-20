<?php

require_once(dirname(__FILE__) . '/../../bootstrap.php');

/**
 * media service test case.
 */
class MediaServiceEntitlementTest extends MediaServiceTestBase
{
    
    /**
     * Test the addition of a mediaEntry with proeprty "categories" set.
     * @param string $categories
     * @dataProvider provideData
     */
    public function testAddWithCategories ($categories)
    {
        $mediaEntry = new KalturaMediaEntry();
        $mediaEntry->name = uniqid();
        $mediaEntry->mediaType = KalturaMediaType::VIDEO;
        $mediaEntry->categories = $categories;
        $mediaEntry = $this->client->media->add($mediaEntry);
        
        var_dump(KAutoloader::getClassFilePath(get_class($this->client)));
        $categoryNames = explode(",", $categories);
        foreach ($categoryNames as $categoryName)
        {
            $filter = new KalturaCategoryFilter();
            $filter->fullNameEqual = $categoryName;
            $results = $this->client->category->listAction($filter);
            $this->assertEquals(1, $results->totalCount, "Unexpected number of categories with fullname $categoryName.");
            
            
            $catId = $results->objects[0]->id;
            // Assert that the entry's "categoriesIds" property was updated properly.
            $entryCatIds = explode(',', $mediaEntry->categoriesIds);
            $this->assertTrue(in_array($catId, $entryCatIds), "Entry's categoriesIds property should containt category Id [$catId] for category [$categoryName]");
            //Assert that a KalturaCategoryEntry object was created for the entry and each category it was associated with.
            $catEntryFilter = new KalturaCategoryEntryFilter();
            $catEntryFilter->categoryIdEqual = $catId;
            $catEntryFilter->entryIdEqual = $mediaEntry->id;
            try {
                $res = $this->client->categoryEntry->listAction($catEntryFilter);
                $this->assertGreaterThan(0, $res->totalCount > 0);
            }
            catch (Exception $e)
            {
                $this->assertEquals(0, 1, "Unexpected exception thrown - expected categoryEntry object for entry Id". $mediaEntry->id ." and category Id $catId");
            }
        }
    }
    /**
     * Test what happens after updating entry's "categories" property
     * @param string $categories
     * @dataProvider provideData
     */
    public function testUpdateCategories ($categories)
    {
        //$categories = "category1,category2>category3";
        $mediaEntry = new KalturaMediaEntry();
        $mediaEntry->name = uniqid();
        $mediaEntry->mediaType = KalturaMediaType::VIDEO;
        $mediaEntry = $this->client->media->add ($mediaEntry);
        
        $update =  new KalturaMediaEntry();
        $update->categories = $categories;
        $mediaEntry = $this->client->media->update($mediaEntry->id, $update);
        
        $categoryNames = explode(",", $categories);
        foreach ($categoryNames as $categoryName)
        {
            $filter = new KalturaCategoryFilter();
            $filter->fullNameEqual = $categoryName;
            $results = $this->client->category->listAction($filter);
            $this->assertEquals(1, $results->totalCount, "Unexpected number of categories with fullname $categoryName.");
            
            $catId = $results->objects[0]->id;
            $catEntryFilter = new KalturaCategoryEntryFilter();
            $catEntryFilter->categoryIdEqual = $catId;
            $catEntryFilter->entryIdEqual = $mediaEntry->id;
            try {
                $res = $this->client->categoryEntry->listAction($catEntryFilter);
                $this->assertGreaterThan(0, $res->totalCount > 0);
            }
            catch (Exception $e)
            {
                $this->assertEquals(0, 1, "Unexpected exception thrown - expected categoryEntry object for entry Id". $mediaEntry->id ." and category Id $catId");
            }
        }
    }
    
    /**
     * Checks validity of categoriesIds property 
     * @param string $categoriesIds
     * @dataProvider provideData
     */
    public function testCategoriesIds ($categoriesIds)
    {
        $mediaEntry = new KalturaMediaEntry();
        $mediaEntry->name = uniqid("mediaEntitlementTest_");
        $mediaEntry->mediaType = KalturaMediaType::VIDEO;
        $mediaEntry->categoriesIds = $categoriesIds;
        
        $mediaEntry = $this->client->media->add($mediaEntry);
        $categoriesIdsArr = explode(',', $categoriesIds);
        
        foreach ($categoriesIdsArr as $categoryId)
        {
            $catEntryFilter = new KalturaCategoryEntryFilter();
            $catEntryFilter->categoryIdEqual = $categoryId;
            $catEntryFilter->entryIdEqual = $mediaEntry->id;
            try {
                $res = $this->client->categoryEntry->listAction($catEntryFilter);
                $this->assertGreaterThan(0, $res->totalCount > 0);
            }
            catch (Exception $e)
            {
                $this->assertEquals(0, 1, "Unexpected exception thrown - expected categoryEntry object for entry Id ". $mediaEntry->id ." and category Id $categoryId");
            }
        }
        
    }
    
}
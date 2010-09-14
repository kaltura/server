<?php
require_once("tests/bootstrapTests.php");

class CategoryAddTests extends PHPUnit_Framework_TestCase 
{
	/**
	 * @var CategoryService
	 */
	protected $categoryService = null;
	
	public function setUp()
	{
		parent::setUp();
		$this->categoryPrefix = __CLASS__;
		$ks = KalturaTestsHelpers::getAdminKs();
		$this->categoryService = KalturaTestsHelpers::getServiceInitializedForAction("category", "add", null, null, $ks);
	}
	
	public function tearDown()
	{
		$criteria = new Criteria();
		$criteria->add(categoryPeer::NAME, $this->categoryPrefix."%", Criteria::LIKE);
		$categoriesDb = categoryPeer::doSelect($criteria);
		foreach($categoriesDb as $categoryDb)
			$categoryDb->delete();

		$this->categoryService->__destruct(); 
		parent::tearDown();
	}
	
	
	public function testAdd()
	{
		$category = new KalturaCategory();
		$category->name = $this->categoryPrefix;
		
		$newCategory = $this->categoryService->addAction(clone $category);
		$this->assertNotNull($newCategory->id);
		$this->assertEquals(0, $newCategory->parentId);
		$this->assertEquals($category->name, $newCategory->name);
		$this->assertEquals(KalturaTestsHelpers::getPartner()->getId(), $newCategory->partnerId);
		$this->assertEquals($category->name, $newCategory->fullName); // for top category, full name is just the category name
		$this->assertEquals(0, $newCategory->entriesCount);
		$this->assertEquals(0, $newCategory->depth);
		
		return $newCategory;
	}
	
	public function testAddReadOnlyProperties()
	{
		$category = new KalturaCategory();
		$category->name = $this->categoryPrefix;
		
		$category->partnerId = KalturaTestsHelpers::getPartner()->getId() + 1;
		$this->tryCatchValidationReadOnlyProperty($category, "KalturaCategory::partnerId");
		$category->partnerId = null;

		$category->id = 2;
		$this->tryCatchValidationReadOnlyProperty($category, "KalturaCategory::id");
		$category->id = null;
		
		$category->depth = 2;
		$this->tryCatchValidationReadOnlyProperty($category, "KalturaCategory::depth");
		$category->depth = null;
		
		$category->entriesCount = 2;
		$this->tryCatchValidationReadOnlyProperty($category, "KalturaCategory::entriesCount");
		$category->entriesCount = null;
	}
	
	public function testAddOfDuplicateCategory()
	{
		$this->testAdd();
		
		try
		{
			$this->testAdd();
			$this->fail();
		}
		catch(KalturaAPIException $ex)
		{
			$this->assertEquals("DUPLICATE_CATEGORY", $ex->getCode());
		}
		catch(Exception $ex)
		{
			$this->fail();
		}
	}
	
	public function testAddOfDuplicateCategoryUnderParent()
	{
		$topCategory = new KalturaCategory();
		$topCategory->name = $this->categoryPrefix."TOP";
		$topCategory = $this->categoryService->addAction(clone $topCategory);
		
		$category = new KalturaCategory();
		$category->name = $this->categoryPrefix;
		$category->parentId = $topCategory->id;
		$newCategory = $this->categoryService->addAction(clone $category);
		$this->assertEquals($topCategory->id, $newCategory->parentId);
		
		try
		{
			$this->categoryService->addAction(clone $category);
			$this->fail();
		}
		catch(KalturaAPIException $ex)
		{
			$this->assertEquals("DUPLICATE_CATEGORY", $ex->getCode());
		}
	}
	
	public function testAddWhenParentCategoryNotFound()
	{
		// find parent category that doesnt exists
		$continue = true;
		$parentId = 1;
		while($continue)
		{
			$category = categoryPeer::retrieveByPK($parentId);
			if ($category)
				$parentId++;
			else
				$continue = false;
		}
		
		$category = new KalturaCategory();
		$category->name = $this->categoryPrefix;
		$category->parentId = $parentId;
		try
		{
			$this->categoryService->addAction(clone $category);
			$this->fail();
		}
		catch(KalturaAPIException $ex)
		{
			$this->assertEquals("PARENT_CATEGORY_NOT_FOUND", $ex->getCode());
		}
	}
	
	public function testAddOfChildCategory()
	{
		$topCategory = new KalturaCategory();
		$topCategory->name = $this->categoryPrefix."TOP";
		$topCategory = $this->categoryService->addAction(clone $topCategory);
		
		$category = new KalturaCategory();
		$category->name = $this->categoryPrefix."CHILD";
		$category->parentId = $topCategory->id;
		$childCategory = $this->categoryService->addAction(clone $category);
		$this->assertEquals($this->categoryPrefix."TOP>".$this->categoryPrefix."CHILD", $childCategory->fullName);
	}
	
	public function testReplaceOfInvalidCharacters()
	{
		$category = new KalturaCategory();
		$category->name = $this->categoryPrefix.">mycategory";
		$newCatagory = $this->categoryService->addAction(clone $category);
		$this->assertEquals($this->categoryPrefix."_mycategory", $newCatagory->name);
		$this->assertEquals($this->categoryPrefix."_mycategory", $newCatagory->fullName);
		
		$category = new KalturaCategory();
		$category->name = $this->categoryPrefix.",my,category";
		$newCatagory = $this->categoryService->addAction(clone $category);
		$this->assertEquals($this->categoryPrefix."_my_category", $newCatagory->name);
		$this->assertEquals($this->categoryPrefix."_my_category", $newCatagory->fullName);
	}
	
	public function testLinkToEntry()
	{
		$category = $this->testAdd();
		$mediaService = KalturaTestsHelpers::getServiceInitializedForAction("media", "addFromUrl");
	    $mediaEntry = MediaTestsHelpers::prepareMediaEntry();
	    $mediaEntry->categories = $category->fullName;
		$url = MediaTestsHelpers::prepareDummyUrl();
		$newMediaEntry = $mediaService->addFromUrlAction(clone $mediaEntry, $url);
		$this->assertEquals($category->fullName, $newMediaEntry->categories);

		$newMediaEntry = $mediaService->getAction($newMediaEntry->id);
		$this->assertEquals($category->fullName, $newMediaEntry->categories);

		$entryDb = entryPeer::retrieveByPK($newMediaEntry->id);
		$searchTextDiscrete = $entryDb->getSearchTextDiscrete();
		$this->assertType("numeric", strpos($searchTextDiscrete, "_CAT_".$category->id));
	}
	
	public function testEntriesCount()
	{
		$category = new KalturaCategory();
		$category->name = $this->categoryPrefix.__FUNCTION__;
		$category = $this->categoryService->addAction(clone $category);
		
		$mediaService = KalturaTestsHelpers::getServiceInitializedForAction("media", "addFromUrl");
	    $mediaEntry = MediaTestsHelpers::prepareMediaEntry();
	    $mediaEntry->categories = $category->fullName;
		$url = MediaTestsHelpers::prepareDummyUrl();
		
		// add entry linked to category and assert count to 1
		$newMediaEntry1 = $mediaService->addFromUrlAction(clone $mediaEntry, $url);
		$category = $this->categoryService->getAction($category->id);
		$this->assertEquals(1, $category->entriesCount);
		
		// add another entry to this category and assert count to 2
		$newMediaEntry2 = $mediaService->addFromUrlAction(clone $mediaEntry, $url);
		$category = $this->categoryService->getAction($category->id);
		$this->assertEquals(2, $category->entriesCount);
		
		$updateMediaEntry = new KalturaMediaEntry();
		$updateMediaEntry->categories = "";
		
		// update the first entry, remove the category and check that it is decremented to 1
		$mediaService->updateAction($newMediaEntry1->id, clone $updateMediaEntry);
		$category = $this->categoryService->getAction($category->id);
		$this->assertEquals(1, $category->entriesCount);
		
		// update the second entry, remove the category and check that it is decremented to 0
		$mediaService->updateAction($newMediaEntry2->id, clone $updateMediaEntry);
		$category = $this->categoryService->getAction($category->id);
		$this->assertEquals(0, $category->entriesCount);
	}
	
	public function testDynamicCategoryCreation()
	{
		$mediaService = KalturaTestsHelpers::getServiceInitializedForAction("media", "addFromUrl");
	    $mediaEntry = MediaTestsHelpers::prepareMediaEntry();
	    $mediaEntry->categories = $this->categoryPrefix."_".__FUNCTION__;
	    $url = MediaTestsHelpers::prepareDummyUrl();
	    
	    $category = categoryPeer::getByFullNameExactMatch($mediaEntry->categories);
	    $this->assertNull($category);
	    
	    $newMediaEntry = $mediaService->addFromUrlAction(clone $mediaEntry, $url);
	    
	    $category = categoryPeer::getByFullNameExactMatch($mediaEntry->categories);
	    $this->assertNotNull($category);
	    
	}
	
	public function testMaxCategoryName()
	{
		$category = new KalturaCategory();
		$category->name = $this->categoryPrefix;
		for($i = 0; $i < category::MAX_CATEGORY_NAME; $i++)
		{
			$category->name .= "a";
		}
		
		try
		{
			$category = $this->categoryService->addAction(clone $category);
			$this->fail();
		}
		catch(KalturaAPIException $ex)
		{
			$this->assertEquals("PROPERTY_VALIDATION_MAX_LENGTH", $ex->getCode());
		}
	}
	
	public function testMaxCategoryDepth()
	{
		$category = new KalturaCategory();
		$category->name = $this->categoryPrefix;
		
		$count = 0;
		while($count < category::MAX_CATEGORY_DEPTH + 5)
		{
			try
			{
				$newCategory = $this->categoryService->addAction(clone $category);
				$category->parentId = $newCategory->id;
				$count++;
			}
			catch(KalturaAPIEXception $ex)
			{
				$this->assertEquals("MAX_CATEGORY_DEPTH_REACHED", $ex->getCode());
				break;
			}
		}
		$this->assertEquals(category::MAX_CATEGORY_DEPTH, $count);
	}
	
	public function testMaxNumberOfCategories()
	{
		$c = new Criteria();
		$beforeCount = categoryPeer::doCount($c);
		$this->assertLessThan(Partner::MAX_NUMBER_OF_CATEGORIES, $beforeCount, "Can't run test when there are more categories then the limit");
		
		$category = new KalturaCategory();
		
		
		$count = $beforeCount;
		while($count < Partner::MAX_NUMBER_OF_CATEGORIES + 5)
		{
			try
			{
				$category->name = $this->categoryPrefix . $count;
				$this->categoryService->addAction(clone $category);
			}
			catch(KalturaAPIException $ex)
			{
				$this->assertEquals("MAX_NUMBER_OF_CATEGORIES_REACHED", $ex->getCode());
				break;
			}
			$count++;
		}
		$this->assertEquals(Partner::MAX_NUMBER_OF_CATEGORIES, $count);
	}
	
	protected function tryCatchValidationReadOnlyProperty($category, $property)
	{
		try
		{
			$this->categoryService->addAction(clone $category);
			$this->fail();
		}
		catch(KalturaAPIException $ex)
		{
			AssertValidationHelpers::assertNotUpdatable($ex, $property);
		}
	}
}
?>
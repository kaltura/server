<?php
require_once("tests/bootstrapTests.php");

class CategoryUpdateTests extends PHPUnit_Framework_TestCase 
{
	/**
	 * @var categoryService
	 */
	protected $categoryService = null;
	
	public function setUp()
	{
		parent::setUp();
		$this->categoryPrefix = __CLASS__;
		$ks = KalturaTestsHelpers::getAdminKs();
		$this->categoryService = KalturaTestsHelpers::getServiceInitializedForAction("category", "update", null, null, $ks);
	}
	
	public function tearDown()
	{
		categoryPeer::setUseCriteriaFilter(false);
		$criteria = new Criteria();
		$criteria->add(categoryPeer::NAME, $this->categoryPrefix."%", Criteria::LIKE);
		$categoriesDb = categoryPeer::doSelect($criteria);
		foreach($categoriesDb as $categoryDb)
			$categoryDb->delete();

		categoryPeer::setUseCriteriaFilter(true);
		$this->categoryService->__destruct(); 
		parent::tearDown();
	}
	
	public function testUpdate()
	{
		$category = new KalturaCategory();
		$category->name = $this->categoryPrefix;
		$newCategory = $this->categoryService->addAction($category);
		
		$category = new KalturaCategory();
		$category->name = $this->categoryPrefix."2";
		$updatedCategory = $this->categoryService->updateAction($newCategory->id, clone $category);
		$this->assertNotNull($updatedCategory->id);
		$this->assertEquals(0, $updatedCategory->parentId);
		$this->assertEquals($category->name, $updatedCategory->name);
		$this->assertEquals(KalturaTestsHelpers::getPartner()->getId(), $updatedCategory->partnerId);
		$this->assertEquals($category->name, $updatedCategory->fullName); // for top category, full name is just the category name
		$this->assertEquals(0, $updatedCategory->entriesCount);
		$this->assertEquals(0, $updatedCategory->depth);
	}
	
	public function testUpdateDuplicateName()
	{
		$category = new KalturaCategory();
		$category->name = $this->categoryPrefix."1";
		$cat1 = $this->categoryService->addAction(clone $category);
		
		$category = new KalturaCategory();
		$category->name = $this->categoryPrefix."2";
		$cat2 = $this->categoryService->addAction(clone $category);
		
		
		$category = new KalturaCategory();
		$category->name = $this->categoryPrefix."1";
		try
		{
			$this->categoryService->updateAction($cat2->id, clone $category);
			$this->fail();
		}
		catch(KalturaAPIException $ex)
		{
			$this->assertEquals("DUPLICATE_CATEGORY", $ex->getCode());
		}
	}
	
	public function testUpdateReadOnlyProperties()
	{
		$category = new KalturaCategory();
		$category->name = $this->categoryPrefix;
		$newCategory = $this->categoryService->addAction($category);
		
		$category = new KalturaCategory();
		$category->partnerId = KalturaTestsHelpers::getPartner()->getId() + 1;
		$this->tryCatchValidationReadOnlyProperty($newCategory->id, $category, "KalturaCategory::partnerId");

		$category = new KalturaCategory();
		$category->id = 2;
		$this->tryCatchValidationReadOnlyProperty($newCategory->id, $category, "KalturaCategory::id");
		
		$category = new KalturaCategory();
		$category->depth = 2;
		$this->tryCatchValidationReadOnlyProperty($newCategory->id, $category, "KalturaCategory::depth");

		$category = new KalturaCategory();
		$category->entriesCount = 2;
		$this->tryCatchValidationReadOnlyProperty($newCategory->id, $category, "KalturaCategory::entriesCount");
	}
	
	public function testUpdateWhenLinkedToEntry()
	{
		// add the category
		$category = new KalturaCategory();
		$category->name = $this->categoryPrefix;
		$newCategory = $this->categoryService->addAction($category);
		
		// add the entry with the category
		$mediaService = KalturaTestsHelpers::getServiceInitializedForAction("media", "addFromUrl");
	    $mediaEntry = MediaTestsHelpers::prepareMediaEntry();
	    $mediaEntry->categories = $category->fullName;
		$url = MediaTestsHelpers::prepareDummyUrl();
		$newMediaEntry = $mediaService->addFromUrlAction(clone $mediaEntry, $url);
		
		// check category name was saved on entry
		$newMediaEntry = $mediaService->getAction($newMediaEntry->id);
		$this->assertEquals($newCategory->fullName, $newMediaEntry->categories);
		
		// update category name
		$category = new KalturaCategory();
		$category->name = $this->categoryPrefix."UPDATED";
		$updatedCategory = $this->categoryService->updateAction($newCategory->id, clone $category);
		
		// check category name was update on entry
		$newMediaEntry = $mediaService->getAction($newMediaEntry->id);
		$this->assertEquals($updatedCategory->fullName, $newMediaEntry->categories);
		
		return array($updatedCategory, $newMediaEntry);
	}
	
	public function testUpdateOfParentReflectToChildFullNames()
	{
		$category = new KalturaCategory();
		$category->name = $this->categoryPrefix."LEVEL1";
		$level1Category = $this->categoryService->addAction($category);
		
		$category = new KalturaCategory();
		$category->name = $this->categoryPrefix."LEVEL2";
		$category->parentId = $level1Category->id;
		$level2Category = $this->categoryService->addAction($category);
		
		$category = new KalturaCategory();
		$category->name = $this->categoryPrefix."LEVEL3";
		$category->parentId = $level2Category->id;
		$level3Category = $this->categoryService->addAction($category);
		
		$category = new KalturaCategory();
		$category->name = $this->categoryPrefix."LEVEL_UPDATED";
		$this->categoryService->updateAction($level1Category->id, $category);
		
		$level2Category = $this->categoryService->getAction($level2Category->id);
		$this->assertEquals($this->categoryPrefix."LEVEL_UPDATED>".$this->categoryPrefix."LEVEL2", $level2Category->fullName);
		
		$level3Category = $this->categoryService->getAction($level3Category->id);
		$this->assertEquals($this->categoryPrefix."LEVEL_UPDATED>".$this->categoryPrefix."LEVEL2>".$this->categoryPrefix."LEVEL3", $level3Category->fullName);
	}
	
	public function testMaxCategoryName()
	{
		$category = new KalturaCategory();
		$category->name = $this->categoryPrefix;
		$category = $this->categoryService->addAction(clone $category);
		
		$categoryForUpdate = new KalturaCategory();
		$categoryForUpdate->name = $this->categoryPrefix;
		
		for($i = 0; $i < category::MAX_CATEGORY_NAME; $i++)
		{
			$categoryForUpdate->name .= "a";
		}
		
		try
		{
			$category = $this->categoryService->updateAction($category->id, clone $categoryForUpdate);
			$this->fail();
		}
		catch(KalturaAPIException $ex)
		{
			$this->assertEquals("PROPERTY_VALIDATION_MAX_LENGTH", $ex->getCode());
		}
	}
	
	public function testCategoryUpdateParentIsTheChild()
	{
		$category = new KalturaCategory();
		$category->name = $this->categoryPrefix."LEVEL1";
		$level1Category = $this->categoryService->addAction($category);
		
		$category = new KalturaCategory();
		$category->name = $this->categoryPrefix."LEVEL2";
		$category->parentId = $level1Category->id;
		$level2Category = $this->categoryService->addAction($category);
		
		
		$category = new KalturaCategory();
		$category->name = $this->categoryPrefix."LEVEL3";
		$category->parentId = $level2Category->id;
		$level3Category = $this->categoryService->addAction($category);
		
		
		$category = new KalturaCategory();
		$category->parentId = $level3Category->id;
		try
		{
			$this->categoryService->updateAction($level1Category->id, $category);
			$this->fail();
		}
		catch(KalturaAPIException $ex)
		{
			$this->assertEquals("PARENT_CATEGORY_IS_CHILD", $ex->getCode());
		}
	}
	
	protected function tryCatchValidationReadOnlyProperty($id, $category, $property)
	{
		try
		{
			$this->categoryService->updateAction($id, clone $category);
			$this->fail();
		}
		catch(KalturaAPIException $ex)
		{
			AssertValidationHelpers::assertNotUpdatable($ex, $property);
		}
	}
}
?>
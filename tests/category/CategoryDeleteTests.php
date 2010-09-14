<?php
require_once("tests/bootstrapTests.php");

class CategoryDeleteTests extends PHPUnit_Framework_TestCase 
{
	protected $categoryService = null;
	
	public function setUp()
	{
		parent::setUp();
		$this->categoryPrefix = __CLASS__;
		$ks = KalturaTestsHelpers::getAdminKs();
		$this->categoryService = KalturaTestsHelpers::getServiceInitializedForAction("category", "delete", null, null, $ks);
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
	
	
	public function testDelete()
	{
		$category = new KalturaCategory();
		$category->name = $this->categoryPrefix;
		$newCategory = $this->categoryService->addAction(clone $category);
		$newCategory = $this->categoryService->getAction($newCategory->id);
		$this->assertNotNull($newCategory);
		
		$res = $this->categoryService->deleteAction($newCategory->id);
		$this->assertNull($res);
		
		try
		{
			$this->categoryService->getAction($newCategory->id);
			$this->fail();
		}
		catch(KalturaAPIException $ex)
		{
			$this->assertEquals("CATEGORY_NOT_FOUND", $ex->getCode());
		}
	}
	
	public function testDeleteOfParentDeletesTheChilds()
	{
		$category = new KalturaCategory();
		$category->name = $this->categoryPrefix;
		$level1Category = $this->categoryService->addAction(clone $category);
		
		$category = new KalturaCategory();
		$category->name = $this->categoryPrefix;
		$category->parentId = $level1Category->id;
		$level2Category = $this->categoryService->addAction(clone $category);
		
		$category = new KalturaCategory();
		$category->name = $this->categoryPrefix;
		$category->parentId = $level2Category->id;
		$level3Category = $this->categoryService->addAction(clone $category);
		
		$this->categoryService->deleteAction($level1Category->id);
		
		try
		{
			$this->categoryService->getAction($level2Category->id);
			$this->fail();
		}
		catch(KalturaAPIException $ex)
		{
			$this->assertEquals("CATEGORY_NOT_FOUND", $ex->getCode());
		}
		
		try
		{
			$this->categoryService->getAction($level3Category->id);
			$this->fail();
		}
		catch(KalturaAPIException $ex)
		{
			$this->assertEquals("CATEGORY_NOT_FOUND", $ex->getCode());
		}
	}
	
	public function testDeleteWhenLinkedToEntries()
	{
		$categoriesUpdateTests = new CategoryUpdateTests();
		$categoriesUpdateTests->setUp();
		list($category, $mediaEntry) = $categoriesUpdateTests->testUpdateWhenLinkedToEntry();
		$this->assertContains($category->fullName, $mediaEntry->categories);
		
		$this->categoryService->deleteAction($category->id);
		
		$mediaService = KalturaTestsHelpers::getServiceInitializedForAction("media", "get");
		$mediaEntry = $mediaService->getAction($mediaEntry->id);
		$this->assertNotContains($category->fullName, $mediaEntry->categories);
	}
}
?>
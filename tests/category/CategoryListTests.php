<?php
require_once("tests/bootstrapTests.php");

class CategoryListTests extends PHPUnit_Framework_TestCase 
{
	protected $categoryService = null;
	
	public function setUp()
	{
		parent::setUp();
		$this->categoryPrefix = __CLASS__;
		$ks = KalturaTestsHelpers::getAdminKs();
		$this->categoryService = KalturaTestsHelpers::getServiceInitializedForAction("category", "list", null, null, $ks);
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
	
	public function testList()
	{
		$categoryResponse = $this->categoryService->listAction();
		$originalCount = $categoryResponse->totalCount;
		
		$category = new KalturaCategory();
		$category->name = $this->categoryPrefix."CAT1";
		$category1 = $this->categoryService->addAction(clone $category);
		
		$category->name = $this->categoryPrefix."CAT2";
		$category2 = $this->categoryService->addAction(clone $category);
		
		$category->name = $this->categoryPrefix."CAT11";
		$category->parentId = $category1->id;
		$category11 = $this->categoryService->addAction(clone $category);
		
		$categoryResponse = $this->categoryService->listAction();
		$this->assertEquals($originalCount + 3, $categoryResponse->totalCount);
	}
}
?>
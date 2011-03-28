<?php

/**
 * annotation service base test case.
 */
abstract class AnnotationServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests annotation->listAction action
	 * @param KalturaAnnotationFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaAnnotationListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaAnnotationFilter $filter = null, KalturaFilterPager $pager = null, KalturaAnnotationListResponse $reference)
	{
		$resultObject = $this->client->annotation->listAction($filter, $pager);
		$this->assertType('KalturaAnnotationListResponse', $resultObject);
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaAnnotationFilter $filter = null, KalturaFilterPager $pager = null, KalturaAnnotationListResponse $reference)
	{
	}

	/**
	 * Tests annotation->add action
	 * @param KalturaAnnotation $annotation 
	 * @param KalturaAnnotation $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaAnnotation $annotation, KalturaAnnotation $reference)
	{
		$resultObject = $this->client->annotation->add($annotation);
		$this->assertType('KalturaAnnotation', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($annotation, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaAnnotation $annotation, KalturaAnnotation $reference)
	{
	}

	/**
	 * Tests annotation->get action
	 * @param string $id 
	 * @param KalturaAnnotation $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testGet($id, KalturaAnnotation $reference)
	{
		$resultObject = $this->client->annotation->get($id);
		$this->assertType('KalturaAnnotation', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateGet($id, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($id, KalturaAnnotation $reference)
	{
	}

	/**
	 * Tests annotation->delete action
	 * @param string $id 
	 * @dataProvider provideData
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->annotation->delete($id);
		$this->validateDelete($id);
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($id)
	{
	}

	/**
	 * Tests annotation->update action
	 * @param string $id 
	 * @param KalturaAnnotation $annotation 
	 * @param KalturaAnnotation $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testUpdate($id, KalturaAnnotation $annotation, KalturaAnnotation $reference)
	{
		$resultObject = $this->client->annotation->update($id, $annotation);
		$this->assertType('KalturaAnnotation', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateUpdate($id, $annotation, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($id, KalturaAnnotation $annotation, KalturaAnnotation $reference)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}

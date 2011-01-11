<?php

require_once(dirname(__FILE__) . '/../../../../../tests/base/bootstrap.php');
require_once(dirname(__FILE__) . '/GenericDistributionProviderActionServiceBaseTest.php');

/**
 * genericDistributionProviderAction service test case.
 */
class GenericDistributionProviderActionServiceTest extends GenericDistributionProviderActionServiceBaseTest
{
	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaGenericDistributionProviderAction $genericDistributionProviderAction, KalturaGenericDistributionProviderAction $reference)
	{
		parent::validateAdd($genericDistributionProviderAction, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Tests genericDistributionProviderAction->addMrssTransform action
	 * @param string $xslData
	 * @param KalturaGenericDistributionProviderAction $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testAddMrssTransform($xslData, KalturaGenericDistributionProviderAction $reference, $id)
	{
		$resultObject = $this->client->genericDistributionProviderAction->addMrssTransform($id, $xslData, $reference);
		$this->assertType('KalturaGenericDistributionProviderAction', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests genericDistributionProviderAction->addMrssTransformFromFile action
	 * @param file $xslFile
	 * @param KalturaGenericDistributionProviderAction $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testAddMrssTransformFromFile(file $xslFile, KalturaGenericDistributionProviderAction $reference, $id)
	{
		$resultObject = $this->client->genericDistributionProviderAction->addMrssTransformFromFile($id, $xslFile, $reference);
		$this->assertType('KalturaGenericDistributionProviderAction', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests genericDistributionProviderAction->addMrssValidate action
	 * @param string $xsdData
	 * @param KalturaGenericDistributionProviderAction $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testAddMrssValidate($xsdData, KalturaGenericDistributionProviderAction $reference, $id)
	{
		$resultObject = $this->client->genericDistributionProviderAction->addMrssValidate($id, $xsdData, $reference);
		$this->assertType('KalturaGenericDistributionProviderAction', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests genericDistributionProviderAction->addMrssValidateFromFile action
	 * @param file $xsdFile
	 * @param KalturaGenericDistributionProviderAction $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testAddMrssValidateFromFile(file $xsdFile, KalturaGenericDistributionProviderAction $reference, $id)
	{
		$resultObject = $this->client->genericDistributionProviderAction->addMrssValidateFromFile($id, $xsdFile, $reference);
		$this->assertType('KalturaGenericDistributionProviderAction', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests genericDistributionProviderAction->addResultsTransform action
	 * @param string $transformData
	 * @param KalturaGenericDistributionProviderAction $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testAddResultsTransform($transformData, KalturaGenericDistributionProviderAction $reference, $id)
	{
		$resultObject = $this->client->genericDistributionProviderAction->addResultsTransform($id, $transformData, $reference);
		$this->assertType('KalturaGenericDistributionProviderAction', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests genericDistributionProviderAction->addResultsTransformFromFile action
	 * @param file $transformFile
	 * @param KalturaGenericDistributionProviderAction $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testAddResultsTransformFromFile(file $transformFile, KalturaGenericDistributionProviderAction $reference, $id)
	{
		$resultObject = $this->client->genericDistributionProviderAction->addResultsTransformFromFile($id, $transformFile, $reference);
		$this->assertType('KalturaGenericDistributionProviderAction', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet(KalturaGenericDistributionProviderAction $reference, $id)
	{
		parent::validateGet($reference);
		// TODO - add your own validations here
	}

	/**
	 * Tests genericDistributionProviderAction->getByProviderId action
	 * @param int $genericDistributionProviderId
	 * @param KalturaDistributionAction $actionType
	 * @param KalturaGenericDistributionProviderAction $reference
	 * @dataProvider provideData
	 */
	public function testGetByProviderId($genericDistributionProviderId, $actionType, KalturaGenericDistributionProviderAction $reference)
	{
		$resultObject = $this->client->genericDistributionProviderAction->getByProviderId($genericDistributionProviderId, $actionType, $reference);
		$this->assertType('KalturaGenericDistributionProviderAction', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests genericDistributionProviderAction->updateByProviderId action
	 * @param int $genericDistributionProviderId
	 * @param KalturaDistributionAction $actionType
	 * @param KalturaGenericDistributionProviderAction $genericDistributionProviderAction
	 * @param KalturaGenericDistributionProviderAction $reference
	 * @dataProvider provideData
	 */
	public function testUpdateByProviderId($genericDistributionProviderId, $actionType, KalturaGenericDistributionProviderAction $genericDistributionProviderAction, KalturaGenericDistributionProviderAction $reference)
	{
		$resultObject = $this->client->genericDistributionProviderAction->updateByProviderId($genericDistributionProviderId, $actionType, $genericDistributionProviderAction, $reference);
		$this->assertType('KalturaGenericDistributionProviderAction', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate(KalturaGenericDistributionProviderAction $genericDistributionProviderAction, KalturaGenericDistributionProviderAction $reference, $id)
	{
		parent::validateUpdate($genericDistributionProviderAction, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($id)
	{
		parent::validateDelete();
		// TODO - add your own validations here
	}

	/**
	 * Tests genericDistributionProviderAction->deleteByProviderId action
	 * @param int $genericDistributionProviderId
	 * @param KalturaDistributionAction $actionType
	 * @dataProvider provideData
	 */
	public function testDeleteByProviderId($genericDistributionProviderId, $actionType)
	{
		$resultObject = $this->client->genericDistributionProviderAction->deleteByProviderId($genericDistributionProviderId, $actionType);
		// TODO - add here your own validations
	}

	/**
	 * Validates testList results
	 */
	protected function validateList(KalturaGenericDistributionProviderActionFilter $filter = null, KalturaFilterPager $pager = null, KalturaGenericDistributionProviderActionListResponse $reference)
	{
		parent::validateList($filter, $pager, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 * @depends testUpdate - TODO: replace testUpdate with last test function that uses that id
	 */
	public function testFinished($id)
	{
		return $id;
	}

}

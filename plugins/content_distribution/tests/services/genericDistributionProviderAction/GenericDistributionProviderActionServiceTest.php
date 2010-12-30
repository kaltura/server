<?php

require_once(dirname(__FILE__) . '/../../../../../tests/base/bootstrap.php');
require_once(dirname(__FILE__) . '/GenericDistributionProviderActionServiceBaseTest.php');

/**
 * genericDistributionProviderAction service test case.
 */
class GenericDistributionProviderActionServiceTest extends GenericDistributionProviderActionServiceBaseTest
{
	/**
	 * Tests genericDistributionProviderAction->addMrssTransform action
	 * @param string $xslData
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testAddMrssTransform($xslData, $id)
	{
		$resultObject = $this->client->genericDistributionProviderAction->addMrssTransform($id, $xslData);
		$this->assertType('KalturaGenericDistributionProviderAction', $resultObject);
	}

	/**
	 * Tests genericDistributionProviderAction->addMrssTransformFromFile action
	 * @param file $xslFile
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testAddMrssTransformFromFile(file $xslFile, $id)
	{
		$resultObject = $this->client->genericDistributionProviderAction->addMrssTransformFromFile($id, $xslFile);
		$this->assertType('KalturaGenericDistributionProviderAction', $resultObject);
	}

	/**
	 * Tests genericDistributionProviderAction->addMrssValidate action
	 * @param string $xsdData
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testAddMrssValidate($xsdData, $id)
	{
		$resultObject = $this->client->genericDistributionProviderAction->addMrssValidate($id, $xsdData);
		$this->assertType('KalturaGenericDistributionProviderAction', $resultObject);
	}

	/**
	 * Tests genericDistributionProviderAction->addMrssValidateFromFile action
	 * @param file $xsdFile
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testAddMrssValidateFromFile(file $xsdFile, $id)
	{
		$resultObject = $this->client->genericDistributionProviderAction->addMrssValidateFromFile($id, $xsdFile);
		$this->assertType('KalturaGenericDistributionProviderAction', $resultObject);
	}

	/**
	 * Tests genericDistributionProviderAction->addResultsTransform action
	 * @param string $transformData
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testAddResultsTransform($transformData, $id)
	{
		$resultObject = $this->client->genericDistributionProviderAction->addResultsTransform($id, $transformData);
		$this->assertType('KalturaGenericDistributionProviderAction', $resultObject);
	}

	/**
	 * Tests genericDistributionProviderAction->addResultsTransformFromFile action
	 * @param file $transformFile
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testAddResultsTransformFromFile(file $transformFile, $id)
	{
		$resultObject = $this->client->genericDistributionProviderAction->addResultsTransformFromFile($id, $transformFile);
		$this->assertType('KalturaGenericDistributionProviderAction', $resultObject);
	}

	/**
	 * Tests genericDistributionProviderAction->getByProviderId action
	 * @param int $genericDistributionProviderId
	 * @param KalturaDistributionAction $actionType
	 * @dataProvider provideData
	 */
	public function testGetByProviderId($genericDistributionProviderId, KalturaDistributionAction $actionType)
	{
		$resultObject = $this->client->genericDistributionProviderAction->getByProviderId($genericDistributionProviderId, $actionType);
		$this->assertType('KalturaGenericDistributionProviderAction', $resultObject);
	}

	/**
	 * Tests genericDistributionProviderAction->updateByProviderId action
	 * @param int $genericDistributionProviderId
	 * @param KalturaDistributionAction $actionType
	 * @param KalturaGenericDistributionProviderAction $genericDistributionProviderAction
	 * @dataProvider provideData
	 */
	public function testUpdateByProviderId($genericDistributionProviderId, KalturaDistributionAction $actionType, KalturaGenericDistributionProviderAction $genericDistributionProviderAction)
	{
		$resultObject = $this->client->genericDistributionProviderAction->updateByProviderId($genericDistributionProviderId, $actionType, $genericDistributionProviderAction);
		$this->assertType('KalturaGenericDistributionProviderAction', $resultObject);
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 * @depends testFunction - TODO: replace testFunction with last test function that uses that id
	 */
	public function testFinished($id)
	{
		return $id;
	}

	/**
	 * Tests genericDistributionProviderAction->deleteByProviderId action
	 * @param int $genericDistributionProviderId
	 * @param KalturaDistributionAction $actionType
	 * @dataProvider provideData
	 */
	public function testDeleteByProviderId($genericDistributionProviderId, KalturaDistributionAction $actionType)
	{
		$resultObject = $this->client->genericDistributionProviderAction->deleteByProviderId($genericDistributionProviderId, $actionType);
	}

}

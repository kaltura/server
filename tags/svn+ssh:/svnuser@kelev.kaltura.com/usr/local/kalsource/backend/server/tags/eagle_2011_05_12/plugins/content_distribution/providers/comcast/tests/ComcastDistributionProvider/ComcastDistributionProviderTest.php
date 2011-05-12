<?php

require_once(dirname(__FILE__) . '/../../../../../../tests/base/bootstrap.php');

/**
 * ComcastDistributionProvider test case.
 */
class ComcastDistributionProviderTest extends KalturaUnitTestCase
{
	
	/**
	 * @var ComcastDistributionProvider
	 */
	private $ComcastDistributionProvider;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->ComcastDistributionProvider = ComcastDistributionProvider::get();
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		$this->ComcastDistributionProvider = null;
		
		parent::tearDown();
	}
	
	/**
	 * Tests ComcastDistributionProvider::generateDeleteXML()
	 * @param string $entryId
	 * @param KalturaComcastDistributionJobProviderData $providerData
	 * @dataProvider provideData
	 */
	public function testGenerateDeleteXML($entryId, KalturaComcastDistributionJobProviderData $providerData)
	{
		$xml = ComcastDistributionProvider::generateDeleteXML($entryId, $providerData);
		KalturaLog::info($xml);
	}
	
	/**
	 * Tests ComcastDistributionProvider::generateUpdateXML()
	 * @param string $entryId
	 * @param KalturaMsnDistributionJobProviderData $providerData
	 * @dataProvider provideData
	 */
	public function testGenerateUpdateXML($entryId, KalturaComcastDistributionJobProviderData $providerData)
	{
		$xml = ComcastDistributionProvider::generateUpdateXML($entryId, $providerData);
		KalturaLog::info($xml);
	}
	
	/**
	 * Tests ComcastDistributionProvider::generateSubmitXML()
	 * @param string $entryId
	 * @param KalturaComcastDistributionJobProviderData $providerData
	 * @dataProvider provideData
	 */
	public function testGenerateSubmitXML($entryId, KalturaComcastDistributionJobProviderData $providerData)
	{
		$xml = ComcastDistributionProvider::generateSubmitXML($entryId, $providerData);
		KalturaLog::info($xml);
	}
}


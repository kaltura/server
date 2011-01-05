<?php

require_once(dirname(__FILE__) . '/../../../../tests/base/bootstrap.php');

/**
 * MsnDistributionProvider test case.
 */
class MsnDistributionProviderTest extends KalturaUnitTestCase
{
	
	/**
	 * @var MsnDistributionProvider
	 */
	private $MsnDistributionProvider;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->MsnDistributionProvider = MsnDistributionProvider::get();
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		$this->MsnDistributionProvider = null;
		
		parent::tearDown();
	}
	
	/**
	 * Tests MsnDistributionProvider::generateDeleteXML()
	 * @param string $entryId
	 * @param KalturaMsnDistributionJobProviderData $providerData
	 * @dataProvider provideData
	 */
	public function testGenerateDeleteXML($entryId, KalturaMsnDistributionJobProviderData $providerData)
	{
		MsnDistributionProvider::generateDeleteXML($entryId, $providerData);
	}
	
	/**
	 * Tests MsnDistributionProvider::generateUpdateXML()
	 * @param string $entryId
	 * @param KalturaMsnDistributionJobProviderData $providerData
	 * @dataProvider provideData
	 */
	public function testGenerateUpdateXML($entryId, KalturaMsnDistributionJobProviderData $providerData)
	{
		MsnDistributionProvider::generateUpdateXML($entryId, $providerData);
	}
	
	/**
	 * Tests MsnDistributionProvider::generateSubmitXML()
	 * @param string $entryId
	 * @param KalturaMsnDistributionJobProviderData $providerData
	 * @dataProvider provideData
	 */
	public function testGenerateSubmitXML($entryId, KalturaMsnDistributionJobProviderData $providerData)
	{
		MsnDistributionProvider::generateSubmitXML($entryId, $providerData);
	}
}


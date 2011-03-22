<?php

require_once(dirname(__FILE__) . '/../../../../../../tests/base/bootstrap.php');

/**
 * Youtube_apiDistributionProvider test case.
 */
class Youtube_apiDistributionProviderTest extends KalturaUnitTestCase
{
	
	/**
	 * @var Youtube_apiDistributionProvider
	 */
	private $Youtube_apiDistributionProvider;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->Youtube_apiDistributionProvider = Youtube_apiDistributionProvider::get();
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		$this->Youtube_apiDistributionProvider = null;
		
		parent::tearDown();
	}
	
	/**
	 * Tests Youtube_apiDistributionProvider::generateDeleteXML()
	 * @param string $entryId
	 * @param KalturaYoutube_apiDistributionJobProviderData $providerData
	 * @dataProvider provideData
	 */
	public function testGenerateDeleteXML($entryId, KalturaYoutube_apiDistributionJobProviderData $providerData)
	{
		$xml = Youtube_apiDistributionProvider::generateDeleteXML($entryId, $providerData);
		KalturaLog::info($xml);
	}
	
	/**
	 * Tests Youtube_apiDistributionProvider::generateUpdateXML()
	 * @param string $entryId
	 * @param KalturaMsnDistributionJobProviderData $providerData
	 * @dataProvider provideData
	 */
	public function testGenerateUpdateXML($entryId, KalturaYoutube_apiDistributionJobProviderData $providerData)
	{
		$xml = Youtube_apiDistributionProvider::generateUpdateXML($entryId, $providerData);
		KalturaLog::info($xml);
	}
	
	/**
	 * Tests Youtube_apiDistributionProvider::generateSubmitXML()
	 * @param string $entryId
	 * @param KalturaYoutube_apiDistributionJobProviderData $providerData
	 * @dataProvider provideData
	 */
	public function testGenerateSubmitXML($entryId, KalturaYoutube_apiDistributionJobProviderData $providerData)
	{
		$xml = Youtube_apiDistributionProvider::generateSubmitXML($entryId, $providerData);
		KalturaLog::info($xml);
	}
}


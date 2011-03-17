<?php

require_once(dirname(__FILE__) . '/../../../../../../tests/base/bootstrap.php');

/**
 * DailymotionDistributionProvider test case.
 */
class DailymotionDistributionProviderTest extends KalturaUnitTestCase
{
	
	/**
	 * @var DailymotionDistributionProvider
	 */
	private $DailymotionDistributionProvider;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->DailymotionDistributionProvider = DailymotionDistributionProvider::get();
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		$this->DailymotionDistributionProvider = null;
		
		parent::tearDown();
	}
	
	/**
	 * Tests DailymotionDistributionProvider::generateDeleteXML()
	 * @param string $entryId
	 * @param KalturaDailymotionDistributionJobProviderData $providerData
	 * @dataProvider provideData
	 */
	public function testGenerateDeleteXML($entryId, KalturaDailymotionDistributionJobProviderData $providerData)
	{
		$xml = DailymotionDistributionProvider::generateDeleteXML($entryId, $providerData);
		KalturaLog::info($xml);
	}
	
	/**
	 * Tests DailymotionDistributionProvider::generateUpdateXML()
	 * @param string $entryId
	 * @param KalturaMsnDistributionJobProviderData $providerData
	 * @dataProvider provideData
	 */
	public function testGenerateUpdateXML($entryId, KalturaDailymotionDistributionJobProviderData $providerData)
	{
		$xml = DailymotionDistributionProvider::generateUpdateXML($entryId, $providerData);
		KalturaLog::info($xml);
	}
	
	/**
	 * Tests DailymotionDistributionProvider::generateSubmitXML()
	 * @param string $entryId
	 * @param KalturaDailymotionDistributionJobProviderData $providerData
	 * @dataProvider provideData
	 */
	public function testGenerateSubmitXML($entryId, KalturaDailymotionDistributionJobProviderData $providerData)
	{
		$xml = DailymotionDistributionProvider::generateSubmitXML($entryId, $providerData);
		KalturaLog::info($xml);
	}
}


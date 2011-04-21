<?php

require_once(dirname(__FILE__) . '/../../../../../../tests/base/bootstrap.php');

/**
 * YoutubeApiDistributionProvider test case.
 */
class YoutubeApiDistributionProviderTest extends KalturaUnitTestCase
{
	
	/**
	 * @var YoutubeApiDistributionProvider
	 */
	private $YoutubeApiDistributionProvider;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->YoutubeApiDistributionProvider = YoutubeApiDistributionProvider::get();
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		$this->YoutubeApiDistributionProvider = null;
		
		parent::tearDown();
	}
	
	/**
	 * Tests YoutubeApiDistributionProvider::generateDeleteXML()
	 * @param string $entryId
	 * @param KalturaYoutubeApiDistributionJobProviderData $providerData
	 * @dataProvider provideData
	 */
	public function testGenerateDeleteXML($entryId, KalturaYoutubeApiDistributionJobProviderData $providerData)
	{
		$xml = YoutubeApiDistributionProvider::generateDeleteXML($entryId, $providerData);
		KalturaLog::info($xml);
	}
	
	/**
	 * Tests YoutubeApiDistributionProvider::generateUpdateXML()
	 * @param string $entryId
	 * @param KalturaMsnDistributionJobProviderData $providerData
	 * @dataProvider provideData
	 */
	public function testGenerateUpdateXML($entryId, KalturaYoutubeApiDistributionJobProviderData $providerData)
	{
		$xml = YoutubeApiDistributionProvider::generateUpdateXML($entryId, $providerData);
		KalturaLog::info($xml);
	}
	
	/**
	 * Tests YoutubeApiDistributionProvider::generateSubmitXML()
	 * @param string $entryId
	 * @param KalturaYoutubeApiDistributionJobProviderData $providerData
	 * @dataProvider provideData
	 */
	public function testGenerateSubmitXML($entryId, KalturaYoutubeApiDistributionJobProviderData $providerData)
	{
		$xml = YoutubeApiDistributionProvider::generateSubmitXML($entryId, $providerData);
		KalturaLog::info($xml);
	}
}


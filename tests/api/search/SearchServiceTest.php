<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/SearchServiceBaseTest.php');

/**
 * search service test case.
 */
class SearchServiceTest extends SearchServiceBaseTest
{
	/**
	 * Tests search->search action
	 * @param KalturaSearch $search
	 * @param KalturaFilterPager $pager
	 * @dataProvider provideData
	 */
	public function testSearch(KalturaSearch $search, KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->search->search($search, $pager);
		$this->assertType('KalturaSearchResultResponse', $resultObject);
	}

	/**
	 * Tests search->getMediaInfo action
	 * @param KalturaSearchResult $searchResult
	 * @dataProvider provideData
	 */
	public function testGetMediaInfo(KalturaSearchResult $searchResult)
	{
		$resultObject = $this->client->search->getMediaInfo($searchResult);
		$this->assertType('KalturaSearchResult', $resultObject);
	}

	/**
	 * Tests search->searchUrl action
	 * @param KalturaMediaType $mediaType
	 * @param string $url
	 * @dataProvider provideData
	 */
	public function testSearchUrl(KalturaMediaType $mediaType, $url)
	{
		$resultObject = $this->client->search->searchUrl($mediaType, $url);
		$this->assertType('KalturaSearchResult', $resultObject);
	}

	/**
	 * Tests search->externalLogin action
	 * @param KalturaSearchProviderType $searchSource
	 * @param string $userName
	 * @param string $password
	 * @dataProvider provideData
	 */
	public function testExternalLogin(KalturaSearchProviderType $searchSource, $userName, $password)
	{
		$resultObject = $this->client->search->externalLogin($searchSource, $userName, $password);
		$this->assertType('KalturaSearchAuthData', $resultObject);
	}

}

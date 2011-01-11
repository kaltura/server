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
	 * @param KalturaSearchResultResponse $reference
	 * @dataProvider provideData
	 */
	public function testSearch(KalturaSearch $search, KalturaFilterPager $pager = null, KalturaSearchResultResponse $reference)
	{
		$resultObject = $this->client->search->search($search, $pager, $reference);
		$this->assertType('KalturaSearchResultResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests search->getMediaInfo action
	 * @param KalturaSearchResult $searchResult
	 * @param KalturaSearchResult $reference
	 * @dataProvider provideData
	 */
	public function testGetMediaInfo(KalturaSearchResult $searchResult, KalturaSearchResult $reference)
	{
		$resultObject = $this->client->search->getMediaInfo($searchResult, $reference);
		$this->assertType('KalturaSearchResult', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests search->searchUrl action
	 * @param KalturaMediaType $mediaType
	 * @param string $url
	 * @param KalturaSearchResult $reference
	 * @dataProvider provideData
	 */
	public function testSearchUrl($mediaType, $url, KalturaSearchResult $reference)
	{
		$resultObject = $this->client->search->searchUrl($mediaType, $url, $reference);
		$this->assertType('KalturaSearchResult', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests search->externalLogin action
	 * @param KalturaSearchProviderType $searchSource
	 * @param string $userName
	 * @param string $password
	 * @param KalturaSearchAuthData $reference
	 * @dataProvider provideData
	 */
	public function testExternalLogin($searchSource, $userName, $password, KalturaSearchAuthData $reference)
	{
		$resultObject = $this->client->search->externalLogin($searchSource, $userName, $password, $reference);
		$this->assertType('KalturaSearchAuthData', $resultObject);
		// TODO - add here your own validations
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

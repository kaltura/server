<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/PlaylistServiceBaseTest.php');

/**
 * playlist service test case.
 */
class PlaylistServiceTest extends PlaylistServiceBaseTest
{
	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaPlaylist $playlist, $updateStats = null, KalturaPlaylist $reference)
	{
		parent::validateAdd($playlist, $updateStats, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($id, $version = -1, KalturaPlaylist $reference)
	{
		parent::validateGet($id, $version, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($id, KalturaPlaylist $playlist, $updateStats = null, KalturaPlaylist $reference)
	{
		parent::validateUpdate($id, $playlist, $updateStats, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($id)
	{
		parent::validateDelete($id);
		// TODO - add your own validations here
	}

	/**
	 * Tests playlist->clone action
	 * @param string $id
	 * @param KalturaPlaylist $newPlaylist
	 * @param KalturaPlaylist $reference
	 * @dataProvider provideData
	 */
	public function testClone($id, KalturaPlaylist $newPlaylist = null, KalturaPlaylist $reference)
	{
		$resultObject = $this->client->playlist->clone($id, $newPlaylist, $reference);
		$this->assertType('KalturaPlaylist', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Validates testList results
	 */
	protected function validateList(KalturaPlaylistFilter $filter = null, KalturaFilterPager $pager = null, KalturaPlaylistListResponse $reference)
	{
		parent::validateList($filter, $pager, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Tests playlist->execute action
	 * @param string $id
	 * @param string $detailed
	 * @param KalturaBaseEntryArray $reference
	 * @dataProvider provideData
	 */
	public function testExecute($id, $detailed = null, KalturaBaseEntryArray $reference)
	{
		$resultObject = $this->client->playlist->execute($id, $detailed, $reference);
		$this->assertType('KalturaBaseEntryArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests playlist->executeFromContent action
	 * @param KalturaPlaylistType $playlistType
	 * @param string $playlistContent
	 * @param string $detailed
	 * @param KalturaBaseEntryArray $reference
	 * @dataProvider provideData
	 */
	public function testExecuteFromContent($playlistType, $playlistContent, $detailed = null, KalturaBaseEntryArray $reference)
	{
		$resultObject = $this->client->playlist->executeFromContent($playlistType, $playlistContent, $detailed, $reference);
		$this->assertType('KalturaBaseEntryArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests playlist->executeFromFilters action
	 * @param KalturaMediaEntryFilterForPlaylistArray $filters
	 * @param int $totalResults
	 * @param string $detailed
	 * @param KalturaBaseEntryArray $reference
	 * @dataProvider provideData
	 */
	public function testExecuteFromFilters(KalturaMediaEntryFilterForPlaylistArray $filters, $totalResults, $detailed = null, KalturaBaseEntryArray $reference)
	{
		$resultObject = $this->client->playlist->executeFromFilters($filters, $totalResults, $detailed, $reference);
		$this->assertType('KalturaBaseEntryArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests playlist->getStatsFromContent action
	 * @param KalturaPlaylistType $playlistType
	 * @param string $playlistContent
	 * @param KalturaPlaylist $reference
	 * @dataProvider provideData
	 */
	public function testGetStatsFromContent($playlistType, $playlistContent, KalturaPlaylist $reference)
	{
		$resultObject = $this->client->playlist->getStatsFromContent($playlistType, $playlistContent, $reference);
		$this->assertType('KalturaPlaylist', $resultObject);
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

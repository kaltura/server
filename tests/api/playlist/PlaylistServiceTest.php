<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/PlaylistServiceBaseTest.php');

/**
 * playlist service test case.
 */
class PlaylistServiceTest extends PlaylistServiceBaseTest
{
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
	 * Tests playlist->clone action
	 * @param string $id
	 * @param KalturaPlaylist $newPlaylist
	 * @dataProvider provideData
	 */
	public function testClone($id, KalturaPlaylist $newPlaylist = null)
	{
		$resultObject = $this->client->playlist->clone($id, $newPlaylist);
		$this->assertType('KalturaPlaylist', $resultObject);
	}

	/**
	 * Tests playlist->execute action
	 * @param string $id
	 * @param string $detailed
	 * @dataProvider provideData
	 */
	public function testExecute($id, $detailed = null)
	{
		$resultObject = $this->client->playlist->execute($id, $detailed);
		$this->assertType('KalturaBaseEntryArray', $resultObject);
	}

	/**
	 * Tests playlist->executeFromContent action
	 * @param KalturaPlaylistType $playlistType
	 * @param string $playlistContent
	 * @param string $detailed
	 * @dataProvider provideData
	 */
	public function testExecuteFromContent(KalturaPlaylistType $playlistType, $playlistContent, $detailed = null)
	{
		$resultObject = $this->client->playlist->executeFromContent($playlistType, $playlistContent, $detailed);
		$this->assertType('KalturaBaseEntryArray', $resultObject);
	}

	/**
	 * Tests playlist->executeFromFilters action
	 * @param KalturaMediaEntryFilterForPlaylistArray $filters
	 * @param int $totalResults
	 * @param string $detailed
	 * @dataProvider provideData
	 */
	public function testExecuteFromFilters(KalturaMediaEntryFilterForPlaylistArray $filters, $totalResults, $detailed = null)
	{
		$resultObject = $this->client->playlist->executeFromFilters($filters, $totalResults, $detailed);
		$this->assertType('KalturaBaseEntryArray', $resultObject);
	}

	/**
	 * Tests playlist->getStatsFromContent action
	 * @param KalturaPlaylistType $playlistType
	 * @param string $playlistContent
	 * @dataProvider provideData
	 */
	public function testGetStatsFromContent(KalturaPlaylistType $playlistType, $playlistContent)
	{
		$resultObject = $this->client->playlist->getStatsFromContent($playlistType, $playlistContent);
		$this->assertType('KalturaPlaylist', $resultObject);
	}

}

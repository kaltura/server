<?php

/**
 * playlist service base test case.
 */
abstract class PlaylistServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests playlist->add action
	 * @param KalturaPlaylist $playlist
	 * @param bool $updateStats
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaPlaylist $playlist, $updateStats = null)
	{
		$resultObject = $this->client->playlist->add($playlist, $updateStats);
		$this->assertType('KalturaPlaylist', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests playlist->get action
	 * @param string $id
	 * @param int $version
	 * @return int
	 * @depends testAdd with data set #0
	 */
	public function testGet($id, $version = -1)
	{
		$resultObject = $this->client->playlist->get($id, $version);
		$this->assertType('KalturaPlaylist', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests playlist->update action
	 * @param string $id
	 * @param KalturaPlaylist $playlist
	 * @param bool $updateStats
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate($id, KalturaPlaylist $playlist, $updateStats = null)
	{
		$resultObject = $this->client->playlist->update($id, $playlist, $updateStats);
		$this->assertType('KalturaPlaylist', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

	/**
	 * Tests playlist->delete action
	 * @param string $id
	 * @return int
	 * @depends testFinished
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->playlist->delete($id);
	}

	/**
	 * Tests playlist->list action
	 * @param KalturaPlaylistFilter $filter
	 * @param KalturaFilterPager $pager
	 * @dataProvider provideData
	 */
	public function testList(KalturaPlaylistFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->playlist->listAction($filter, $pager);
		$this->assertType('KalturaPlaylistListResponse', $resultObject);
		$this->assertNotEquals($resultObject->totalCount, 0);
	}

}

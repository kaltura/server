<?php

/**
 * playlist service base test case.
 */
abstract class PlaylistServiceBaseTest extends KalturaApiTestCase
{
	/**
	 * Tests playlist->add action
	 * @param KalturaPlaylist $playlist 
	 * @param bool $updateStats 
	 * @param KalturaPlaylist $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaPlaylist $playlist, $updateStats = null, KalturaPlaylist $reference)
	{
		$resultObject = $this->client->playlist->add($playlist, $updateStats);
		$this->assertType('KalturaPlaylist', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($playlist, $updateStats, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaPlaylist $playlist, $updateStats = null, KalturaPlaylist $reference)
	{
	}

	/**
	 * Tests playlist->get action
	 * @param string $id 
	 * @param int $version Desired version of the data
	 * @param KalturaPlaylist $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testGet($id, $version = -1, KalturaPlaylist $reference)
	{
		$resultObject = $this->client->playlist->get($id, $version);
		$this->assertType('KalturaPlaylist', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateGet($id, $version, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($id, $version = -1, KalturaPlaylist $reference)
	{
	}

	/**
	 * Tests playlist->update action
	 * @param string $id 
	 * @param KalturaPlaylist $playlist 
	 * @param bool $updateStats  
	 * @param KalturaPlaylist $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testUpdate($id, KalturaPlaylist $playlist, $updateStats = null, KalturaPlaylist $reference)
	{
		$resultObject = $this->client->playlist->update($id, $playlist, $updateStats);
		$this->assertType('KalturaPlaylist', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateUpdate($id, $playlist, $updateStats, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($id, KalturaPlaylist $playlist, $updateStats = null, KalturaPlaylist $reference)
	{
	}

	/**
	 * Tests playlist->delete action
	 * @param string $id 
	 * @dataProvider provideData
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->playlist->delete($id);
		$this->validateDelete($id);
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($id)
	{
	}

	/**
	 * Tests playlist->listAction action
	 * @param KalturaPlaylistFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaPlaylistListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaPlaylistFilter $filter = null, KalturaFilterPager $pager = null, KalturaPlaylistListResponse $reference)
	{
		$resultObject = $this->client->playlist->listAction($filter, $pager);
		$this->assertType('KalturaPlaylistListResponse', $resultObject);
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaPlaylistFilter $filter = null, KalturaFilterPager $pager = null, KalturaPlaylistListResponse $reference)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}

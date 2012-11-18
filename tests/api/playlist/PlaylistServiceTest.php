<?php

require_once(dirname(__FILE__) . '/../../bootstrap.php');

/**
 * playlist service test case.
 */
class PlaylistServiceTest extends PlaylistServiceTestBase
{
	/**
	 * Test update of a playlist with new parameters
	 * @param int $id
	 * @param KalturaPlaylist $playlist
	 * @dataProvider provideData
	 */
	public function testUpdateAction ($id, KalturaPlaylist $playlist, KalturaPlaylist $expectedResult)
	{
		try 
		{
			$result = $this->client->playlist->update($id, $playlist);	
		}
		catch (Exception $e)
		{
			$this->assertEquals(0, 1, $e->getMessage());
		}
		
		$this->assertAPIObjects($expectedResult, $result);
		
	}

}


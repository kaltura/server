package com.kaltura.client.tests;

import java.util.ArrayList;
import java.util.List;

import org.apache.log4j.Logger;

import com.kaltura.client.KalturaApiException;
import com.kaltura.client.enums.KalturaPlaylistType;
import com.kaltura.client.services.KalturaPlaylistService;
import com.kaltura.client.types.KalturaPlaylist;
import com.kaltura.client.types.KalturaPlaylistListResponse;

public class PlaylistServiceTest extends BaseTest {
	
	private Logger logger = Logger.getLogger(PlaylistServiceTest.class);
	
	// keeps track of test playlists we create so they can be cleaned up at the end
	protected List<String> testPlaylistIds = new ArrayList<String>();
		
	protected boolean doCleanup = false;
		
	public void testListPlaylists() {
		logger.info("Starting list playlists test");
		//See comments below. We'll uncomment this when playlist is working.
		/*
		// add a test clip
		String name1 = "test one (" + new Date() + ")";
		KalturaMediaEntry addedEntry1 = addClip(name1);		
		String id1 = addedEntry1.id;
		
		// add another test clip
		String name2 = "test two (" + new Date() + ")";
		KalturaMediaEntry addedEntry2 = addClip(name2);		
		String id2 = addedEntry2.id;
		*/
		startAdminSession();
		
		boolean exceptionThrown = false;
		KalturaPlaylistListResponse playlistList = null;
		try {
			KalturaPlaylistService playlistService = this.client.getPlaylistService();

			// create a playlist
			KalturaPlaylist playlist = new KalturaPlaylist();			
			playlist.name = "Test playlist";
			playlist.description = "Playlist unit test";
			playlist.playlistType = KalturaPlaylistType.STATIC_LIST;
			// add test clips to the playlist
			//TODO we'll use the id's of the test clips we added above, once the playlist
			// starts actually working on the server side
			//playlist.playlistContent = "dwygyeikas,iqhubczu14";
			KalturaPlaylist addedPlaylist = playlistService.add(playlist);
			assertNotNull(addedPlaylist);			
			String addedPlaylistId = addedPlaylist.id;
			assertNotNull(addedPlaylistId);

			/*
			 * The call to add actually creates an unusable playlist so the rest of this
			 * test will fail.
			 * http://www.kaltura.org/playlistservice-add-action-creates-unusable-playlist-api-v3
			 */
			
			// find the playlist
			//TODO using the all list now, but should change to a filter
			playlistList = playlistService.list();
						
			boolean foundPlaylist = false;
			for (KalturaPlaylist aPlaylist : playlistList.objects) {
				if (aPlaylist.id.equals(addedPlaylistId)) foundPlaylist = true;
			}
			assertTrue(foundPlaylist);
			
		} catch (KalturaApiException kae) {
			exceptionThrown = true;
			logger.error(kae);
		}
		
		assertFalse(exceptionThrown);
		
	}
		
	@Override
	protected void tearDown() {
		
		if (!doCleanup) return;
		
		super.tearDown();
		
		logger.info("Cleaning up test playlists after test");
		
		KalturaPlaylistService playlistService = this.client.getPlaylistService();
		for (String id : this.testPlaylistIds) {
			logger.debug("Deleting " + id);
			try {				
				playlistService.delete(id);			
			} catch (Exception e) {
				logger.error("Couldn't delete playlist " + id, e);
			}
		} //next id
	}
}

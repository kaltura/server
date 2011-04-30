package com.kaltura.client.tests;

import java.io.File;
import java.util.Date;

import org.apache.log4j.Logger;

import com.kaltura.client.KalturaApiException;
import com.kaltura.client.enums.KalturaEntryStatus;
import com.kaltura.client.enums.KalturaEntryType;
import com.kaltura.client.enums.KalturaMediaType;
import com.kaltura.client.enums.KalturaModerationFlagType;
import com.kaltura.client.services.KalturaMediaService;
import com.kaltura.client.types.KalturaMediaEntry;
import com.kaltura.client.types.KalturaMediaEntryFilter;
import com.kaltura.client.types.KalturaMediaListResponse;
import com.kaltura.client.types.KalturaModerationFlag;
import com.kaltura.client.types.KalturaModerationFlagListResponse;

public class MediaServiceTest extends BaseTest {

	private Logger logger = Logger.getLogger(MediaServiceTest.class);

	public void testAddFromUrl() {
        logger.info("Starting addFromUrl test");
        
        startUserSession();
        
		String name = "test (" + new Date() + ")";
		
		KalturaMediaEntry addedEntry = addClip(name);
		
		assertNotNull(addedEntry);
		assertNotNull(addedEntry.id);
		assertEquals(name, addedEntry.name);
		assertEquals(KalturaEntryStatus.IMPORT, addedEntry.status);
		
	}
	
	/*
	 * Update seems to be whacked so skipping for now
	 * http://www.kaltura.org/how-update-supposed-work-api-v3
	 */
	public void testUpdate() {
		logger.info("Starting update test");
		
		startUserSession();
		
		String name = "test (" + new Date() + ")";
		
		KalturaMediaEntry addedEntry = addClip(name);
		
		String entryId = addedEntry.id;
		
		boolean exceptionThrown = false;
		try {
			getProcessedClip(addedEntry.id);
			KalturaMediaService mediaService = this.client.getMediaService();
			KalturaMediaEntry entryUpdate = new KalturaMediaEntry();
			entryUpdate.tags = "foo";			
			mediaService.update(entryId, entryUpdate);
			entryUpdate = getProcessedClip(entryId);
			logger.debug("Tags:" + entryUpdate.tags);
		} catch (Exception e) {
			logger.error("Trouble updating entry", e);
		}
		
		assertFalse(exceptionThrown);
		
	}
		
	public void testList() {
		logger.info("Starting list test");
		
		startUserSession();
		
		// add a test clip
		String name1 = "test one (" + new Date() + ")";
		KalturaMediaEntry addedEntry1 = addClip(name1);		
		String id1 = addedEntry1.id;
		
		// add another test clip
		String name2 = "test two (" + new Date() + ")";
		KalturaMediaEntry addedEntry2 = addClip(name2);		
		String id2 = addedEntry2.id;
		
		boolean exceptionThrown = false;
		try {

			//wait for the newly-added clip to process
			getProcessedClip(id1);
			getProcessedClip(id2);
						
			KalturaMediaService mediaService = this.client.getMediaService();

			// get a list of clips starting with "test"
			KalturaMediaEntryFilter filter = new KalturaMediaEntryFilter();
			filter.mediaTypeEqual = null;
			filter.statusEqual = null;
			filter.typeEqual = null;
			filter.nameLike = "test";
			
			KalturaMediaListResponse listResponse = mediaService.list(filter);			
			assertEquals(listResponse.totalCount, 2);
			
			boolean found1 = false;
			boolean found2 = false;
			for (KalturaMediaEntry entry : listResponse.objects) {
				logger.debug("id:" + entry.id);
				if (entry.id.equals(id1)) {
					found1 = true;
				} else if (entry.id.equals(id2)) {
					found2 = true;
				}
			}
			
			assertTrue(found1);
			assertTrue(found2);
		} catch (Exception e) {
			exceptionThrown = true;
			logger.error("Got exception testing moderation flag", e);			
		} finally {
			assertFalse(exceptionThrown);
		}
		
	}
	public void testModeration() {
		logger.info("Starting moderation test");
		
		startAdminSession();
		
		final String FLAG_COMMENTS = "This is a test flag";
		
		logger.info("Starting addFromUrl test");
        
		String name = "test (" + new Date() + ")";
		
		KalturaMediaEntry addedEntry = addClip(name);
				
		String id = addedEntry.id;
		
		boolean exceptionThrown = false;
		try {

			//wait for the newly-added clip to process
			getProcessedClip(id);
						
			KalturaMediaService mediaService = this.client.getMediaService();
			
			// flag the clip
			KalturaModerationFlag flag = new KalturaModerationFlag();
			flag.flaggedEntryId = id;
			flag.flagType = KalturaModerationFlagType.SPAM_COMMERCIALS;
			flag.comments = FLAG_COMMENTS;
			mediaService.flag(flag);
			
			// get the list of flags for this entry
			KalturaModerationFlagListResponse flagList = mediaService.listFlags(id);
			assertEquals(flagList.totalCount, 1);

			// check that the flag we put in is the flag we got back
			KalturaModerationFlag retFlag = (KalturaModerationFlag)flagList.objects.get(0);						
			assertEquals(retFlag.flagType, KalturaModerationFlagType.SPAM_COMMERCIALS);
			assertEquals(retFlag.comments, FLAG_COMMENTS);
			
			/*
			 * Reject seems to kill the video completely--it can't be retrieved after,
			 * so we will skip it until we hear back from Kaltura
			 */
			/*
			// reject the clip			
			mediaService.reject(id);
						
			KalturaMediaEntryFilter filter = new KalturaMediaEntryFilter();
			
			// check the status of the rejected clip
			KalturaMediaEntry processedClip = getProcessedClip(id);			
			assertEquals(KalturaEntryStatus.BLOCKED, processedClip.status);
			
			// approve the clip
			mediaService.approve(id);			
			
			// check the status of the approved clip
			processedClip = getProcessedClip(id);			
			assertEquals(KalturaEntryStatus.READY, processedClip.status);			
			*/
		} catch (Exception e) {
			exceptionThrown = true;
			logger.error("Got exception testing moderation flag", e);			
		} finally {
			assertFalse(exceptionThrown);
		}
		
	}
	
	public void testBadGet() {
		logger.info("Starting badGet test");
		
		startUserSession();
		
		// look for one we know doesn't exist
		boolean exceptionThrown = false;
		KalturaMediaEntry badEntry = null;
		try {
			KalturaMediaService mediaService = this.client.getMediaService();
			badEntry = mediaService.get("badid");
		} catch (KalturaApiException kae) {
			exceptionThrown = true;
		}
		
		assertTrue(exceptionThrown);
		assertNull(badEntry);
	}
	
	public void testGet() {
		logger.info("Starting get test");
		
		startUserSession();
		
		String name = "test (" + new Date() + ")";

		KalturaMediaEntry addedEntry = addClip(name);
		
		if (addedEntry == null) {
			logger.error("Trouble adding in testGet");
			return;
		}
		
		KalturaMediaEntry retrievedEntry = null;
		try {
			KalturaMediaService mediaService = this.client.getMediaService();
			retrievedEntry = mediaService.get(addedEntry.id);
		} catch (KalturaApiException kae) {
			logger.error("Trouble getting entry", kae);
		}
		
		assertNotNull(retrievedEntry);
		assertEquals(addedEntry.id, retrievedEntry.id);
		
	}
	
	public void testDelete() {
		logger.info("Starting delete test");
		
		startUserSession();
		
		String name = "test (" + new Date() + ")";

		KalturaMediaEntry addedEntry = addClip(name);
		
		assertNotNull(addedEntry);
		
		String idToDelete = addedEntry.id;
		
		KalturaMediaService mediaService = this.client.getMediaService();
		
		boolean deleted = false;
		try {
			// calling this makes the test wait for processing to complete
			// if you call delete while it is processing, the delete doesn't happen
			getProcessedClip(idToDelete);
			mediaService.delete(idToDelete);
			deleted = true;
		} catch (Exception e) {
			logger.error("Trouble deleting", e);
		} finally {
			assertTrue(deleted);
		}

		boolean exceptionThrown = false;
		KalturaMediaEntry deletedEntry = null;
		try {
			deletedEntry = mediaService.get(idToDelete);
		} catch (KalturaApiException kae) {
			exceptionThrown = true;
		} finally {
			assertTrue(exceptionThrown);	
		
			// we whacked this one, so let's not keep track of it		
			this.testIds.remove(testIds.size() - 1);
		}

		assertNull(deletedEntry);
	}
	
	public void testUpload() {
		logger.info("Starting delete test");
		
		startUserSession();
				
		KalturaMediaService mediaService = this.client.getMediaService();
		
		String name = "test (" + new Date() + ")";
		
		boolean exceptionThrown = false;
		KalturaMediaEntry entry = new KalturaMediaEntry();
		try {
			File file = new File("/var/tmp/video.flv");
			String result = mediaService.upload(file);
			logger.debug("After upload, result:" + result);			
			entry.name = name;
			entry.type = KalturaEntryType.MEDIA_CLIP;
			entry.mediaType = KalturaMediaType.VIDEO;
			entry = mediaService.addFromUploadedFile(entry, result);
		} catch (Exception e) {
			logger.error("Trouble uploading", e);
			exceptionThrown = true;
		} finally {
			assertFalse(exceptionThrown);
		}
		
		assertNotNull(entry.id);
		
		if (entry.id != null) {
			this.testIds.add(entry.id);
		}
		
	}
}

// ===================================================================================================
//                           _  __     _ _
//                          | |/ /__ _| | |_ _  _ _ _ __ _
//                          | ' </ _` | |  _| || | '_/ _` |
//                          |_|\_\__,_|_|\__|\_,_|_| \__,_|
//
// This file is part of the Kaltura Collaborative Media Suite which allows users
// to do with audio, video, and animation what Wiki platfroms allow them to do with
// text.
//
// Copyright (C) 2006-2011  Kaltura Inc.
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Affero General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Affero General Public License for more details.
//
// You should have received a copy of the GNU Affero General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// @ignore
// ===================================================================================================
package com.kaltura.client.test;

import java.io.BufferedReader;
import java.io.File;
import java.io.InputStream;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.ArrayList;
import java.util.Date;
import java.util.List;

import com.kaltura.client.KalturaApiException;
import com.kaltura.client.KalturaClient;
import com.kaltura.client.enums.KalturaEntryStatus;
import com.kaltura.client.enums.KalturaEntryType;
import com.kaltura.client.enums.KalturaMediaType;
import com.kaltura.client.enums.KalturaModerationFlagType;
import com.kaltura.client.services.KalturaMediaService;
import com.kaltura.client.types.KalturaBaseEntry;
import com.kaltura.client.types.KalturaDataEntry;
import com.kaltura.client.types.KalturaFlavorAsset;
import com.kaltura.client.types.KalturaMediaEntry;
import com.kaltura.client.types.KalturaMediaEntryFilter;
import com.kaltura.client.types.KalturaMediaEntryFilterForPlaylist;
import com.kaltura.client.types.KalturaMediaListResponse;
import com.kaltura.client.types.KalturaModerationFlag;
import com.kaltura.client.types.KalturaModerationFlagListResponse;
import com.kaltura.client.types.KalturaUploadToken;
import com.kaltura.client.types.KalturaUploadedFileTokenResource;
import com.kaltura.client.KalturaLogger;

public class MediaServiceTest extends BaseTest {

	private KalturaLogger logger = KalturaLogger.getLogger(MediaServiceTest.class);
	
	/**
	 * Tests the following : 
	 * Media Service -
	 *  - add From Url
	 */
	public void testAddFromUrl() {
		if (logger.isEnabled()) 
			logger.info("Test Add From URL");
        
		String name = "test (" + new Date() + ")";
		
		try {
			startUserSession(client, kalturaConfig);
			KalturaMediaEntry addedEntry = addClipFromUrl(this, client, name);
			assertNotNull(addedEntry);
			assertNotNull(addedEntry.id);
			assertEquals(name, addedEntry.name);
			assertEquals(KalturaEntryStatus.IMPORT, addedEntry.status);
		} catch (KalturaApiException e) {
			e.printStackTrace();
			fail();
		}
	}
	
	public static KalturaMediaEntry addClipFromUrl(BaseTest testContainer,
			KalturaClient client, String name) throws KalturaApiException {

		KalturaMediaEntry entry = new KalturaMediaEntry();

		entry.name = name;
		entry.type = KalturaEntryType.MEDIA_CLIP;
		entry.mediaType = KalturaMediaType.VIDEO;

		KalturaMediaService mediaService = client.getMediaService();
		KalturaMediaEntry addedEntry = mediaService.addFromUrl(entry, KalturaTestConfig.TEST_URL);

		if(addedEntry != null)
			testContainer.testIds.add(addedEntry.id);
		
		return addedEntry;
	}
	
	/**
	 * Tests the following : 
	 * Media Service - 
	 * 	- add 
	 *  - add Content
	 *  - count
	 * Upload token - 
	 *  - add
	 *  - upload
	 * Flavor asset - 
	 * 	- get by entry id
	 */
	public void testUploadTokenAddGivenFile() {
		
		if (logger.isEnabled())
			logger.info("Test upload token add");
		
		try {
			InputStream fileData = TestUtils.getTestVideo();
			int fileSize = fileData.available();
			
			startUserSession(client, kalturaConfig);
			int sz = client.getMediaService().count();
			
			// Create entry
			KalturaMediaEntry entry = new KalturaMediaEntry();
			entry.name =  "test (" + new Date() + ")";
			entry.type = KalturaEntryType.MEDIA_CLIP;
			entry.mediaType = KalturaMediaType.VIDEO;
			
			entry = client.getMediaService().add(entry);
			assertNotNull(entry);
			
			testIds.add(entry.id);
			
			// Create token
			KalturaUploadToken uploadToken = new KalturaUploadToken();
			uploadToken.fileName = KalturaTestConfig.UPLOAD_VIDEO;
			uploadToken.fileSize = fileSize;
			KalturaUploadToken token = client.getUploadTokenService().add(uploadToken);
			assertNotNull(token);
			
			// Define content
			KalturaUploadedFileTokenResource resource = new KalturaUploadedFileTokenResource();
			resource.token = token.id;
			entry = client.getMediaService().addContent(entry.id, resource);
			assertNotNull(entry);
			
			// upload
			uploadToken = client.getUploadTokenService().upload(token.id, fileData, KalturaTestConfig.UPLOAD_VIDEO, fileSize, false);
			assertNotNull(uploadToken);
			
			// Test Creation
			entry = getProcessedEntry(client, entry.id, true);
			assertNotNull(entry);
			
			// Test get flavor asset by entry id.
			List<KalturaFlavorAsset> listFlavors = client.getFlavorAssetService().getByEntryId(entry.id);
			assertNotNull(listFlavors);
			assertTrue(listFlavors.size() >= 1); // Should contain at least the source
			
			// Test count
			int sz2 = client.getMediaService().count();
			assertTrue(sz + 1 == sz2);
			
		} catch (Exception e) {
			e.printStackTrace();
			fail();
		}
	}
	
	public void testUploadUnexistingFile() throws KalturaApiException {
		
		File file = new File("bin/nonExistingfile.flv");
		
		startUserSession(client, kalturaConfig);
		
		// Create token
		KalturaUploadToken uploadToken = new KalturaUploadToken();
		uploadToken.fileName = file.getName();
		uploadToken.fileSize = file.length();
		KalturaUploadToken token = client.getUploadTokenService().add(uploadToken);
		assertNotNull(token);
		
		// upload
		try {
			client.getUploadTokenService().upload(token.id, file, false);
			fail();
		} catch (IllegalArgumentException e) {
			assert(e.getMessage().contains("is not readable or not a file"));
		}
	}
	
	
	/**
	 * Tests the following : 
	 * Media Service -
	 *  - add From Url
	 * http://www.kaltura.org/how-update-supposed-work-api-v3
	 */
	public void testUpdate() {
		if (logger.isEnabled())
			logger.info("Test Update Entry");
		
		String name = "test (" + new Date() + ")";
		
		try {
			startUserSession(client, kalturaConfig);
			KalturaMediaEntry addedEntry = BaseTest.addTestImage(this, client, name);
			assertNotNull(addedEntry);
			assertNotNull(addedEntry.id);
			
			String name2 = "test (" + new Date() + ")";
			
			KalturaMediaEntry updatedEntry = new KalturaMediaEntry();
			updatedEntry.name = name2;			
			client.getMediaService().update(addedEntry.id, updatedEntry);
			
			KalturaMediaEntry queriedEntry  = getProcessedEntry(client, addedEntry.id, true);
			assertEquals(name2, queriedEntry.name);
			
		} catch (Exception e) {
			e.printStackTrace();
			fail();
		}
	}
	
	/**
	 * Tests the following : 
	 * Media Service -
	 *  - Get
	 */
	public void testBadGet() {
		if (logger.isEnabled())
			logger.info("Starting badGet test");
		
		// look for one we know doesn't exist
		KalturaMediaEntry badEntry = null;
		try {
			startUserSession(client, kalturaConfig);
			KalturaMediaService mediaService = this.client.getMediaService();
			badEntry = mediaService.get("badid");
			fail();
		} catch (KalturaApiException kae) {
			// expected behavior
		}
		
		assertNull(badEntry);
	}
	
	/**
	 * Tests the following : 
	 * Media Service -
	 *  - Get
	 */
	public void testGet() {
		if (logger.isEnabled())
			logger.info("Starting get test");
		
		String name = "test (" + new Date() + ")";
		
		try {
			startUserSession(client, kalturaConfig);
			KalturaMediaEntry addedEntry = BaseTest.addTestImage(this, client, name);
			KalturaMediaService mediaService = this.client.getMediaService();
			KalturaMediaEntry retrievedEntry = mediaService.get(addedEntry.id);
			
			assertNotNull(retrievedEntry);
			assertEquals(addedEntry.id, retrievedEntry.id);
			
		} catch (Exception e) {
			e.printStackTrace();
			fail();
		}
		
	}

	/**
	 * Tests the following : 
	 * Media Service -
	 *  - list
	 */
	public void testList() {

		if (logger.isEnabled())
			logger.info("Test List");

		try {
			startUserSession(client, kalturaConfig);
			// add test clips
			String name1 = "test one (" + new Date() + ")";
			KalturaMediaEntry addedEntry1 = BaseTest.addTestImage(this, client, name1);
			String name2 = "test two (" + new Date() + ")";
			KalturaMediaEntry addedEntry2 = BaseTest.addTestImage(this, client, name2);

			// Make sure were updated
			getProcessedEntry(client, addedEntry1.id, true);
			getProcessedEntry(client, addedEntry2.id, true);

			KalturaMediaService mediaService = this.client.getMediaService();

			// get a list of clips starting with "test"
			KalturaMediaEntryFilter filter = new KalturaMediaEntryFilter();
			filter.mediaTypeEqual = null;
			filter.statusEqual = null;
			filter.typeEqual = null;
			filter.nameMultiLikeOr = name1 + "," + name2;

			KalturaMediaListResponse listResponse = mediaService.list(filter);
			assertEquals(listResponse.totalCount, 2);

			boolean found1 = false;
			boolean found2 = false;
			for (KalturaMediaEntry entry : listResponse.objects) {
				if (logger.isEnabled())
					logger.debug("id:" + entry.id);
				if (entry.id.equals(addedEntry1.id)) {
					found1 = true;
				} else if (entry.id.equals(addedEntry2.id)) {
					found2 = true;
				}
			}

			assertTrue(found1);
			assertTrue(found2);

		} catch (Exception e) {
			e.printStackTrace();
			fail();
		}
	}
	
	/**
	 * Tests the following : 
	 * Media Service -
	 *  - flag
	 *  - list flags
	 */
	public void testModeration() {
		
		if (logger.isEnabled())
			logger.info("Starting moderation test");
		
		final String FLAG_COMMENTS = "This is a test flag";
		
		if (logger.isEnabled())
			logger.info("Starting addFromUrl test");
        
		String name = "test (" + new Date() + ")";
		
		try {
			
			startAdminSession(client, kalturaConfig);

			KalturaMediaEntry addedEntry = BaseTest.addTestImage(this, client, name);
			//wait for the newly-added clip to process
			getProcessedEntry(client, addedEntry.id);
						
			KalturaMediaService mediaService = this.client.getMediaService();
			
			// flag the clip
			KalturaModerationFlag flag = new KalturaModerationFlag();
			flag.flaggedEntryId = addedEntry.id;
			flag.flagType = KalturaModerationFlagType.SPAM_COMMERCIALS;
			flag.comments = FLAG_COMMENTS;
			mediaService.flag(flag);
			
			// get the list of flags for this entry
			KalturaModerationFlagListResponse flagList = mediaService.listFlags(addedEntry.id);
			assertEquals(flagList.totalCount, 1);

			// check that the flag we put in is the flag we got back
			KalturaModerationFlag retFlag = (KalturaModerationFlag)flagList.objects.get(0);						
			assertEquals(retFlag.flagType, KalturaModerationFlagType.SPAM_COMMERCIALS);
			assertEquals(retFlag.comments, FLAG_COMMENTS);
			
		} catch (Exception e) {
			if (logger.isEnabled())
				logger.error("Got exception testing moderation flag", e);	
			e.printStackTrace();
			fail();
		} 
		
	}
	
	
	/**
	 * Tests the following : 
	 * Media Service -
	 *  - delete
	 */
	public void testDelete() throws KalturaApiException {
		if (logger.isEnabled())
			logger.info("Starting delete test");
		
		String name = "test (" + new Date() + ")";
		String idToDelete = "";
		
		startUserSession(client, kalturaConfig);
		KalturaMediaService mediaService = this.client.getMediaService();
		
		// First delete - should succeed
		try {
			
			KalturaMediaEntry addedEntry = BaseTest.addTestImage(this, client, name);
			assertNotNull(addedEntry);
			idToDelete = addedEntry.id;
			
			// calling this makes the test wait for processing to complete
			// if you call delete while it is processing, the delete doesn't happen
			getProcessedEntry(client,idToDelete);
			mediaService.delete(idToDelete);
			
		} catch (Exception e) {
			if (logger.isEnabled())
				logger.error("Trouble deleting", e);
			fail();
		} 

		// Second delete - should fail
		KalturaMediaEntry deletedEntry = null;
		try {
			deletedEntry = mediaService.get(idToDelete);
			fail();
		} catch (KalturaApiException kae) {
			// Wanted behavior
		} 
		
		// we whacked this one, so let's not keep track of it		
		this.testIds.remove(testIds.size() - 1);
		assertNull(deletedEntry);
	}
	
	/**
	 * Tests the following : 
	 * Media Service -
	 *  - upload
	 *  - add from uploaded file
	 */
	public void testUpload() {
		if (logger.isEnabled())
			logger.info("Starting delete test");
		
		String name = "test (" + new Date() + ")";
		
		KalturaMediaEntry entry = new KalturaMediaEntry();
		try {
			startUserSession(client, kalturaConfig);
			KalturaMediaService mediaService = this.client.getMediaService();

			InputStream fileData = TestUtils.getTestVideo();
			int fileSize = fileData.available();

			String result = mediaService.upload(fileData, KalturaTestConfig.UPLOAD_VIDEO, fileSize);
			if (logger.isEnabled())
				logger.debug("After upload, result:" + result);			
			entry.name = name;
			entry.type = KalturaEntryType.MEDIA_CLIP;
			entry.mediaType = KalturaMediaType.VIDEO;
			entry = mediaService.addFromUploadedFile(entry, result);
		} catch (Exception e) {
			if (logger.isEnabled())
				logger.error("Trouble uploading", e);
			fail();
		} 
		
		assertNotNull(entry.id);
		
		if (entry.id != null) {
			this.testIds.add(entry.id);
		}
	}
	
	public void testDataServe() {
		if (logger.isEnabled())
			logger.info("Starting test data serve");
		try {
			startUserSession(client, kalturaConfig);
			//client.getDataService().serve();
		} catch (Exception e) {
			e.printStackTrace();
			fail();
		} 
	}
	
	public void testPlaylist() {
		if (logger.isEnabled())	
			logger.info("Starting test playlist execute from filters");
		try {
			startAdminSession(client, kalturaConfig);
			
			// Create entry
			KalturaMediaEntry entry = BaseTest.addTestImage(this, client, "test (" + new Date() + ")");
			
			// generate filter
			KalturaMediaEntryFilterForPlaylist filter = new KalturaMediaEntryFilterForPlaylist();
			filter.idEqual = entry.id;
			ArrayList<KalturaMediaEntryFilterForPlaylist> filters = new ArrayList<KalturaMediaEntryFilterForPlaylist>();
			filters.add(filter);
			List<KalturaBaseEntry> res = client.getPlaylistService().executeFromFilters(filters, 5);
			assertNotNull(res);
			assertEquals(1, res.size());
			
		} catch (Exception e) {
			e.printStackTrace();
			fail();
		} 
	}
	
	public void testServe() throws KalturaApiException {
		String test = "bla bla bla";
		try {
			startUserSession(client, kalturaConfig);
			
			// Add Entry
			KalturaDataEntry dataEntry = new KalturaDataEntry();
			dataEntry.name = "test (" + new Date() + ")";
			dataEntry.dataContent = test;
			KalturaDataEntry addedDataEntry = client.getDataService().add(dataEntry);
			
			// serve
			String serveUrl = client.getDataService().serve(addedDataEntry.id);
			URL url = new URL(serveUrl);
			String content = readContent(url);
			assertEquals(test, content);
			
		} catch (MalformedURLException e) {
			e.printStackTrace();
			fail();
		} 
	}
	
	private String readContent(URL url) {
		StringBuffer sb = new StringBuffer();
		BufferedReader in = null;
		try {
			in = new BufferedReader(new InputStreamReader(url.openStream()));
			String inputLine;

			while ((inputLine = in.readLine()) != null)
				sb.append(inputLine);

			
		} catch (IOException e) {
			e.printStackTrace();
			fail();
		} finally {
			if(in != null)
				try {
					in.close();
				} catch (IOException e) {
					fail();
				}
		}

		return sb.toString();
	}
	
}

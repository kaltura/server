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


import java.io.InputStream;
import java.io.IOException;
import java.io.FileNotFoundException;
import java.util.ArrayList;
import java.util.List;

import junit.framework.TestCase;

import com.kaltura.client.KalturaApiException;
import com.kaltura.client.KalturaClient;
import com.kaltura.client.KalturaConfiguration;
import com.kaltura.client.enums.KalturaEntryStatus;
import com.kaltura.client.enums.KalturaMediaType;
import com.kaltura.client.enums.KalturaSessionType;
import com.kaltura.client.services.KalturaMediaService;
import com.kaltura.client.types.KalturaMediaEntry;
import com.kaltura.client.types.KalturaUploadToken;
import com.kaltura.client.types.KalturaUploadedFileTokenResource;
import com.kaltura.client.IKalturaLogger;
import com.kaltura.client.KalturaLogger;

public class BaseTest extends TestCase {
	protected KalturaTestConfig testConfig;
	
	protected KalturaConfiguration kalturaConfig = new KalturaConfiguration();
	protected KalturaClient client;
	
	// keeps track of test vids we upload so they can be cleaned up at the end
	protected List<String> testIds = new ArrayList<String>();

	protected boolean doCleanup = true;

	private static IKalturaLogger logger = KalturaLogger.getLogger(BaseTest.class);

	@Override
	protected void setUp() throws Exception {
		super.setUp();
		
		testConfig = new KalturaTestConfig();
		
		// Create client
		this.kalturaConfig.setEndpoint(testConfig.getServiceUrl());
		this.client = new KalturaClient(this.kalturaConfig);
	}
	
	@Override
	protected void tearDown() throws Exception {
		super.tearDown();
		
		if (!doCleanup) return;
		
		if (logger.isEnabled())
			logger.info("Cleaning up test entries after test");
		
		KalturaMediaService mediaService = this.client.getMediaService();
		for (String id : this.testIds) {
			if (logger.isEnabled())
				logger.info("Deleting " + id);
			try {
				getProcessedEntry(client, id);
				mediaService.delete(id);			
			} catch (Exception e) {
				if (logger.isEnabled())
					logger.error("Couldn't delete " + id, e);
				fail();
			}
		} //next id
	}
	
	
	public void startUserSession() throws Exception{
		startSession(KalturaSessionType.USER);
	}
	
	public void startAdminSession() throws Exception{
		startSession(KalturaSessionType.ADMIN);
	}
	
	protected void startSession(KalturaSessionType type) throws Exception {
		
		String sessionId = client.generateSessionV2(testConfig.getAdminSecret(), testConfig.getUserId(), type, testConfig.getPartnerId(), 86400, "");
		if (logger.isEnabled()){
			logger.debug("Session id:" + sessionId);
		}
		
		client.setSessionId(sessionId);
	}
	
	public static void closeSession(KalturaClient client) throws KalturaApiException {
		client.getSessionService().end();
	}
	
	// Entry utils
	
	public KalturaMediaEntry addTestImage(BaseTest container, String name) throws KalturaApiException, IOException, FileNotFoundException
	{
		KalturaMediaEntry entry = new KalturaMediaEntry();
		entry.name = name;
		entry.mediaType = KalturaMediaType.IMAGE;
		
		InputStream fileData = TestUtils.getTestImage();
		int fileSize = fileData.available();
		entry = client.getMediaService().add(entry);
		
		// Upload token
		KalturaUploadToken uploadToken = new KalturaUploadToken();
		uploadToken.fileName = testConfig.getUploadImage();
		uploadToken.fileSize = fileSize;
		KalturaUploadToken token = client.getUploadTokenService().add(uploadToken);
		assertNotNull(token);
		
		// Define content
		KalturaUploadedFileTokenResource resource = new KalturaUploadedFileTokenResource();
		resource.token = token.id;
		entry = client.getMediaService().addContent(entry.id, resource);
		assertNotNull(entry);
		
		// upload
		uploadToken = client.getUploadTokenService().upload(token.id, fileData, testConfig.getUploadImage(), fileSize, false);
		if(container != null)
			container.testIds.add(entry.id);
		return client.getMediaService().get(entry.id);
	}
	
	public static KalturaMediaEntry getProcessedEntry(KalturaClient client, String id) throws Exception {
		return getProcessedEntry(client, id, false);
	}
	
	public static KalturaMediaEntry getProcessedEntry(KalturaClient client, String id,
			Boolean checkReady) throws KalturaApiException {
		int maxTries = 50;
		int sleepInterval = 30 * 1000;
		int counter = 0;
		KalturaMediaEntry retrievedEntry = null;
		KalturaMediaService mediaService = client.getMediaService();
		retrievedEntry = mediaService.get(id);
		while (checkReady && retrievedEntry.status != KalturaEntryStatus.READY) {

			counter++;

			if (counter >= maxTries) {
				throw new RuntimeException("Max retries (" + maxTries
						+ ") when retrieving entry:" + id);
			} else {
				if (logger.isEnabled())
					logger.info("On try: " + counter + ", clip not ready. waiting "
						+ (sleepInterval / 1000) + " seconds...");
				try {
					Thread.sleep(sleepInterval);
				} catch (InterruptedException ie) {
					throw new RuntimeException("Failed while waiting for entry");
				}
			}

			retrievedEntry = mediaService.get(id);
		}

		return retrievedEntry;
	}
}

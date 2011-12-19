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
package com.kaltura.client.tests;


import java.util.ArrayList;
import java.util.List;
import org.apache.log4j.Logger;
import junit.framework.TestCase;
import com.kaltura.client.KalturaApiException;
import com.kaltura.client.KalturaClient;
import com.kaltura.client.KalturaConfiguration;
import com.kaltura.client.enums.KalturaEntryStatus;
import com.kaltura.client.enums.KalturaEntryType;
import com.kaltura.client.enums.KalturaMediaType;
import com.kaltura.client.enums.KalturaSessionType;
import com.kaltura.client.services.KalturaMediaService;
import com.kaltura.client.services.KalturaSessionService;
import com.kaltura.client.types.KalturaMediaEntry;

public class BaseTest extends TestCase {
	public KalturaConfiguration kalturaConfig = new KalturaConfiguration();

	protected KalturaClient client;
	
	// keeps track of test vids we upload so they can be cleaned up at the end
	protected List<String> testIds = new ArrayList<String>();

	protected boolean doCleanup = true;

	private Logger logger = Logger.getLogger(BaseTest.class);

	@Override
	protected void setUp() throws Exception {
		if (KalturaTestConfig.SECRET == "") {
			throw(new Error("Please fill the partner credentials to use"));
		}
		this.kalturaConfig.setPartnerId(KalturaTestConfig.PARTNER_ID);
		this.kalturaConfig.setSecret(KalturaTestConfig.SECRET);
		this.kalturaConfig.setAdminSecret(KalturaTestConfig.ADMIN_SECRET);
		this.kalturaConfig.setEndpoint(KalturaTestConfig.ENDPOINT);
		this.client = new KalturaClient(this.kalturaConfig);
	}
	
	protected void startUserSession() {
		try {
	        KalturaSessionService sessionService = this.client.getSessionService();

			String sessionId = sessionService.start(this.kalturaConfig.getSecret(),
	        										"admin",
	        										KalturaSessionType.USER,
	        										this.kalturaConfig.getPartnerId(),
	        										86400,
	        								 		"");
	        logger.debug("Session id:" + sessionId);
	        this.client.setSessionId(sessionId);
        
		} catch (Exception kae) {
			logger.error("Caught exception during setup", kae);
		}
	}
	
	protected void startAdminSession() {
		try {
	        KalturaSessionService sessionService = this.client.getSessionService();

	        String sessionId = sessionService.start(this.kalturaConfig.getAdminSecret(),
	        										"admin",
	        										KalturaSessionType.ADMIN,
	        										this.kalturaConfig.getPartnerId(),
	        										86400,
	        								 		"");
	        logger.debug("Session id:" + sessionId);
	        this.client.setSessionId(sessionId);
	        
		} catch (Exception kae) {
			logger.error("Caught exception during setup", kae);
		}
	}
	
	protected KalturaMediaEntry addClip(String name) {

		KalturaMediaEntry entry = new KalturaMediaEntry();
		
		entry.name = name;
		entry.type = KalturaEntryType.MEDIA_CLIP;
		entry.mediaType = KalturaMediaType.VIDEO;
		
		KalturaMediaEntry addedEntry = null;
		try {
			KalturaMediaService mediaService = this.client.getMediaService();
			addedEntry = mediaService.addFromUrl(entry, KalturaTestConfig.testUrl);
		} catch (KalturaApiException kae) {
			logger.error("Caught exception during add from url", kae);
		}
		
		if (addedEntry != null) {
			this.testIds.add(addedEntry.id);
		}
		
		return addedEntry;
	}
	
	protected KalturaMediaEntry getProcessedClip(String id) throws Exception {
		return getProcessedClip(id, false);
	}
	
	protected KalturaMediaEntry getProcessedClip(String id, Boolean checkReady) throws Exception {
		int maxTries = 30;
		int sleepInterval = 300000;
		int counter = 0;
		KalturaMediaEntry retrievedEntry = null;		
		try {
			KalturaMediaService mediaService = this.client.getMediaService();
			retrievedEntry = mediaService.get(id);
			while (checkReady && retrievedEntry.status != KalturaEntryStatus.READY) {
				
				counter++;

				if (counter >= maxTries) {
					throw new Exception("Max retries (" + maxTries + ") when retrieving entry:" + id);
				} else {
					logger.info("On try: " + counter + ", clip not ready. waiting "+(sleepInterval/60000)+" minutes...");
					try {
						Thread.sleep(sleepInterval);
					} catch (InterruptedException ie) {							
					}
				}
				
				retrievedEntry = mediaService.get(id);				
				
			} //wend
		
		} catch (KalturaApiException kae) {
			logger.error("Problem retrieving entry: " + kae.getLocalizedMessage());
		} 
	
		return retrievedEntry;
	}
	
	@Override
	protected void tearDown() {
		
		if (!doCleanup) return;
		
		logger.info("Cleaning up test entries after test");
		
		KalturaMediaService mediaService = this.client.getMediaService();
		for (String id : this.testIds) {
			logger.info("Deleting " + id);
			try {
				getProcessedClip(id);
				mediaService.delete(id);			
			} catch (Exception e) {
				logger.error("Couldn't delete " + id, e);
			}
		} //next id
	}
}

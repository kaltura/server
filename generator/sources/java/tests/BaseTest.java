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
	public final int PARTNER_ID = 0; // PARTNER_ID_GOES_HERE
	public final String SECRET = ""; // SERVICE_SECRET_GOES_HERE
	public final String ADMIN_SECRET = ""; // ADMIN_SECRET_GOES_HERE
	public final String ENDPOINT = "http://www.kaltura.com";
	
	protected String testUrl = "http://kaldev.kaltura.com/content/zbale/java_client_test_video.flv";

	public KalturaConfiguration kalturaConfig = new KalturaConfiguration();

	protected KalturaClient client;
	
	// keeps track of test vids we upload so they can be cleaned up at the end
	protected List<String> testIds = new ArrayList<String>();

	protected boolean doCleanup = true;

	private Logger logger = Logger.getLogger(BaseTest.class);

	@Override
	protected void setUp() throws Exception {		
		this.kalturaConfig.setPartnerId(PARTNER_ID);
		this.kalturaConfig.setSecret(SECRET);
		this.kalturaConfig.setAdminSecret(ADMIN_SECRET);
		this.kalturaConfig.setEndpoint(ENDPOINT);

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
			addedEntry = mediaService.addFromUrl(entry, this.testUrl);
		} catch (KalturaApiException kae) {
			logger.error("Caught exception during add from url", kae);
		}
		
		if (addedEntry != null) {
			this.testIds.add(addedEntry.id);
		}
		
		return addedEntry;
	}
	
	protected KalturaMediaEntry getProcessedClip(String id) throws Exception {
		int maxTries = 30;
		int sleepInterval = 12000;
		int counter = 0;
		KalturaMediaEntry retrievedEntry = null;		
		try {
			KalturaMediaService mediaService = this.client.getMediaService();
			retrievedEntry = mediaService.get(id);
			while (retrievedEntry.status != KalturaEntryStatus.READY) {
				
				counter++;

				if (counter >= maxTries) {
					throw new Exception("Max retries (" + maxTries + ") when retrieving entry:" + id);
				} else {
					logger.debug("On try: " + counter + ", clip not ready. waiting...");
					try {
						Thread.sleep(sleepInterval);
					} catch (InterruptedException ie) {							
					}
				}
				
				retrievedEntry = mediaService.get(id);				
				
			} //wend
		
		} catch (KalturaApiException kae) {
			logger.error("Problem retrieving entry");
		} 
	
		return retrievedEntry;
	}
	
	@Override
	protected void tearDown() {
		
		if (!doCleanup) return;
		
		logger.info("Cleaning up test entries after test");
		
		KalturaMediaService mediaService = this.client.getMediaService();
		for (String id : this.testIds) {
			logger.debug("Deleting " + id);
			try {
				getProcessedClip(id);
				mediaService.delete(id);			
			} catch (Exception e) {
				logger.error("Couldn't delete " + id, e);
			}
		} //next id
	}
}

package com.kaltura.client.tests;

import org.apache.log4j.Logger;

import com.kaltura.client.KalturaApiException;
import com.kaltura.client.KalturaClient;
import com.kaltura.client.services.KalturaSessionService;
import com.kaltura.client.enums.KalturaSessionType;

public class SessionServiceTest extends BaseTest {

	private Logger logger = Logger.getLogger(SessionServiceTest.class);
	
	public void testSession() {
		
        KalturaClient client = new KalturaClient(this.kalturaConfig);

        KalturaSessionService sessionService = client.getSessionService();
        assertNotNull(sessionService);
        
        try {
        	String sessionId = sessionService.start(this.kalturaConfig.getSecret(),
        										"admin",
        										KalturaSessionType.USER,
        										this.kalturaConfig.getPartnerId(),
        										86400,
        								 		"");
        	assertNotNull(sessionId);
        	logger.debug("Session id:" + sessionId);
        	client.setSessionId(sessionId);
        } catch (KalturaApiException kae) {
        	logger.error(kae);
        }
	}
}

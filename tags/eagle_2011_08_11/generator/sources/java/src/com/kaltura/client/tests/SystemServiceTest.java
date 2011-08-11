package com.kaltura.client.tests;

import org.apache.log4j.Logger;

import com.kaltura.client.services.KalturaSystemService;

public class SystemServiceTest extends BaseTest {
	
	private Logger logger = Logger.getLogger(SystemServiceTest.class);
			
	public void testPing() {
		logger.info("Starting ping test");

		startUserSession();
		
		boolean exceptionThrown = false;
		try {
			KalturaSystemService systemService = this.client.getSystemService();
			boolean result = systemService.ping();
			assertTrue(result);
		} catch (Exception e) {
			exceptionThrown = true;
		}
		
		assertFalse(exceptionThrown);
		
	}
		
}

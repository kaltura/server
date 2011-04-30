package com.kaltura.client.tests;

import junit.framework.Test;
import junit.framework.TestSuite;

public class KalturaTestSuite {
		
	@SuppressWarnings("unchecked")
	public static Test suite() {
			
		Class[] testClasses = { SystemServiceTest.class,
								SessionServiceTest.class,
								MediaServiceTest.class,
								PlaylistServiceTest.class,
								UiConfServiceTest.class };
		
		TestSuite suite = new TestSuite(testClasses);
		
		return suite;

	}

}

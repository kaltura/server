package com.kaltura.client.tests;

import java.util.ArrayList;
import java.util.Date;
import java.util.List;

import org.apache.log4j.Logger;

import com.kaltura.client.KalturaApiException;
import com.kaltura.client.enums.KalturaUiConfCreationMode;
import com.kaltura.client.services.KalturaUiConfService;
import com.kaltura.client.types.KalturaUiConf;
import com.kaltura.client.types.KalturaUiConfListResponse;

public class UiConfServiceTest extends BaseTest {
	private Logger logger = Logger.getLogger(UiConfServiceTest.class);

	// keeps track of test vids we upload so they can be cleaned up at the end
	protected List<Integer> testUiConfIds = new ArrayList<Integer>();
	
	protected KalturaUiConf addUiConf(String name) throws KalturaApiException {

		KalturaUiConfService uiConfService = client.getUiConfService();

		KalturaUiConf uiConf = new KalturaUiConf();
		uiConf.name = name;
		uiConf.description = "Ui conf unit test";
		uiConf.height = 373;
		uiConf.width = 750;
		uiConf.creationMode = KalturaUiConfCreationMode.ADVANCED;
		
		// this uiConf won't be editable in the KMC until it gets a config added to it, I think
		
		KalturaUiConf addedConf = uiConfService.add(uiConf);
				
		this.testUiConfIds.add(addedConf.id);
		
		return addedConf;
		
	}
	
	public void testAddUiConf() {
		logger.info("Starting ui conf add test");
		
		startAdminSession();
		
		boolean exceptionThrown = false;
		try {			
	
			String name = "Test UI Conf (" + new Date() + ")";
			KalturaUiConf addedConf = addUiConf(name);
			assertNotNull(addedConf);
			
		} catch (KalturaApiException kae) {
			exceptionThrown = true;
			logger.error(kae);
		}
		
		assertFalse(exceptionThrown);
	}
	
	public void testGetUiConf() {
		logger.info("Starting ui get test");
		
		startAdminSession();
		
		boolean exceptionThrown = false;
		try {			
			
			String name = "Test UI Conf (" + new Date() + ")";
			KalturaUiConf addedConf = addUiConf(name);
			
			int addedConfId = addedConf.id;
			
			KalturaUiConfService confService = this.client.getUiConfService();
			
			KalturaUiConf retrievedConf = confService.get(addedConfId);
			
			assertEquals(retrievedConf.id, addedConfId);
			
		} catch (KalturaApiException kae) {
			exceptionThrown = true;
			logger.error(kae);
		}
		
		assertFalse(exceptionThrown);
	}
	
	public void testDeleteUiConf() {
		logger.info("Starting ui conf delete test");
		
		startAdminSession();
		
		boolean exceptionThrown = false;
		try {			
			
			String name = "Test UI Conf (" + new Date() + ")";
			KalturaUiConf addedConf = addUiConf(name);
			
			int addedConfId = addedConf.id;
			
			KalturaUiConfService confService = this.client.getUiConfService();
			
			confService.delete(addedConfId);
			
			boolean notFound = false;
			try {
				confService.get(addedConfId);
			} catch (KalturaApiException kae) {
				notFound = true;
			} finally {
				assertTrue(notFound);
				
				// we whacked this one, so let's not keep track of it		
				this.testUiConfIds.remove(testUiConfIds.size() - 1);
			}
						
		} catch (KalturaApiException kae) {
			exceptionThrown = true;
			logger.error(kae);
		}
		
		assertFalse(exceptionThrown);
	}

	public void testListUiConf() {
		logger.info("Starting ui conf list test");
		
		startAdminSession();
		
		boolean exceptionThrown = false;
		try {
			KalturaUiConfService uiConfService = client.getUiConfService();
			assertNotNull(uiConfService);
			
			KalturaUiConfListResponse listResponse = uiConfService.list();
			assertNotNull(listResponse);
			
			for (KalturaUiConf uiConf : listResponse.objects) {
				logger.debug("uiConf id:" + uiConf.id + " name:" + uiConf.name);
			}
			
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
		
		logger.info("Cleaning up test UI Conf entries after test");
		
		KalturaUiConfService uiConfService = this.client.getUiConfService();
		for (Integer id : this.testUiConfIds) {
			logger.debug("Deleting UI conf " + id);
			try {
				uiConfService.delete(id);			
			} catch (Exception e) {
				logger.error("Couldn't delete " + id, e);
			}
		} //next id
	}
}

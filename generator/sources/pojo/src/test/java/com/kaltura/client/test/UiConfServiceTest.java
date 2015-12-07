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

import java.util.ArrayList;
import java.util.Date;
import java.util.List;

import com.kaltura.client.KalturaApiException;
import com.kaltura.client.enums.KalturaUiConfCreationMode;
import com.kaltura.client.services.KalturaUiConfService;
import com.kaltura.client.types.KalturaUiConf;
import com.kaltura.client.types.KalturaUiConfListResponse;
import com.kaltura.client.IKalturaLogger;
import com.kaltura.client.KalturaLogger;

public class UiConfServiceTest extends BaseTest {
	private IKalturaLogger logger = KalturaLogger.getLogger(UiConfServiceTest.class);

	// keeps track of test vids we upload so they can be cleaned up at the end
	protected List<Integer> testUiConfIds = new ArrayList<Integer>();
	
	protected KalturaUiConf addUiConf(String name) throws KalturaApiException {

		KalturaUiConfService uiConfService = client.getUiConfService();

		KalturaUiConf uiConf = new KalturaUiConf();
		uiConf.setName(name);
		uiConf.setDescription("Ui conf unit test");
		uiConf.setHeight(373);
		uiConf.setWidth(750);
		uiConf.setCreationMode(KalturaUiConfCreationMode.ADVANCED);
		uiConf.setConfFile("NON_EXISTING_CONF_FILE");
		
		// this uiConf won't be editable in the KMC until it gets a config added to it, I think
		
		KalturaUiConf addedConf = uiConfService.add(uiConf);
				
		this.testUiConfIds.add(addedConf.getId());
		
		return addedConf;
		
	}
	
	public void testAddUiConf() throws Exception {
		if (logger.isEnabled())
			logger.info("Starting ui conf add test");
		
		try {			
			startAdminSession();
			String name = "Test UI Conf (" + new Date() + ")";
			KalturaUiConf addedConf = addUiConf(name);
			assertNotNull(addedConf);
			
		} catch (KalturaApiException e) {
			if (logger.isEnabled())
				logger.error(e);
			fail(e.getMessage());
		}
		
	}
	
	public void testGetUiConf() throws Exception {
		if (logger.isEnabled())
			logger.info("Starting ui get test");
		
		try {			
			startAdminSession();
			String name = "Test UI Conf (" + new Date() + ")";
			KalturaUiConf addedConf = addUiConf(name);
			
			int addedConfId = addedConf.getId();
			KalturaUiConfService confService = this.client.getUiConfService();
			KalturaUiConf retrievedConf = confService.get(addedConfId);
			assertEquals(retrievedConf.getId(), addedConfId);
			
		} catch (KalturaApiException e) {
			if (logger.isEnabled())
				logger.error(e);
			fail(e.getMessage());
		}
		
	}
	
	public void testDeleteUiConf() throws Exception {
		if (logger.isEnabled())
			logger.info("Starting ui conf delete test");
		
		try {			
			startAdminSession();
			String name = "Test UI Conf (" + new Date() + ")";
			KalturaUiConf addedConf = addUiConf(name);
			
			int addedConfId = addedConf.getId();
			
			KalturaUiConfService confService = this.client.getUiConfService();
			
			confService.delete(addedConfId);
			
			try {
				confService.get(addedConfId);
				fail("Getting deleted ui-conf should fail");
			} catch (KalturaApiException kae) {
				// Wanted behavior
			} finally {
				// we whacked this one, so let's not keep track of it		
				this.testUiConfIds.remove(testUiConfIds.size() - 1);
			}
						
		} catch (KalturaApiException e) {
			if (logger.isEnabled())
				logger.error(e);
			fail(e.getMessage());
		}
	}

	public void testListUiConf() throws Exception {
		if (logger.isEnabled())
			logger.info("Starting ui conf list test");
		
		try {
			startAdminSession();
			KalturaUiConfService uiConfService = client.getUiConfService();
			assertNotNull(uiConfService);
			
			KalturaUiConfListResponse listResponse = uiConfService.list();
			assertNotNull(listResponse);
			
			for (KalturaUiConf uiConf : listResponse.getObjects()) {
				if (logger.isEnabled())
					logger.debug("uiConf id:" + uiConf.getId() + " name:" + uiConf.getName());
			}
			
		} catch (KalturaApiException e) {
			if (logger.isEnabled())
				logger.error(e);
			fail(e.getMessage());
		}
	}
	
	@Override
	protected void tearDown() throws Exception {
		
		super.tearDown();
		
		if (!doCleanup) return;
		
		if (logger.isEnabled())
			logger.info("Cleaning up test UI Conf entries after test");
		
		KalturaUiConfService uiConfService = this.client.getUiConfService();
		for (Integer id : this.testUiConfIds) {
			if (logger.isEnabled())
				logger.debug("Deleting UI conf " + id);
			try {
				uiConfService.delete(id);			
			} catch (Exception e) {
				if (logger.isEnabled())
					logger.error("Couldn't delete " + id, e);
			}
		} //next id
	}
}

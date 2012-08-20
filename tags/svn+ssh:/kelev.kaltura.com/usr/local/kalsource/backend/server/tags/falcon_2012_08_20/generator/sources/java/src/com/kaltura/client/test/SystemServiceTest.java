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

import com.kaltura.client.KalturaLogger;

import com.kaltura.client.services.KalturaSystemService;

public class SystemServiceTest extends BaseTest {
	
	private KalturaLogger logger = KalturaLogger.getLogger(SystemServiceTest.class);
			
	/**
	 * Tests that ping to the session works
	 */
	public void testPing() {
		if (logger.isEnabled())
			logger.info("Starting ping test");

		try {
			BaseTest.startUserSession(client,kalturaConfig);
			KalturaSystemService systemService = this.client.getSystemService();
			boolean result = systemService.ping();
			assertTrue(result);
			BaseTest.closeSession(client);
		} catch (Exception e) {
			fail();
		}
		
	}
		
}

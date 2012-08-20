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

import com.kaltura.client.KalturaApiException;
import com.kaltura.client.enums.KalturaSessionType;
import com.kaltura.client.types.KalturaMediaListResponse;

public class SessionServiceTest extends BaseTest {

	/**
	 * Test Open / close Session
	 */
	public void testSession() {

		try {
			
			// test open session
			BaseTest.startUserSession(client, kalturaConfig);
			assertNotNull(client.getSessionId());
			
			KalturaMediaListResponse response = client.getMediaService().list();
			assertNotNull(response);
			
			// Close session
			BaseTest.closeSession(client);
			
		} catch (KalturaApiException e) {
			e.printStackTrace();
			fail();
		}
		
		// Test close connection
		try {
			client.getMediaService().list();
			fail();
		} catch (KalturaApiException e) {
			// Should fail since the connection is closed.
		}
		
	}
	
	public void testExpiredSession() {
		try {
			String KS = client.generateSession(KalturaTestConfig.ADMIN_SECRET,
					"asdasd", KalturaSessionType.USER,
					KalturaTestConfig.PARTNER_ID, 60 * 60 * 24);
			client.setSessionId(KS);

			KalturaMediaListResponse response = client.getMediaService().list();
			assertNotNull(response);
		} catch (Exception e) {
			e.printStackTrace();
			fail();
		}

		try {
			String KS = client.generateSession(KalturaTestConfig.ADMIN_SECRET,
					"asdasd", KalturaSessionType.USER,
					KalturaTestConfig.PARTNER_ID, -60 * 60 * 24);
			client.setSessionId(KS);

			client.getMediaService().list();
			fail();
		} catch (Exception e) {
			assertTrue(e instanceof KalturaApiException);
			String msg = ((KalturaApiException)e).getMessage();
			assertTrue(msg.contains("EXPIRED"));
		}

	}
}

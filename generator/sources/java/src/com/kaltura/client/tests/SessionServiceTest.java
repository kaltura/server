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

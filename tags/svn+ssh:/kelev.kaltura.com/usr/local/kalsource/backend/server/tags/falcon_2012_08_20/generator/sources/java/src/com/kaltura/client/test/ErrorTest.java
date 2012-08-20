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

import org.apache.commons.httpclient.HttpClient;
import org.apache.commons.httpclient.methods.PostMethod;
import org.w3c.dom.Element;

import com.kaltura.client.KalturaApiException;
import com.kaltura.client.KalturaClient;
import com.kaltura.client.KalturaConfiguration;
import com.kaltura.client.KalturaObjectFactory;
import com.kaltura.client.KalturaParams;
import com.kaltura.client.types.KalturaMediaEntry;
import com.kaltura.client.types.KalturaMediaListResponse;



public class ErrorTest extends BaseTest {

	public void testInvalidServiceId() {
		this.kalturaConfig.setPartnerId(KalturaTestConfig.PARTNER_ID);
		this.kalturaConfig.setSecret(KalturaTestConfig.SECRET);
		this.kalturaConfig.setAdminSecret(KalturaTestConfig.ADMIN_SECRET);
		this.kalturaConfig.setEndpoint("http://2.2.2.2");
		this.kalturaConfig.setTimeout(2000);
		
		try {
			this.client = new KalturaClient(this.kalturaConfig);
			client.getSystemService().ping();
			fail();
		} catch (Exception e) {
			// Expected behavior
		}
	}
	
	public void testInvalidServerDnsName() {
		this.kalturaConfig.setPartnerId(KalturaTestConfig.PARTNER_ID);
		this.kalturaConfig.setSecret(KalturaTestConfig.SECRET);
		this.kalturaConfig.setAdminSecret(KalturaTestConfig.ADMIN_SECRET);
		this.kalturaConfig.setEndpoint("http://www.nonexistingkaltura.com");
		this.kalturaConfig.setTimeout(2000);
		
		try {
			this.client = new KalturaClient(this.kalturaConfig);
			client.getSystemService().ping();
			fail();
		} catch (Exception e) {
			// Expected behavior
		}
	}
	
	private class KalturaClientMock extends KalturaClient {
		
		String resultToReturn;

		public KalturaClientMock(KalturaConfiguration config, String res) {
			super(config);
			resultToReturn = res;
		}
		
		protected String executeMethod(HttpClient client, PostMethod method) {
			return resultToReturn;
		}
	}

	/**
	 * Tests case in which XML format is completely ruined
	 */
	public void testXmlParsingError() {
		KalturaClientMock mockClient = new KalturaClientMock(this.kalturaConfig, "<xml>");
		mockClient.queueServiceCall("system", "ping", new KalturaParams());
		try {
			mockClient.doQueue();
			fail();
		} catch (KalturaApiException e) {
			assertEquals("XPath expression exception evaluating result", e.getMessage());
		}
	}
	
	/**
	 * Tests case in which the response has xml format, but no object type as expected
	 */
	public void testTagInSimpleType() throws KalturaApiException {
		KalturaClientMock mockClient = new KalturaClientMock(this.kalturaConfig, "<xml><result><sometag></sometag></result></xml>");
		mockClient.queueServiceCall("system", "ping", new KalturaParams());
		Element resultXmlElement = mockClient.doQueue();
		try {
			KalturaObjectFactory.create(resultXmlElement);
			fail();
		} catch (Exception e) {
			// Expected behavior
		}
	}
	
	/**
	 * Tests case in which the response has xml format, but no object
	 */
	public void testEmptyObjectOrException() throws KalturaApiException {
		KalturaClientMock mockClient = new KalturaClientMock(this.kalturaConfig, "<xml><result></result></xml>");
		mockClient.queueServiceCall("system", "ping", new KalturaParams());
		Element resultXmlElement = mockClient.doQueue();
		try {
			KalturaObjectFactory.create(resultXmlElement);
			fail();
		} catch (Exception e) {
			// Expected behavior
		}
	}
	
	/**
	 * Tests case in which the response has xml format, but empty object
	 */
	public void testEmptyObject() throws KalturaApiException {
		KalturaClientMock mockClient = new KalturaClientMock(this.kalturaConfig, "<xml><result><objectType>KalturaPlaylist</objectType><filters><item/></filters></result></xml>");
		mockClient.queueServiceCall("system", "ping", new KalturaParams());
		Element resultXmlElement = mockClient.doQueue();
		try {
			KalturaObjectFactory.create(resultXmlElement);
			fail();
		} catch (Exception e) {
			// Expected behavior
		}
	}
	
	public void testTagInObjectDoesntStartWithType() throws KalturaApiException {
		KalturaClientMock mockClient = new KalturaClientMock(this.kalturaConfig, "<xml><result><id>1234</id></result></xml>");
		mockClient.queueServiceCall("system", "ping", new KalturaParams());
		Element resultXmlElement = mockClient.doQueue();
		try {
			KalturaObjectFactory.create(resultXmlElement);
			fail();
		} catch (Exception e) {
			// Expected behavior
		}
	}
	
	public void testCharsInsteadOfObject() throws KalturaApiException {
		KalturaClientMock mockClient = new KalturaClientMock(this.kalturaConfig, "<xml><result>1234</result></xml>");
		mockClient.queueServiceCall("system", "ping", new KalturaParams());
		Element resultXmlElement = mockClient.doQueue();
		try {
			KalturaObjectFactory.create(resultXmlElement);
			fail();
		} catch (Exception e) {
			// Expected behavior
		}
	}
	
	public void testUnknownObjectType() throws KalturaApiException {
		KalturaClientMock mockClient = new KalturaClientMock(this.kalturaConfig, "<xml><result><objectType>UnknownObjectType</objectType></result></xml>");
		mockClient.queueServiceCall("system", "ping", new KalturaParams());
		Element resultXmlElement = mockClient.doQueue();
		try {
			KalturaObjectFactory.create(resultXmlElement);
			fail();
		} catch (Exception e) {
			assertEquals("Invalid object : UnknownObjectType", e.getMessage());
		}
	}
	
	public void testNonKalturaObjectType() throws KalturaApiException {
		KalturaClientMock mockClient = new KalturaClientMock(this.kalturaConfig, "<xml><result><objectType>NSString</objectType></result></xml>");
		mockClient.queueServiceCall("system", "ping", new KalturaParams());
		Element resultXmlElement = mockClient.doQueue();
		try {
			KalturaObjectFactory.create(resultXmlElement);
			fail();
		} catch (Exception e) {
			assertEquals("Invalid object : NSString", e.getMessage());
		}
	}
	
	public void testArrayOfUknownEntry() throws KalturaApiException {
		String testXml = "<xml><result><objectType>KalturaMediaListResponse</objectType><objects>" +
				"<item><objectType>NonExistingclass</objectType><id>test1</id><name>test1</name></item>" +
				"<item><objectType>NonExistingclass</objectType><id>test2</id><name>test2</name></item>" +
				"</objects><totalCount>2</totalCount></result></xml>";
		
		KalturaClientMock mockClient = new KalturaClientMock(this.kalturaConfig, testXml);
		mockClient.queueServiceCall("system", "ping", new KalturaParams()); // Just since we need something in the queue
		Element resultXmlElement = mockClient.doQueue();
		try {
			KalturaMediaListResponse res = (KalturaMediaListResponse) KalturaObjectFactory.create(resultXmlElement);
			assertEquals(2, res.totalCount);
			KalturaMediaEntry entry1 = res.objects.get(0);
			KalturaMediaEntry entry2 = res.objects.get(1);
			assertTrue(entry1.id.equals("test1"));
			assertTrue(entry1.name.equals("test1"));
			assertTrue(entry2.id.equals("test2"));
			assertTrue(entry2.name.equals("test2"));
		} catch (Exception e) {
			e.printStackTrace();
			fail();
		}
	}
}

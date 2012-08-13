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

import java.io.InputStream;
import java.util.ArrayList;
import java.util.Date;
import java.util.List;

import com.kaltura.client.KalturaApiException;
import com.kaltura.client.KalturaMultiResponse;
import com.kaltura.client.enums.KalturaMediaType;
import com.kaltura.client.types.KalturaBaseEntry;
import com.kaltura.client.types.KalturaMediaEntry;
import com.kaltura.client.types.KalturaMediaEntryFilterForPlaylist;
import com.kaltura.client.types.KalturaUploadToken;
import com.kaltura.client.types.KalturaUploadedFileTokenResource;
import com.kaltura.client.utils.ParseUtils;


public class MultiRequestTest extends BaseTest{

	@SuppressWarnings("unchecked")
	public void testMultiRequest() throws Exception {
		
		BaseTest.startAdminSession(client,kalturaConfig);
		client.setMultiRequest(true);
		
		// 1. Ping (Bool : void)
		client.getSystemService().ping();
		
		// 2. Create Entry (Object : Object)
		KalturaMediaEntry entry = new KalturaMediaEntry();
		entry.name = "test (" + new Date() + ")";
		entry.mediaType = KalturaMediaType.IMAGE;
		InputStream fileData = TestUtils.getTestImage();
		entry = client.getMediaService().add(entry);
		assertNull(entry);
		
		// 3. Upload token (Object : Object)
		KalturaUploadToken uploadToken = new KalturaUploadToken();
		uploadToken.fileName = KalturaTestConfig.UPLOAD_IMAGE;
		uploadToken.fileSize = fileData.available();
		KalturaUploadToken token = client.getUploadTokenService().add(uploadToken);
		assertNull(token);
		
		// 4. Add Content (Object : String, Object)
		KalturaUploadedFileTokenResource resource = new KalturaUploadedFileTokenResource();
		resource.token = "{3:result:id}";
		entry = client.getMediaService().addContent("{2:result:id}", resource);
		assertNull(entry);
		
		// 5. upload (Object : String, file, boolean)
		uploadToken = client.getUploadTokenService().upload("{3:result:id}", fileData, KalturaTestConfig.UPLOAD_IMAGE, fileData.available(), false);
		
		KalturaMultiResponse multi = client.doMultiRequest();
		// 0
		assertNotNull(multi.get(0));
		assertTrue(ParseUtils.parseBool((String)multi.get(0)));
		// 1
		KalturaMediaEntry mEntry = (KalturaMediaEntry) multi.get(1);
		assertNotNull(mEntry);
		assertNotNull(mEntry.id);
		// 2
		KalturaUploadToken mToken =(KalturaUploadToken) multi.get(2);
		assertNotNull(mToken);
		assertNotNull(mToken.id);
		// 3
		assertTrue(multi.get(3) instanceof KalturaMediaEntry);
		// 4
		assertTrue(multi.get(4) instanceof KalturaUploadToken);
		
		// Multi request part II:
		client.setMultiRequest(true);
		
		// execute from filters (Array: Array, int)
		KalturaMediaEntryFilterForPlaylist filter = new KalturaMediaEntryFilterForPlaylist();
		filter.idEqual = mEntry.id;
		ArrayList<KalturaMediaEntryFilterForPlaylist> filters = new ArrayList<KalturaMediaEntryFilterForPlaylist>();
		filters.add(filter);
		List<KalturaBaseEntry> res = client.getPlaylistService().executeFromFilters(filters, 5);
		assertNull(res);

		multi = client.doMultiRequest();
		List<KalturaBaseEntry> mRes = (List<KalturaBaseEntry>)multi.get(0);
		assertNotNull(mRes);
		assertEquals(1, mRes.size());
		
		client.getMediaService().delete(mEntry.id);
	}
	
	
	/**
	 * This function tests that in a case of error in a multi request, the error is parsed correctly
	 * and it doesn't affect the rest of the multi-request.
	 * @throws KalturaApiException
	 */
	public void testMultiRequestWithError() throws KalturaApiException {
		
		BaseTest.startAdminSession(client,kalturaConfig);
		client.setMultiRequest(true);
		
		client.getSystemService().ping();
		client.getMediaService().get("Illegal String");
		client.getSystemService().ping();
		
		KalturaMultiResponse multi = client.doMultiRequest();
		assertNotNull(multi.get(0));
		assertTrue(ParseUtils.parseBool((String)multi.get(0)));
		assertTrue(multi.get(1) instanceof KalturaApiException);
		assertNotNull(multi.get(2));
		assertTrue(ParseUtils.parseBool((String)multi.get(2)));
		
	}
}

package com.kaltura.client.tests;

import java.io.File;
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
	public void testMultiRequest() throws KalturaApiException {
		
		BaseTest.startAdminSession(client,kalturaConfig);
		client.setMultiRequest(true);
		
		// 1. Ping (Bool : void)
		client.getSystemService().ping();
		
		// 2. Create Entry (Object : Object)
		KalturaMediaEntry entry = new KalturaMediaEntry();
		entry.name = "test (" + new Date() + ")";
		entry.mediaType = KalturaMediaType.IMAGE;
		File file = new File(KalturaTestConfig.UPLOAD_IMAGE);
		entry = client.getMediaService().add(entry);
		assertNull(entry);
		
		// 3. Upload token (Object : Object)
		KalturaUploadToken uploadToken = new KalturaUploadToken();
		uploadToken.fileName = file.getName();
		uploadToken.fileSize = file.length();
		KalturaUploadToken token = client.getUploadTokenService().add(uploadToken);
		assertNull(token);
		
		// 4. Add Content (Object : String, Object)
		KalturaUploadedFileTokenResource resource = new KalturaUploadedFileTokenResource();
		resource.token = "{3:result:id}";
		entry = client.getMediaService().addContent("{2:result:id}", resource);
		assertNull(entry);
		
		// 5. upload (Object : String, file, boolean)
		uploadToken = client.getUploadTokenService().upload("{3:result:id}", file, false);
		
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

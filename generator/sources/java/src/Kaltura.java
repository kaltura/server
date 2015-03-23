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

import java.io.InputStream;
import java.io.FileNotFoundException;
import java.io.IOException;

import com.kaltura.client.KalturaApiException;
import com.kaltura.client.KalturaClient;
import com.kaltura.client.KalturaConfiguration;
import com.kaltura.client.KalturaMultiResponse;
import com.kaltura.client.enums.KalturaEntryStatus;
import com.kaltura.client.enums.KalturaMediaType;
import com.kaltura.client.enums.KalturaSessionType;
import com.kaltura.client.services.KalturaMediaService;
import com.kaltura.client.types.KalturaMediaEntry;
import com.kaltura.client.types.KalturaMediaListResponse;
import com.kaltura.client.types.KalturaPartner;
import com.kaltura.client.types.KalturaUploadToken;
import com.kaltura.client.types.KalturaUploadedFileTokenResource;

import com.kaltura.client.test.KalturaTestConfig;
import com.kaltura.client.test.TestUtils;

public class Kaltura {
	
	private static final int WAIT_BETWEEN_TESTS = 30000;
	protected static KalturaTestConfig testConfig;
	static public KalturaClient client;
	
	public static void main(String[] args) throws IOException {

		if(testConfig == null){
			testConfig = new KalturaTestConfig();
		}
		
		try {

			list();
			multiRequest();
			KalturaMediaEntry entry = addEmptyEntry();
			uploadMediaFileAndAttachToEmptyEntry(entry);
			testIfEntryIsReadyForPublish(entry);
			// cleanup the sample by deleting the entry:
			deleteEntry(entry);
			System.out.println("Sample code finished successfully.");
			
		} catch (KalturaApiException e) {
			System.out.println("Example failed.");
			e.printStackTrace();
		}
	}
	
	/**
	 * Helper function to create the Kaltura client object once and then reuse a static instance.
	 * @return a singleton of <code>KalturaClient</code> used in this case 
	 * @throws KalturaApiException if failed to generate session
	 */
	private static KalturaClient getKalturaClient() throws KalturaApiException
	{
		if (client != null) {
			return client;
		}
		
		// Set Constants
		int partnerId = testConfig.getPartnerId();
		String adminSecret = testConfig.getAdminSecret();
		String userId = testConfig.getUserId();
		
		// Generate configuration
		KalturaConfiguration config = new KalturaConfiguration();
		config.setEndpoint(testConfig.getServiceUrl());
		
		try {
			// Create the client and open session
			client = new KalturaClient(config);
			String ks = client.generateSession(adminSecret, userId, KalturaSessionType.ADMIN, partnerId);
			client.setSessionId(ks);
		} catch(Exception ex) {
			client = null;
			throw new KalturaApiException("Failed to generate session");
		}
		
		System.out.println("Generated KS locally: [" + client.getSessionId() + "]");
		return client;
	}
	
	/** 
	 * lists all media in the account.
	 */
	private static void list() throws KalturaApiException {

		KalturaMediaListResponse list = getKalturaClient().getMediaService().list();
		if (list.totalCount > 0) {
			System.out.println("The account contains " + list.totalCount + " entries.");
			for (KalturaMediaEntry entry : list.objects) {
				System.out.println("\t \"" + entry.name + "\"");
			}
		} else {
			System.out.println("This account doesn't have any entries in it.");
		}
	}

	/**
	 * shows how to chain requests together to call a multi-request type where several requests are called in a single request.
	 */
	private static void multiRequest() throws KalturaApiException
 {
		KalturaClient client = getKalturaClient();
		client.startMultiRequest();
		client.getBaseEntryService().count();
		client.getPartnerService().getInfo();
		client.getPartnerService().getUsage(2010);
		KalturaMultiResponse multi = client.doMultiRequest();
		KalturaPartner partner = (KalturaPartner) multi.get(1);
		System.out.println("Got Admin User email: " + partner.adminEmail);

	}
	
	/** 
	 * creates an empty media entry and assigns basic metadata to it.
	 * @return the generated <code>KalturaMediaEntry</code>
	 * @throws KalturaApiException 
	 */
	private static KalturaMediaEntry addEmptyEntry() throws KalturaApiException {
		System.out.println("Creating an empty Kaltura Entry (without actual media binary attached)...");
		KalturaMediaEntry entry = new KalturaMediaEntry();
		entry.name = "An Empty Kaltura Entry Test";
		entry.mediaType = KalturaMediaType.VIDEO;
		KalturaMediaEntry newEntry = getKalturaClient().getMediaService().add(entry);
		System.out.println("The id of our new Video Entry is: " + newEntry.id);
		return newEntry;
	}
	
	/**
	 *  uploads a video file to Kaltura and assigns it to a given Media Entry object
	 */
	private static void uploadMediaFileAndAttachToEmptyEntry(KalturaMediaEntry entry) throws KalturaApiException
	{
			KalturaClient client = getKalturaClient();			
			System.out.println("Uploading a video file...");
			
			// upload upload token
			KalturaUploadToken upToken = client.getUploadTokenService().add();
			KalturaUploadedFileTokenResource fileTokenResource = new KalturaUploadedFileTokenResource();
			
			// Connect to media entry and update name
			fileTokenResource.token = upToken.id;
			entry = client.getMediaService().addContent(entry.id, fileTokenResource);
			
			// Upload actual data
			try
			{
				InputStream fileData = TestUtils.getTestVideo();
				int fileSize = fileData.available();

				client.getUploadTokenService().upload(upToken.id, fileData, testConfig.getUploadVideo(), fileSize);
				
				System.out.println("Uploaded a new Video file to entry: " + entry.id);
			}
			catch (FileNotFoundException e)
			{
				System.out.println("Failed to open test video file");
			}
			catch (IOException e)
			{
				System.out.println("Failed to read test video file");
			}
	}
	
	/** 
	 * periodically calls the Kaltura API to check that a given video entry has finished transcoding and is ready for playback. 
	 * @param entry The <code>KalturaMediaEntry</code> we want to test
	 */
	private static void testIfEntryIsReadyForPublish(KalturaMediaEntry entry)
			throws KalturaApiException {

		System.out.println("Testing if Media Entry has finished processing and ready to be published...");
		KalturaMediaService mediaService = getKalturaClient().getMediaService();
		while (true) {
			KalturaMediaEntry retrievedEntry = mediaService.get(entry.id);
			if (retrievedEntry.status == KalturaEntryStatus.READY) {
				break;
			}
			System.out.println("Media not ready yet. Waiting 30 seconds.");
			try {
				Thread.sleep(WAIT_BETWEEN_TESTS);
			} catch (InterruptedException ie) {
			}
		}
		System.out.println("Entry id: " + entry.id + " is now ready to be published and played.");
	}

	/** 
	 * deletes a given entry
	 * @param entry the <code>KalturaMediaEntry</code> we want to delete
	 * @throws KalturaApiException
	 */
	private static void deleteEntry(KalturaMediaEntry entry)
			throws KalturaApiException {
		System.out.println("Deleting entry id: " + entry.id);
		getKalturaClient().getMediaService().delete(entry.id);
	}
}

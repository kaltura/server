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

import java.io.File;

import sun.management.FileSystem;

import com.kaltura.client.*;
import com.kaltura.client.enums.KalturaEntryStatus;
import com.kaltura.client.enums.KalturaEntryType;
import com.kaltura.client.enums.KalturaMediaType;
import com.kaltura.client.enums.KalturaSessionType;
import com.kaltura.client.services.KalturaMediaService;
import com.kaltura.client.tests.KalturaTestConfig;
import com.kaltura.client.types.KalturaBaseEntry;
import com.kaltura.client.types.KalturaBaseEntryListResponse;
import com.kaltura.client.types.KalturaMediaEntry;
import com.kaltura.client.types.KalturaMediaListResponse;
import com.kaltura.client.types.KalturaPartner;
import com.kaltura.client.types.KalturaUploadToken;
import com.kaltura.client.types.KalturaUploadedFileTokenResource;

public class Kaltura {
	
	static public KalturaClient client;
	
	// shows a few sample Kaltura Api calls
	public Kaltura() {	}

	/**
	 * @param args
	 */
	public static void main(String[] args) {
		if (KalturaTestConfig.SECRET == "") {
			throw(new Error("Please fill the partner credentials to use in the KalturaTestConfig class"));
		}
		Kaltura samples = new Kaltura();
		samples.list();
		samples.multiRequest();
		KalturaMediaEntry entry = samples.addEmptyEntry();
		samples.uploadMediaFileAndAttachToEmptyEntry(entry);
		samples.testIfEntryIsReadyForPublish(entry);
		//cleanup the sample by deleting the entry:
		samples.deleteEntry(entry);
		System.out.print("\nSample code finished successfully.");
	}
	
	
	//helper function to create the Kaltura client object once and then reuse a static instance.
	private KalturaClient getKalturaClient() throws KalturaApiException
	{
		int partnerId = KalturaTestConfig.PARTNER_ID;
		String adminSecret = KalturaTestConfig.ADMIN_SECRET;
		Boolean isAdmin = true;
		if (client != null) return client;
		KalturaConfiguration config = new KalturaConfiguration();
		config.setPartnerId(partnerId);
		config.setEndpoint("http://www.kaltura.com");
		client = new KalturaClient(config);
		String userId="SomeOneWeKnow"; 
		KalturaSessionType type = (isAdmin ? KalturaSessionType.ADMIN: KalturaSessionType.USER);
		try {
			String ks = client.generateSession(adminSecret, userId, type, partnerId);
			System.out.print("\nGenerated KS locally: ["+ks+"]");
			client.setSessionId(ks);
		} catch(Exception ex) {
			throw new KalturaApiException();
		}
		return client;
	}
	
	//lists all media in the account and prints the first entry name
	public void list()
	{
		try {
			KalturaClient client = getKalturaClient();			
			//Should not call Base directly - this is an Abstract!
			//KalturaBaseEntryListResponse list = client.getBaseEntryService().list();
			KalturaMediaListResponse list = client.getMediaService().list();
			if (list.totalCount > 0) {
				KalturaMediaEntry entry = list.objects.get(0);
				System.out.print("\nGot an entry: \"" + entry.name + "\"");
			} else {
				System.out.print("\nThis account doesn't have any entries in it.");
			}
		} catch (KalturaApiException e) {
			e.printStackTrace();
		}		
	}
	
	//shows how to chain requests together to call a multi-request type where several requests are called in a single request.
	public void multiRequest()
	{
		try {
			KalturaClient client = getKalturaClient();
			client.setMultiRequest(true);
			client.getBaseEntryService().count();
			client.getPartnerService().getInfo();
			client.getPartnerService().getUsage(2010);	
			KalturaMultiResponse multi = client.doMultiRequest();
			KalturaPartner partner = (KalturaPartner)multi.get(1);
			System.out.print("\nGot Admin User email: " + partner.adminEmail);
		} catch (KalturaApiException e) {
			e.printStackTrace();
		}		
	}
	
	//creates an empty media entry and assigns basic metadata to it.
	private KalturaMediaEntry addEmptyEntry()
	{
		try {
			KalturaClient client = getKalturaClient();			
			System.out.print("\nCreating an empty Kaltura Entry (without actual media binary attached)...");
			KalturaMediaEntry entry = new KalturaMediaEntry();
			entry.name = "An Empty Kaltura Entry Test";
			entry.mediaType = KalturaMediaType.VIDEO;
			KalturaMediaEntry newEntry = client.getMediaService().add(entry);
			System.out.print("\nThe id of our new Video Entry is: " + newEntry.id);
			return newEntry;
		} catch (KalturaApiException e) {
			e.printStackTrace();
			return null;
		}	
	}
	
	//uploads a video file to Kaltura and assigns it to a given Media Entry object 
	private void uploadMediaFileAndAttachToEmptyEntry(KalturaMediaEntry entry)
	{
		try {
			KalturaClient client = getKalturaClient();			
			System.out.print("\nUploading a video file...");
			File fileData = new File(KalturaTestConfig.UPLOAD_FILE);
			KalturaUploadToken upToken = client.getUploadTokenService().add();
			String uploadTokenId = upToken.id;
			client.getUploadTokenService().upload(uploadTokenId, fileData);
			KalturaUploadedFileTokenResource fileTokenResource = new KalturaUploadedFileTokenResource();
			fileTokenResource.token = uploadTokenId;
			KalturaMediaEntry newEntry = client.getMediaService().addContent(entry.id, fileTokenResource);
			//update the entry name: 
			KalturaMediaEntry updateEntry = new KalturaMediaEntry();
			updateEntry.name = "A Media Entry with Video";
			newEntry = client.getMediaService().update(newEntry.id, updateEntry);
			System.out.print("\nUploaded a new Video file to entry: " + newEntry.id);
		} catch (KalturaApiException e) {
			e.printStackTrace();
		}	
	}
	
	//periodically calls the Kaltura API to check that a given video entry has finished transcoding and is ready for playback. 
	private void testIfEntryIsReadyForPublish (KalturaMediaEntry entry)
	{
		try {
			KalturaClient client = getKalturaClient();			
			System.out.print("\nTesting if Media Entry has finished processing and ready to be published...");
			KalturaMediaEntry retrievedEntry = null;		
			try {
				KalturaMediaService mediaService = client.getMediaService();
				retrievedEntry = mediaService.get(entry.id);
				while (retrievedEntry.status != KalturaEntryStatus.READY) {
					System.out.print("\nMedia not ready yet. Waiting 30 seconds.");
					try {
						Thread.sleep(30000);
					} catch (InterruptedException ie) { }
					retrievedEntry = mediaService.get(entry.id);				
				}
				System.out.print("\nEntry id: "+entry.id+" is now ready to be published and played.");
			} catch (KalturaApiException kae) {
				System.out.print("\nProblem retrieving entry: " + kae.getLocalizedMessage());
			}
		} catch (KalturaApiException e) {
			e.printStackTrace();
		}	
	}
	
	//deletes a given entry
	private void deleteEntry (KalturaMediaEntry entry)
	{
		try {
			KalturaClient client = getKalturaClient();			
			System.out.print("\nDeleting entry id: "+entry.id);
			client.getMediaService().delete(entry.id);
		} catch (KalturaApiException e) {
			e.printStackTrace();
		}	
	}
}

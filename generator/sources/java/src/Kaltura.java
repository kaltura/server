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
import com.kaltura.client.enums.KalturaEntryType;
import com.kaltura.client.enums.KalturaMediaType;
import com.kaltura.client.enums.KalturaSessionType;
import com.kaltura.client.tests.KalturaTestConfig;
import com.kaltura.client.types.KalturaBaseEntry;
import com.kaltura.client.types.KalturaBaseEntryListResponse;
import com.kaltura.client.types.KalturaMediaEntry;
import com.kaltura.client.types.KalturaMediaListResponse;
import com.kaltura.client.types.KalturaPartner;

public class Kaltura {

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
		samples.multiReponse();
		samples.add();
		System.out.print("\nSample code finished successfully.");
	}

	private KalturaClient getKalturaClient(int partnerId, String adminSecret, boolean isAdmin) throws KalturaApiException
	{
		KalturaConfiguration config = new KalturaConfiguration();
		config.setPartnerId(partnerId);
		config.setEndpoint("http://www.kaltura.com");
		KalturaClient client = new KalturaClient(config);
		String userId="1"; 
		//String ks = client.getSessionService().start(secret, userId, (isAdmin ? KalturaSessionType.ADMIN : KalturaSessionType.USER));
		KalturaSessionType type = (isAdmin ? KalturaSessionType.ADMIN: KalturaSessionType.USER);

		try
		{
			String ks = client.generateSession(adminSecret, userId, type, partnerId);
			System.out.print("generated KS locally: ["+ks+"]");
			client.setSessionId(ks);
		}
		catch(Exception ex)
		{
			throw new KalturaApiException();
		}
		
		return client;
	}
	
	public void list()
	{
		try {
			KalturaClient client = getKalturaClient(KalturaTestConfig.PARTNER_ID, KalturaTestConfig.ADMIN_SECRET, true);			
			//Should not call Base directly - this is an Abstract!
			//KalturaBaseEntryListResponse list = client.getBaseEntryService().list();
			KalturaMediaListResponse list = client.getMediaService().list();
			KalturaMediaEntry entry = list.objects.get(0);
			System.out.print("\nGot an entry: \"" + entry.name + "\"");
		} catch (KalturaApiException e) {
			e.printStackTrace();
		}		
	}
	
	public void multiReponse()
	{
		try {
			KalturaClient client = getKalturaClient(KalturaTestConfig.PARTNER_ID, KalturaTestConfig.ADMIN_SECRET, true);
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
	
	private void add()
	{
		try {
			System.out.print("\nUploading test video...");
			KalturaClient client = getKalturaClient(KalturaTestConfig.PARTNER_ID, KalturaTestConfig.ADMIN_SECRET, false);			
			File up = new File(KalturaTestConfig.UPLOAD_FILE);
			String token = client.getBaseEntryService().upload(up);
			KalturaMediaEntry entry = new KalturaMediaEntry();
			entry.name = "my upload entry";
			entry.mediaType = KalturaMediaType.VIDEO;
			KalturaMediaEntry newEntry = client.getMediaService().addFromUploadedFile(entry, token);
			System.out.print("\nUploaded a new Video entry " + newEntry.id);
			client.getMediaService().delete(newEntry.id);
			try {
				entry = null;
				entry = client.getMediaService().get(newEntry.id);
			} catch (KalturaApiException exApi) {
				if (entry == null) {
					System.out.print("\nDeleted the entry ("+newEntry.id+") successfully!");
				}
			}
		} catch (KalturaApiException e) {
			e.printStackTrace();
		}	
	}
}

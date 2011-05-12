
import java.io.File;

import sun.management.FileSystem;

import com.kaltura.client.*;
import com.kaltura.client.enums.KalturaEntryType;
import com.kaltura.client.enums.KalturaMediaType;
import com.kaltura.client.enums.KalturaSessionType;
import com.kaltura.client.types.KalturaBaseEntry;
import com.kaltura.client.types.KalturaBaseEntryListResponse;
import com.kaltura.client.types.KalturaMediaEntry;
import com.kaltura.client.types.KalturaPartner;

public class Kaltura {

	private static final  int PARTNER_ID = 0;
	private static final  String SECRET = "";
	private static final  String ADMIN_SECRET = "";
	private static final String UPLOAD_FILE = "bin/DemoVideo.flv";
	
	public Kaltura() {	}

	/**
	 * @param args
	 */
	public static void main(String[] args) {
		Kaltura samples = new Kaltura();
		samples.list();
		samples.multiReponse();
		samples.add();
		System.out.print("\nSample code finished successfully.");
	}

	private KalturaClient getKalturaClient(int partnerId, String secret, boolean isAdmin) throws KalturaApiException
	{
		KalturaConfiguration config = new KalturaConfiguration();
		config.setPartnerId(partnerId);
		config.setEndpoint("http://www.kaltura.com");
		KalturaClient client = new KalturaClient(config);
		String userId="1"; 
		String ks = client.getSessionService().start(secret, userId, (isAdmin ? KalturaSessionType.ADMIN : KalturaSessionType.USER));
		client.setSessionId(ks);
		return client;
	}
	
	public void list()
	{
		try {
			KalturaClient client = getKalturaClient(PARTNER_ID, SECRET, false);			
			KalturaBaseEntryListResponse list = client.getBaseEntryService().list();
			KalturaBaseEntry entry = list.objects.get(0);
			System.out.print("\nGot an entry: " + entry.name);
		} catch (KalturaApiException e) {
			e.printStackTrace();
		}		
	}
	
	public void multiReponse()
	{
		try {
			KalturaClient client = getKalturaClient(PARTNER_ID, ADMIN_SECRET, true);
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
			KalturaClient client = getKalturaClient(PARTNER_ID, SECRET, false);			
			File up = new File(UPLOAD_FILE);
			String token = client.getBaseEntryService().upload(up);
			KalturaMediaEntry entry = new KalturaMediaEntry();
			entry.name = "my upload entry";
			entry.mediaType = KalturaMediaType.VIDEO;
			KalturaMediaEntry newEntry = client.getMediaService().addFromUploadedFile(entry, token);
			System.out.print("\nUploaded a new Video entry " + newEntry.id);
		} catch (KalturaApiException e) {
			e.printStackTrace();
		}	
	}
}

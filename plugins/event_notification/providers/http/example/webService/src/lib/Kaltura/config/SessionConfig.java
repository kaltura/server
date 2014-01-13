package lib.Kaltura.config;


import com.kaltura.client.KalturaClient;
import com.kaltura.client.KalturaConfiguration;
import com.kaltura.client.enums.KalturaSessionType;

/**
 * This class centralizes the session configuration 
 */
public class SessionConfig {
	
	/** The partner who is executing this client */
	public static final int KALTURA_PARTNER_ID = PARTNER_ID;
	/** The secret of the indicated partner */
	public static final String KALTURA_ADMIN_SECRET = "KALTURA_ADMIN_SECRET";
	/** Kaltura service url - the end point*/
	public static final String KALTURA_SERVICE_URL = "END-POINT";
	
	/**
	 * This function generates Kaltura Client according to the given ids
	 * @param sessionType KalturaSessionType - whether the session is admin or user session
	 * @param userId String - The user ID.
	 * @param sessionExpiry int - The session expire value. 
	 * @param sessionPrivileges String - The session privileges. 
	 * @return The generated client
	 * @throws Exception In case the client generation failed for some reason.
	 */
	public static KalturaClient getClient(KalturaSessionType sessionType, String userId, int sessionExpiry, String sessionPrivileges) throws Exception {
		
		// Create KalturaClient object using the accound configuration
		KalturaConfiguration config = new KalturaConfiguration();
		config.setPartnerId(KALTURA_PARTNER_ID);
		config.setEndpoint(KALTURA_SERVICE_URL);
		KalturaClient client = new KalturaClient(config);
		
		// Generate KS string locally, without calling the API
		String ks = client.generateSession(
			KALTURA_ADMIN_SECRET,
			userId,
			sessionType,
			config.getPartnerId(),
			sessionExpiry,
			sessionPrivileges
		);
		client.setSessionId(ks);
		
		// Returns the KalturaClient object
		return client;
	}
}
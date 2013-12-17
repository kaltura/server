package lib.Kaltura.notification;

import lib.Kaltura.config.SessionConfig;
import lib.Kaltura.output.Console;

import com.kaltura.client.KalturaApiException;
import com.kaltura.client.KalturaClient;
import com.kaltura.client.enums.KalturaSessionType;
import com.kaltura.client.types.KalturaHttpNotification;

/**
 * This class is a base class for all the notification handlers 
 */
public abstract class BaseNotificationHandler {

	/** Kaltura client */
	private static KalturaClient apiClient = null;
	
	/** The console this handler use*/
	protected Console console;
	
	/**
	 * Constructor
	 * @param console
	 */
	public BaseNotificationHandler(Console console) {
		this.console = console;
	}

	/**
	 * @return The Kaltura client
	 * @throws Exception
	 */
	protected static KalturaClient getClient() {
		if (apiClient == null) {
			// Generates the Kaltura client. The parameters can be changed according to the need
			try {
				apiClient = SessionConfig.getClient(KalturaSessionType.ADMIN, "", 86400, "");
			} catch (Exception e) {
				throw new NotificationHandlerException("Failed to generate client : " + e.getMessage(), NotificationHandlerException.ERROR_PROCESSING);
			}
		}
		return apiClient;
	}

	/**
	 * This function decides whether this handle should handle the notification
	 * @param httpNotification The notification that is considered to be handled
	 * @return Whether this handler should handle this notification
	 */
	abstract public boolean shouldHandle(KalturaHttpNotification httpNotification);

	/**
	 * The handling function. 
	 * @param httpNotification The notification that should be handled
	 * @throws KalturaApiException In case something bad happened
	 */
	abstract public void handle(KalturaHttpNotification httpNotification);

	/**
	 * @return The notification processing timing
	 */
	public HandlerProcessType getType() {
		return HandlerProcessType.PROCESS;
	}
}
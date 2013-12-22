package lib.Kaltura.notification;

/**
 * Thrown notification errors
 */
public class NotificationHandlerException extends RuntimeException 
{
	private static final long serialVersionUID = 3044454799498802773L;
	
	public static final int ERROR_GENERIC = -1;
	public static final int ERROR_WRONG_SIGNATURE = -2;
	public static final int ERROR_REQUIRED_ADMIN_SECRET = -3;
	public static final int ERROR_PROCESSING = -4;
	public static final int ERROR_INVALID_TYPE = -5;
	
	private int code;

	public NotificationHandlerException(String message, int code) {
		super(message);
		this.code = code;
	}
	
	public int getCode() {
		return code;
	}
}


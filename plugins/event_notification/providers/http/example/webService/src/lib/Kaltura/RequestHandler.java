package lib.Kaltura;

import java.math.BigInteger;
import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;

import lib.Kaltura.notification.NotificationHandlerException;

public class RequestHandler {
	
	/**
	 * This function validates the input signature as received from the server
	 * @param data The data from the server
	 * @param secret The admin secret
	 * @param signature The signature as the server passed
	 */
	public static void validateSignature(String data, String secret, String signature) {
		String originalText = data+secret;
		
		MessageDigest mdEnc = null;
		try {
			mdEnc = MessageDigest.getInstance("MD5");
		} catch (NoSuchAlgorithmException e) {
			 throw new NotificationHandlerException("Can'e validate signature. unknown algorithm.", NotificationHandlerException.ERROR_WRONG_SIGNATURE);
		}
		mdEnc.update(originalText.getBytes(), 0, originalText.length());
		String md5 = new BigInteger(1, mdEnc.digest()).toString(16); // Encrypted
																		// string
		if(!md5.equals(signature))
			throw new NotificationHandlerException("The signature validation failed!", NotificationHandlerException.ERROR_WRONG_SIGNATURE);
	}
	
}

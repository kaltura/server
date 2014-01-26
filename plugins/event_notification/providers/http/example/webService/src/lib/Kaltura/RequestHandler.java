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
		String originalText = secret + data;
		
		MessageDigest algorithm = null;
		try {
			
			algorithm = MessageDigest.getInstance("SHA1");
		} catch (NoSuchAlgorithmException e) {
			 throw new NotificationHandlerException("Can't validate signature. unknown algorithm.", NotificationHandlerException.ERROR_WRONG_SIGNATURE);
		}
		
		algorithm.reset();
		algorithm.update(originalText.getBytes());
		
		String calculatedSig = new BigInteger(1,algorithm.digest()).toString(16); 
		calculatedSig = String.format("%40s", calculatedSig).replace(' ', '0');	// Restore leading zeros
		System.out.println("@_!! /" + calculatedSig + "/\t/" + signature +"/");
		if(!calculatedSig.equals(signature))
			throw new NotificationHandlerException("The signature validation failed!", NotificationHandlerException.ERROR_WRONG_SIGNATURE);
	}
	
}

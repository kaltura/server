package com.kaltura.client;

/**
 * Common ancestor for all generated classes in the com.kaltura.client.services package.
 * 
 * @author jpotts
 *
 */
public class KalturaServiceBase {

	protected KalturaClient kalturaClient;
	
	public KalturaServiceBase() {		
	}
	
	public KalturaServiceBase(KalturaClient kalturaClient) {
		this.kalturaClient = kalturaClient;
	}
	
	public void setKalturaClient(KalturaClient kalturaClient) {
		this.kalturaClient = kalturaClient;
	}
	
}

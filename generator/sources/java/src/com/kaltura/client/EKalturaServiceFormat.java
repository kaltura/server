package com.kaltura.client;

public enum EKalturaServiceFormat {

	RESPONSE_TYPE_JSON(1),
	RESPONSE_TYPE_XML(2),
	RESPONSE_TYPE_PHP(3),
	RESPONSE_TYPE_PHP_ARRAY(4),
	RESPONSE_TYPE_PHP_OBJECT(5),
	RESPONSE_TYPE_RAW(6),
	RESPONSE_TYPE_HTML(7);
	
	private int hashCode;
	
	EKalturaServiceFormat(int hashCode) {
		this.hashCode = hashCode;
	}
    
	public int getHashCode() {
		return this.hashCode;
	}
}

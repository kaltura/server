package com.kaltura.client;

public class KalturaApiException extends Exception {

	private static final long serialVersionUID = 6710104690443289367L;

	public KalturaApiException() {
		super();
	}
	
	public KalturaApiException(String message) {
		super(message);
	}
}

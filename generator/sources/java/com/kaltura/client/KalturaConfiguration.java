package com.kaltura.client;

import java.util.HashMap;
import java.util.Map;

/**
 * This class holds information needed by the Kaltura client to establish a session.
 * 
 * @author jpotts
 *
 */
public class KalturaConfiguration {
	protected int partnerId;
	protected String secret;
	protected String adminSecret;	
	protected String endpoint;
	protected int timeout = 100000;
	protected String clientTag = "java";
    protected EKalturaServiceFormat serviceFormat = EKalturaServiceFormat.RESPONSE_TYPE_XML;
	
	private Map<String, String> params;
	
	public KalturaConfiguration() {		
	}
	
	public Map<String, String> getMap ()	{
		params = new HashMap<String, String>();
		params.put ( "partner_id" , new Integer(partnerId).toString());
		
		return params;
	}

	public int getPartnerId() {
		return partnerId;
	}

	public void setPartnerId(int partnerId) {
		this.partnerId = partnerId;
	}

	public String getSecret() {
		return secret;
	}

	public void setSecret(String secret) {
		this.secret = secret;
	}

	public String getAdminSecret() {
		return adminSecret;
	}

	public void setAdminSecret(String adminSecret) {
		this.adminSecret = adminSecret;
	}

	public String getEndpoint() {
		return endpoint;
	}

	public void setEndpoint(String endpoint) {
		this.endpoint = endpoint;
	}

	public Map<String, String> getParams() {
		return params;
	}

	public void setParams(Map<String, String> params) {
		this.params = params;
	}

	public String getClientTag() {
		return clientTag;
	}

	public void setClientTag(String clientTag) {
		this.clientTag = clientTag;
	}

	public EKalturaServiceFormat getServiceFormat() {
		return serviceFormat;
	}

	public void setServiceFormat(EKalturaServiceFormat serviceFormat) {
		this.serviceFormat = serviceFormat;
	}

	public int getTimeout() {
		return timeout;
	}

	public void setTimeout(int timeout) {
		this.timeout = timeout;
	}
	
}

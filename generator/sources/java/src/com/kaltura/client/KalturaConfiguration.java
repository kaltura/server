// ===================================================================================================
//                           _  __     _ _
//                          | |/ /__ _| | |_ _  _ _ _ __ _
//                          | ' </ _` | |  _| || | '_/ _` |
//                          |_|\_\__,_|_|\__|\_,_|_| \__,_|
//
// This file is part of the Kaltura Collaborative Media Suite which allows users
// to do with audio, video, and animation what Wiki platfroms allow them to do with
// text.
//
// Copyright (C) 2006-2011  Kaltura Inc.
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Affero General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Affero General Public License for more details.
//
// You should have received a copy of the GNU Affero General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// @ignore
// ===================================================================================================
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

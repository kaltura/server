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

import java.io.UnsupportedEncodingException;
import java.net.URLEncoder;
import java.util.ArrayList;
import java.util.HashMap;

import com.kaltura.client.enums.KalturaEnumAsInt;
import com.kaltura.client.enums.KalturaEnumAsString;

/**
 * Helper class that provides a collection of Kaltura parameters (key-value
 * pairs).
 * 
 * @author jpotts
 * 
 */
public class KalturaParams extends HashMap<String, String> {
	
	private static final String PARAMS_SEPERATOR = ":";

	private static final long serialVersionUID = 6630046786691120850L;

	private static final String ENCODING = "UTF-8";
	
	public String toQueryString() throws KalturaApiException {

		try {
			StringBuffer str = new StringBuffer();

			boolean isFirst = true;
			for (java.util.Map.Entry<String, String> itr : this.entrySet()) {
				if(isFirst) {
					isFirst = false;
				} else {
					str.append("&");
				}
				str.append(itr.getKey());
				str.append("=");
				str.append(URLEncoder.encode(itr.getValue(), ENCODING));
			}

			return str.toString();
		} catch (UnsupportedEncodingException e) {
			throw new KalturaApiException("Failed to generate query string");
		}
	}

	public void add(String key, int value) {
		if (value == KalturaParamsValueDefaults.KALTURA_UNDEF_INT)
			return;
		if (value == KalturaParamsValueDefaults.KALTURA_NULL_INT)
			putNull(key);
		else
			this.put(key, Integer.toString(value));
	}

	public void add(String key, float value) {
		if (value == KalturaParamsValueDefaults.KALTURA_UNDEF_FLOAT)
			return;
		if (value == KalturaParamsValueDefaults.KALTURA_NULL_FLOAT)
			putNull(key);
		else
			this.put(key, Float.toString(value));
	}

	public void add(String key, String value) {
		if (value == null)
			return;
		if (value.equals(KalturaParamsValueDefaults.KALTURA_NULL_STRING))
			putNull(key);
		else
			this.put(key, value);
	}
	
	public void add(String key, KalturaObjectBase object) {
		if (object == null)
			return;

		for (java.util.Map.Entry<String, String> itr : object.toParams().entrySet()) {
			this.put(key + PARAMS_SEPERATOR + itr.getKey(), itr.getValue());
		}
	}

	public <T extends KalturaObjectBase> void add(String key, ArrayList<T> array) {
		if (array == null)
			return;

		int index = 0;
		for (KalturaObjectBase baseObj : array) {
			for (java.util.Map.Entry<String, String> itr : baseObj.toParams().entrySet()) {
				this.put(key + PARAMS_SEPERATOR + index + PARAMS_SEPERATOR
						+ itr.getKey(), itr.getValue());
			}
			index++;
		}

		if (array.isEmpty()) {
			this.put(key + PARAMS_SEPERATOR + "-", "");
		}
	}
	
	public void add(KalturaParams objectProperties) {
		this.putAll(objectProperties);
	}
	
	public void addMulti(int idx, KalturaParams objectProperties) {
		for (java.util.Map.Entry<String, String> itr : objectProperties.entrySet()) {
			this.put(Integer.toString(idx) + PARAMS_SEPERATOR + itr.getKey(), itr.getValue());           
		}
	}
	
	public void addMulti(int idx, String key, String value) {
		this.put(Integer.toString(idx) + PARAMS_SEPERATOR + key, value);
	}

	protected void putNull(String key) {
		this.put(key + "__null", "");
	}
	
	/**
	 * Pay attention - this function does not check if the value is null.
	 * neither it supports setting value to null.
	 */
	public void add(String key, boolean value) {
		this.put(key, value ? "1" : "0");
	}
	
	/**
	 * Pay attention - this function does not support setting value to null.
	 */
	public void add(String key, KalturaEnumAsString value) {
		if(value == null) 
			return;
		
		add(key, value.getHashCode());
	}
	
	/**
	 * Pay attention - this function does not support setting value to null.
	 */
	public void add(String key, KalturaEnumAsInt value) {
		if(value == null) 
			return;
		
		add(key, value.getHashCode());
	}
	
}

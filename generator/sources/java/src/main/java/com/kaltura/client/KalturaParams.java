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

import javax.json.Json;
import javax.json.JsonObjectBuilder;
import javax.json.JsonString;
import javax.json.JsonValue;

import com.kaltura.client.enums.KalturaEnumAsInt;
import com.kaltura.client.enums.KalturaEnumAsString;

/**
 * Helper class that provides a collection of Kaltura parameters (key-value
 * pairs).
 * 
 * @author jpotts
 * 
 */
public class KalturaParams extends HashMap<String, IKalturaParam> implements IKalturaParam {
	
	private static final long serialVersionUID = 6630046786691120850L;

	private static final String ENCODING = "UTF-8";
	
	class StringParam implements JsonString, IKalturaParam{
		public String value;

		public StringParam(String stringValue){
			value = stringValue;
		}
		
		@Override
		public Object toQueryString(String key) throws KalturaApiException {
			StringBuffer str = new StringBuffer();
			str.append(key);
			str.append("=");
			
			try {
				str.append(URLEncoder.encode(value, ENCODING));
			} catch (UnsupportedEncodingException e) {
				throw new KalturaApiException("Failed to generate query string");
			}
			
			return str.toString();
		}

		@Override
		public JsonValue toJsonObject() {
			return this;
		}

		@Override
		public ValueType getValueType() {
			return JsonValue.ValueType.STRING;
		}

		@Override
		public String getString() {
			return value;
		}

		@Override
		public CharSequence getChars() {
			return value;
		}
	}
	
	public String toQueryString() throws KalturaApiException {
		return toQueryString(null);
	}
	
	public String toQueryString(String prefix) throws KalturaApiException {

		StringBuffer str = new StringBuffer();
		IKalturaParam value;
		for (String key : keySet()) {
			if(str.length() > 0) {
				str.append("&");
			}
			
			value = get(key);
			if(prefix != null){
				key = prefix + "[" + key + "]";
			}
				
			str.append(value.toQueryString(key));
		}

		return str.toString();
	}

	public String toJson() {
		return toJsonObject().toString();
	}

	public JsonValue toJsonObject() {
		JsonObjectBuilder jsonBuilder = Json.createObjectBuilder();
		IKalturaParam value;
		for (String key : this.keySet()) {
			value = get(key);
			jsonBuilder.add(key, value.toJsonObject());
		}
		return jsonBuilder.build();
	}
	
	public void add(String key, int value) {
		if (value == KalturaParamsValueDefaults.KALTURA_UNDEF_INT)
			return;
		if (value == KalturaParamsValueDefaults.KALTURA_NULL_INT)
			putNull(key);
		else
			this.put(key, new StringParam(Integer.toString(value)));
	}
	
	public void add(String key, long value) {
		if (value == KalturaParamsValueDefaults.KALTURA_UNDEF_LONG)
			return;
		if (value == KalturaParamsValueDefaults.KALTURA_NULL_LONG)
			putNull(key);
		else
			this.put(key, new StringParam(Long.toString(value)));
	}

	public void add(String key, double value) {
		if (value == KalturaParamsValueDefaults.KALTURA_UNDEF_DOUBLE)
			return;
		if (value == KalturaParamsValueDefaults.KALTURA_NULL_DOUBLE)
			putNull(key);
		else
			this.put(key, new StringParam(Double.toString(value)));
	}

	public void add(String key, String value) {
		if (value == null)
			return;
		if (value.equals(KalturaParamsValueDefaults.KALTURA_NULL_STRING))
			putNull(key);
		else
			this.put(key, new StringParam(value));
	}
	
	public void add(String key, KalturaObjectBase object) {
		if (object == null)
			return;
		
		this.put(key, object.toParams());
	}

	public <T extends KalturaObjectBase> void add(String key, ArrayList<T> array) {
		if (array == null)
			return;


		if (array.isEmpty()) {
			KalturaParams emptyParams = new KalturaParams();
			emptyParams.put("-", new StringParam(""));
			this.put(key, emptyParams);
		}
		else{
			KalturaParams arrayParams = new KalturaParams();
			int index = 0;
			for (KalturaObjectBase baseObj : array) {
				arrayParams.add(Integer.toString(index), baseObj);
				index++;
			}
			this.put(key, arrayParams);
		}
	}

	public <T extends KalturaObjectBase> void add(String key, HashMap<String, T> map) {
		if (map == null)
			return;

		if (map.isEmpty()) {
			KalturaParams emptyParams = new KalturaParams();
			emptyParams.put("-", new StringParam(""));
			this.put(key, emptyParams);
		}
		else{
			KalturaParams mapParams = new KalturaParams();
			for (String itemKey : map.keySet()) {
				KalturaObjectBase baseObj = map.get(itemKey);
				mapParams.add(itemKey, baseObj);
			}
			this.put(key, mapParams);
		}
	}
	
	public <T extends KalturaObjectBase> void add(String key, IKalturaParam params) {
		if(params instanceof KalturaParams && containsKey(key) && get(key) instanceof KalturaParams){
			KalturaParams existingParams = (KalturaParams) get(key);
			existingParams.putAll((KalturaParams) params);
		}
		else{
			put(key, params);
		}
	}
	
	public void add(KalturaParams objectProperties) {
		this.putAll(objectProperties);
	}
	
	protected void putNull(String key) {
		this.put(key + "__null", new StringParam(""));
	}
	
	/**
	 * Pay attention - this function does not check if the value is null.
	 * neither it supports setting value to null.
	 */
	public void add(String key, boolean value) {
		this.put(key, new StringParam(value ? "1" : "0"));
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

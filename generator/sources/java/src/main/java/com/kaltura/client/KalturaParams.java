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

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Iterator;

import org.json.JSONException;
import org.json.JSONObject;

import com.kaltura.client.enums.KalturaEnumAsInt;
import com.kaltura.client.enums.KalturaEnumAsString;

/**
 * Helper class that provides a collection of Kaltura parameters (key-value
 * pairs).
 * 
 * @author jpotts
 * 
 */
public class KalturaParams extends JSONObject {

	public String toQueryString() throws KalturaApiException {
		return toQueryString(null);
	}
	
	public String toQueryString(String prefix) throws KalturaApiException {

		StringBuffer str = new StringBuffer();
		Object value;
		String key;
		for (Object keyObject : keySet()) {
			key = (String) keyObject;
			if(str.length() > 0) {
				str.append("&");
			}
			
			try {
				value = get(key);
			} catch (JSONException e) {
				throw new KalturaApiException(e.getMessage());
			}
			
			if(prefix != null){
				key = prefix + "[" + key + "]";
			}
			if(value instanceof KalturaParams){
				str.append(((KalturaParams) value).toQueryString(key));
			}
			else{
				str.append(key);
				str.append("=");
				str.append(value);
			}
		}

		return str.toString();
	}
	
	public void add(String key, int value) throws KalturaApiException {
		if (value == KalturaParamsValueDefaults.KALTURA_UNDEF_INT)
		{
			return;
		}
		
		if (value == KalturaParamsValueDefaults.KALTURA_NULL_INT)
		{
			putNull(key);
			return;
		}

		try {
			put(key, value);
		} catch (JSONException e) {
			throw new KalturaApiException(e.getMessage());
		}
	}
	
	public void add(String key, long value) throws KalturaApiException {
		if (value == KalturaParamsValueDefaults.KALTURA_UNDEF_LONG)
		{
			return;
		}
		if (value == KalturaParamsValueDefaults.KALTURA_NULL_LONG)
		{
			putNull(key);
			return;
		}
		
		try {
			put(key, value);
		} catch (JSONException e) {
			throw new KalturaApiException(e.getMessage());
		}
	}

	public void add(String key, double value) throws KalturaApiException {
		if (value == KalturaParamsValueDefaults.KALTURA_UNDEF_DOUBLE)
		{
			return;
		}
		if (value == KalturaParamsValueDefaults.KALTURA_NULL_DOUBLE)
		{
			putNull(key);
			return;
		}
		
		try {
			put(key, value);
		} catch (JSONException e) {
			throw new KalturaApiException(e.getMessage());
		}
	}

	public void add(String key, String value) throws KalturaApiException {
		if (value == null)
		{
			return;
		}
		
		if (value.equals(KalturaParamsValueDefaults.KALTURA_NULL_STRING))
		{
			putNull(key);
			return;
		}
		
		try {
			put(key, value);
		} catch (JSONException e) {
			throw new KalturaApiException(e.getMessage());
		}
	}
	
	public void add(String key, KalturaObjectBase object) throws KalturaApiException {
		if (object == null)
			return;
		
		try {
			put(key, object.toParams());
		} catch (JSONException e) {
			throw new KalturaApiException(e.getMessage());
		}
	}

	public <T extends KalturaObjectBase> void add(String key, ArrayList<T> array) throws KalturaApiException {
		if (array == null)
			return;


		if (array.isEmpty()) {
			KalturaParams emptyParams = new KalturaParams();
			try {
				emptyParams.put("-", "");
				put(key, emptyParams);
			} catch (JSONException e) {
				throw new KalturaApiException(e.getMessage());
			}
		}
		else{
			KalturaParams arrayParams = new KalturaParams();
			int index = 0;
			for (KalturaObjectBase baseObj : array) {
				arrayParams.add(Integer.toString(index), baseObj);
				index++;
			}
			try {
				put(key, arrayParams);
			} catch (JSONException e) {
				throw new KalturaApiException(e.getMessage());
			}
		}
	}

	public <T extends KalturaObjectBase> void add(String key, HashMap<String, T> map) throws KalturaApiException {
		if (map == null)
			return;

		if (map.isEmpty()) {
			KalturaParams emptyParams = new KalturaParams();
			try {
				emptyParams.put("-", "");
				put(key, emptyParams);
			} catch (JSONException e) {
				throw new KalturaApiException(e.getMessage());
			}
		}
		else{
			KalturaParams mapParams = new KalturaParams();
			for (String itemKey : map.keySet()) {
				KalturaObjectBase baseObj = map.get(itemKey);
				mapParams.add(itemKey, baseObj);
			}
			try {
				put(key, mapParams);
			} catch (JSONException e) {
				throw new KalturaApiException(e.getMessage());
			}
		}
	}
	
	public <T extends KalturaObjectBase> void add(String key, KalturaParams params) throws KalturaApiException {
		try{
			if(params instanceof KalturaParams && has(key) && get(key) instanceof KalturaParams){
				KalturaParams existingParams = (KalturaParams) get(key);
				existingParams.putAll((KalturaParams) params);
			}
			else{
				put(key, params);
			}
		} catch (JSONException e) {
			throw new KalturaApiException(e.getMessage());
		}
	}

	public Iterable<String> keySet() {
		return new Iterable<String>() {
			@SuppressWarnings("unchecked")
			public Iterator<String> iterator() {
				return keys();
			}
		};
	}

	private void putAll(KalturaParams params) throws KalturaApiException {
		for(Object key : params.keySet()){
			String keyString = (String) key;
			try {
				put(keyString, params.get(keyString));
			} catch (JSONException e) {
				throw new KalturaApiException(e.getMessage());
			}
		}
	}

	public void add(KalturaParams objectProperties) throws KalturaApiException {
		putAll(objectProperties);
	}
	
	protected void putNull(String key) throws KalturaApiException {
		try {
			put(key + "__null", "");
		} catch (JSONException e) {
			throw new KalturaApiException(e.getMessage());
		}
	}
	
	/**
	 * Pay attention - this function does not check if the value is null.
	 * neither it supports setting value to null.
	 */
	public void add(String key, boolean value) throws KalturaApiException {
		try {
			put(key, value);
		} catch (JSONException e) {
			throw new KalturaApiException(e.getMessage());
		}
	}
	
	/**
	 * Pay attention - this function does not support setting value to null.
	 */
	public void add(String key, KalturaEnumAsString value) throws KalturaApiException {
		if(value == null) 
			return;
		
		add(key, value.getHashCode());
	}
	
	/**
	 * Pay attention - this function does not support setting value to null.
	 */
	public void add(String key, KalturaEnumAsInt value) throws KalturaApiException {
		if(value == null) 
			return;
		
		add(key, value.getHashCode());
	}

	public boolean containsKey(String key) {
		return has(key);
	}

	public void clear() {
		for(Object key : keySet()){
			remove((String) key);
		}
	}

	public KalturaParams getParams(String key) throws KalturaApiException {
		if(!has(key))
			return null;
		
		Object value;
		try {
			value = get(key);
		} catch (JSONException e) {
			throw new KalturaApiException(e.getMessage());
		}
		if(value instanceof KalturaParams)
			return (KalturaParams) value;
		
		throw new KalturaApiException("Key value [" + key + "] is not instance of KalturaParams");
	}
	
}

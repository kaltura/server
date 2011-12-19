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

import java.net.URLEncoder;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Iterator;
import java.util.Set;

/**
 * Helper class that provides a collection of Kaltura parameters (key-value pairs).
 * 
 * @author jpotts
 *
 */
public class KalturaParams extends HashMap<String, String> {

	private static final long serialVersionUID = 6630046786691120850L;

	public String toQueryString() {
		String str = "";
		
		Set<String> s = this.keySet();
		Iterator<String> it = s.iterator( );
		while (it.hasNext()) {
			String name = (String)it.next();
			String value = (String)this.get(name);
			try {
				str += (name + "=" + URLEncoder.encode (value, "UTF-8") + "&");
			} catch (Exception e) {
				//TODO handle this
			}
		}
		
        if (str.endsWith("&"))
            str = str.substring(0, str.length() - 1);

        return str;        
	}
	
	public void add(String objectName, KalturaParams objectProperties) {
        for (String key : objectProperties.keySet()) {
            this.put(objectName + ":" + key, objectProperties.get(key));            
        }
    }

	public void add(KalturaParams objectProperties) {
		this.putAll(objectProperties);
    }

	public void setString(String key, String value) {
		if(this.get(key) != null)
			this.remove(key);
        this.put(key, value);
    }
	
	public void addObjectIfNotNull(String key, KalturaObjectBase object) {
		if (object == null)
			return;
		
		KalturaParams params = object.toParams();
		Set<String> s = params.keySet();
		Iterator<String> it = s.iterator( );
		while (it.hasNext()) {
			String name = (String)it.next();
			String value = (String)params.get(name);
			this.put(key + ":" + name, value);
		}
    }
	
	public void addObjectIfNotNull(String key, ArrayList array) {
		if (array == null)
			return;
		
		int index = 0;
		for(KalturaObjectBase object : (ArrayList<KalturaObjectBase>)array)
		{
			KalturaParams params = object.toParams();
			Set<String> s = params.keySet();
			Iterator<String> it = s.iterator( );
			while (it.hasNext()) {
				String name = (String)it.next();
				String value = (String)params.get(name);
				this.put(key + ":" + index + ":" + name, value);
			}
			index++;
		}
		if (array.isEmpty())
		{
			this.put(key + ":-", "");
		}
    }

	public void addStringIfNotNull(String key, String value) {
        if (value != null) this.put(key, value);
    }

	public void addIntIfNotNull(String key, int value) {
        if (value != Integer.MIN_VALUE) this.put(key, Integer.toString(value));
    }
	
	public void addFloatIfNotNull(String key, float value) {
        if (value != Float.MIN_VALUE) this.put(key, Float.toString(value));
    }
	
    public void addBoolIfNotNull(String key, boolean value) {
        this.put(key, value ? "1" : "0");
    }
}

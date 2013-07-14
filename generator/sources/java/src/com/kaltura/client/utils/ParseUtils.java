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
package com.kaltura.client.utils;

import java.util.ArrayList;

import org.w3c.dom.Element;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;

import com.kaltura.client.KalturaApiException;
import com.kaltura.client.KalturaObjectFactory;
import com.kaltura.client.KalturaLogger;

public final class ParseUtils {
	
	private static KalturaLogger logger = KalturaLogger.getLogger(ParseUtils.class);
	
	public static String parseString(String txt) {
		 return txt;
	}

	public static int parseInt(String txt) {
		if (txt.length() != 0) {
			try {
				return Integer.parseInt(txt);
			} catch (NumberFormatException nfe) {
				if (logger.isEnabled())
					logger.warn("Failed to parse [" + txt + "] as int", nfe);
			}
		}
		return 0;
	}
	
	public static float parseFloat(String txt) {
		if (txt.length() != 0) {
			try {
				return Float.parseFloat(txt);
			} catch (NumberFormatException nfe) {
				if (logger.isEnabled())
					logger.warn("Failed to parse [" + txt + "] as float", nfe);
			}
		}
		return 0;
	}
	
	public static boolean parseBool(String txt) {
		 return txt.equals("0") ? false : true;
	}
	
	@SuppressWarnings("unchecked")
	public static <T> ArrayList<T> parseArray(Class<T> clz, Node aNode) throws KalturaApiException{
		ArrayList<T> tmpList = new ArrayList<T>();
		NodeList subNodeList = aNode.getChildNodes();
		for (int j = 0; j < subNodeList.getLength(); j++) {
			Node arrayNode = subNodeList.item(j);
			tmpList.add((T) KalturaObjectFactory.create((Element) arrayNode, clz));
		}
		return tmpList;
	}

	@SuppressWarnings("unchecked")
	public static <T> T parseObject(Class<T> clz, Node aNode) throws KalturaApiException{
		 return (T) KalturaObjectFactory.create((Element)aNode, clz);
	}
	
}

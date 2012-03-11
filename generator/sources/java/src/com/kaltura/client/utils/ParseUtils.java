package com.kaltura.client.utils;

import java.util.ArrayList;

import org.apache.log4j.Logger;
import org.w3c.dom.Element;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;

import com.kaltura.client.KalturaApiException;
import com.kaltura.client.KalturaObjectFactory;

public final class ParseUtils {
	
	private static Logger logger = Logger.getLogger(ParseUtils.class);
	
	public static String parseString(String txt) {
		 return txt;
	}

	public static int parseInt(String txt) {
		if (!txt.isEmpty()) {
			try {
				return Integer.parseInt(txt);
			} catch (NumberFormatException nfe) {
				logger.warn("Failed to parse [" + txt + "] as int", nfe);
			}
		}
		return 0;
	}
	
	public static float parseFloat(String txt) {
		if (!txt.isEmpty()) {
			try {
				return Float.parseFloat(txt);
			} catch (NumberFormatException nfe) {
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

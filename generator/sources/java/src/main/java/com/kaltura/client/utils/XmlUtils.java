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

import java.io.CharArrayReader;
import java.io.IOException;
import java.io.Reader;

import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;
import javax.xml.xpath.XPath;
import javax.xml.xpath.XPathConstants;
import javax.xml.xpath.XPathExpressionException;
import javax.xml.xpath.XPathFactory;

import org.w3c.dom.Document;
import org.w3c.dom.Element;
import org.xml.sax.SAXException;

import com.kaltura.client.KalturaApiException;

public class XmlUtils {
	public static Element parseXml(String xml) throws KalturaApiException {
		//get the factory
		DocumentBuilderFactory dbf = DocumentBuilderFactory.newInstance();

		try {

			//Using factory get an instance of document builder
			DocumentBuilder db = dbf.newDocumentBuilder();

			//parse using builder to get DOM representation of the XML file
			//Document dom = db.parse(new ByteArrayInputStream(xml.getBytes()));
			Reader reader= new CharArrayReader(xml.toCharArray());
			Document dom = db.parse(new org.xml.sax.InputSource(reader)); 

			Element docEle = dom.getDocumentElement();
			
			return docEle;
			
		} catch(ParserConfigurationException pce) {
			throw new KalturaApiException("Failed building XML parser");
		} catch(SAXException se) {
			throw new KalturaApiException("Failed while parsing response.");
		} catch(IOException ioe) {
			throw new KalturaApiException("I/O exception while reading response");
		}
	}
		
	public static boolean hasChildren (Element e) {
		if (e == null) return false;
		return e.getFirstChild() != null;
	}

	public static Element getElementByXPath(Element e, String xPathExpression) throws XPathExpressionException {
    	XPathFactory factory=XPathFactory.newInstance();
        XPath xPath=factory.newXPath();
                
        return (Element)(xPath.evaluate(xPathExpression, e, XPathConstants.NODE));
        		
	}
}

package com.kaltura.client.utils;

import java.io.ByteArrayInputStream;
import java.io.CharArrayReader;
import java.io.IOException;
import java.io.Reader;

import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;
import javax.xml.transform.TransformerException;
import javax.xml.xpath.XPath;
import javax.xml.xpath.XPathConstants;
import javax.xml.xpath.XPathExpressionException;
import javax.xml.xpath.XPathFactory;

import org.w3c.dom.Document;
import org.w3c.dom.Element;
import org.w3c.dom.Node;
import org.w3c.dom.traversal.NodeIterator;
import org.xml.sax.SAXException;

import com.kaltura.client.KalturaApiException;
import com.sun.org.apache.xpath.internal.XPathAPI;

public class XmlUtils {
	public static Element parseXml(String xml) {
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
			//pce.printStackTrace();
		} catch(SAXException se) {
			//se.printStackTrace();
		} catch(IOException ioe) {
			//ioe.printStackTrace();
		}
		
		return null;
	}
	
	public static  String getTextValue(Element ele, String tagName) {
		String textVal = null;
		
		try {
			NodeIterator ni = XPathAPI.selectNodeIterator(ele, "//" + tagName);
			
			if (ni != null) {
				Element el = (Element)ni.nextNode();
				if (el != null)	{
					Node child = el.getFirstChild();
					if (child != null) textVal = child.getNodeValue();
				}
			}

		} catch (TransformerException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		
		return textVal;
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

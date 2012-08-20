// ===================================================================================================
//						   _  __	 _ _
//						  | |/ /__ _| | |_ _  _ _ _ __ _
//						  | ' </ _` | |  _| || | '_/ _` |
//						  |_|\_\__,_|_|\__|\_,_|_| \__,_|
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

import com.kaltura.client.enums.KalturaSessionType;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.InputStream;
import java.math.BigInteger;
import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import java.util.ArrayList;
import java.util.List;
import java.util.Map.Entry;

import javax.xml.xpath.XPathExpressionException;

import org.apache.commons.codec.binary.Base64;
import org.apache.commons.httpclient.DefaultHttpMethodRetryHandler;
import org.apache.commons.httpclient.Header;
import org.apache.commons.httpclient.HttpClient;
import org.apache.commons.httpclient.HttpException;
import org.apache.commons.httpclient.HttpStatus;
import org.apache.commons.httpclient.ProxyHost;
import org.apache.commons.httpclient.methods.PostMethod;
import org.apache.commons.httpclient.methods.multipart.FilePart;
import org.apache.commons.httpclient.methods.multipart.MultipartRequestEntity;
import org.apache.commons.httpclient.methods.multipart.Part;
import org.apache.commons.httpclient.methods.multipart.PartSource;
import org.apache.commons.httpclient.methods.multipart.StringPart;
import org.apache.commons.httpclient.params.HttpConnectionManagerParams;
import org.apache.commons.httpclient.params.HttpMethodParams;
import org.w3c.dom.Element;

import com.kaltura.client.KalturaLogger;
import com.kaltura.client.utils.XmlUtils;

/**
 * Contains non-generated client logic. Includes the doQueue method which is responsible for
 * making HTTP calls to the Kaltura server.
 * 
 * @author jpotts
 *
 */
abstract public class KalturaClientBase {
	
	private static final String UTF8_CHARSET = "UTF-8";

	private static final String PARTNER_ID_PARAM_NAME = "partnerId";

	private static final int MAX_DEBUG_RESPONSE_STRING_LENGTH = 1024;
	protected KalturaConfiguration kalturaConfiguration;
	protected String sessionId;
	protected List<KalturaServiceActionCall> callsQueue;
	protected boolean isMultiRequest;
	protected KalturaParams multiRequestParamsMap;

	private static KalturaLogger logger = KalturaLogger.getLogger(KalturaClientBase.class);
	
	public KalturaClientBase() {		
	}
	
	public KalturaClientBase(KalturaConfiguration config) {
		this.kalturaConfiguration = config;
		this.callsQueue = new ArrayList<KalturaServiceActionCall>();
		this.multiRequestParamsMap = new KalturaParams();
	}
	
	abstract String getApiVersion();
	
	public String getSessionId() {
		return this.sessionId;
	}

	public void setSessionId(String sessionId) {
		this.sessionId = sessionId;
	}
					
	public boolean isMultiRequest() {
		return isMultiRequest;
	}

	public void setMultiRequest(boolean isMultiRequest) {
		this.isMultiRequest = isMultiRequest;
	}

	public void setKalturaConfiguration(KalturaConfiguration kalturaConfiguration) {
		this.kalturaConfiguration = kalturaConfiguration;
	}
	
	public void queueServiceCall(String service, String action, KalturaParams kparams) {
		this.queueServiceCall(service, action, kparams, new KalturaFiles());
	}

	public void queueServiceCall(String service, String action, KalturaParams kparams, KalturaFiles kfiles) {
		// in start session partner id is optional (default -1). if partner id was not set, use the one in the config
		if (!kparams.containsKey(PARTNER_ID_PARAM_NAME))
			kparams.add(PARTNER_ID_PARAM_NAME, this.kalturaConfiguration.getPartnerId());

		if (kparams.get(PARTNER_ID_PARAM_NAME).equals("-1"))
			kparams.add(PARTNER_ID_PARAM_NAME, this.kalturaConfiguration.getPartnerId());

		kparams.add("ks", this.sessionId);

		KalturaServiceActionCall call = new KalturaServiceActionCall(service, action, kparams, kfiles);
		this.callsQueue.add(call);
	}
	
	public String serve() throws KalturaApiException {
		
		KalturaParams kParams = new KalturaParams();
		String url = extractParamsFromCallQueue(kParams, new KalturaFiles());
		String kParamsString = kParams.toQueryString();
		url += "&" + kParamsString;
		
		return url;
	}

	public Element doQueue() throws KalturaApiException {
		if (this.callsQueue.isEmpty()) return null;

		if (logger.isEnabled())
			logger.debug("service url: [" + this.kalturaConfiguration.getEndpoint() + "]");

		KalturaParams kparams = new KalturaParams();
		KalturaFiles kfiles = new KalturaFiles();

		String url = extractParamsFromCallQueue(kparams, kfiles);

		if (logger.isEnabled())
			logger.debug("full reqeust url: [" + url + "?" + kparams.toQueryString() + "]");

		HttpClient client = createHttpClient();
		PostMethod method = createPostMethod(kparams, kfiles, url);
		String responseString = executeMethod(client, method);			
		Element responseXml = XmlUtils.parseXml(responseString);
		
		this.validateXmlResult(responseXml);
	  
		Element resultXml = null;
	   	resultXml = getElementByXPath(responseXml, "/xml/result");
		
		this.throwExceptionOnAPIError(resultXml);
		
		return resultXml;
	}

	protected String executeMethod(HttpClient client, PostMethod method) {
		String responseString = "";
		try {
			// Execute the method.
			int statusCode = client.executeMethod(method);

			if (logger.isEnabled())
			{
				Header[] headers = method.getRequestHeaders();
				for(Header header : headers)
					logger.debug("Header [" + header.getName() + " value [" + header.getValue() + "]");
			}
			
			if (logger.isEnabled() && statusCode != HttpStatus.SC_OK) {
				logger.error("Method failed: " + method.getStatusLine ( ));
			}

			// Read the response body.
			byte[] responseBody = method.getResponseBody ( );

			// Deal with the response.
			// Use caution: ensure correct character encoding and is not binary data
			responseString = new String (responseBody, UTF8_CHARSET); // Unicon: this MUST be set to UTF-8 charset -AZ
			if (logger.isEnabled())
			{
				if(responseString.length() < MAX_DEBUG_RESPONSE_STRING_LENGTH) {
					logger.debug(responseString);
				} else {
					logger.debug("Received long response. (length : " + responseString.length() + ")");
				}
			}
			
		} catch ( HttpException e ) {
			if (logger.isEnabled())
				logger.error( "Fatal protocol violation: " + e.getMessage ( ) ,e);
		} catch ( IOException e ) {
			if (logger.isEnabled())
				logger.error( "Fatal transport error: " + e.getMessage ( ), e);
		} finally {
			// Release the connection.
			method.releaseConnection ( );
		}
		return responseString;
	}

	private PostMethod createPostMethod(KalturaParams kparams,
			KalturaFiles kfiles, String url) {
		PostMethod method = new PostMethod(url);
		method.setRequestHeader("Accept","text/xml,application/xml,*/*");
		method.setRequestHeader("Accept-Charset","utf-8,ISO-8859-1;q=0.7,*;q=0.5");
		
		if (!kfiles.isEmpty()) {			
			method = this.getPostMultiPartWithFiles(method, kparams, kfiles);			
		} else {
			method = this.addParams(method, kparams);			
		}

		// Provide custom retry handler is necessary
		method.getParams().setParameter(HttpMethodParams.RETRY_HANDLER,
				new DefaultHttpMethodRetryHandler (3, false));
		return method;
	}

	private HttpClient createHttpClient() {
		HttpClient client = new HttpClient();

		// added by Unicon to handle proxy hosts
		String proxyHost = System.getProperty( "http.proxyHost" );
		if ( proxyHost != null ) {
			int proxyPort = -1;
			String proxyPortStr = System.getProperty( "http.proxyPort" );
			if (proxyPortStr != null) {
				try {
					proxyPort = Integer.parseInt( proxyPortStr );
				} catch (NumberFormatException e) {
					if (logger.isEnabled())
						logger.warn("Invalid number for system property http.proxyPort ("+proxyPortStr+"), using default port instead");
				}
			}
			ProxyHost proxy = new ProxyHost( proxyHost, proxyPort );
			client.getHostConfiguration().setProxyHost( proxy );
		}		
		// added by Unicon to force encoding to UTF-8
		client.getParams().setParameter(HttpMethodParams.HTTP_CONTENT_CHARSET, UTF8_CHARSET);
		client.getParams().setParameter(HttpMethodParams.HTTP_ELEMENT_CHARSET, UTF8_CHARSET);
		client.getParams().setParameter(HttpMethodParams.HTTP_URI_CHARSET, UTF8_CHARSET);
		
		HttpConnectionManagerParams connParams = client.getHttpConnectionManager().getParams();
		if(this.kalturaConfiguration.getTimeout() != 0) {
			connParams.setSoTimeout(this.kalturaConfiguration.getTimeout());
			connParams.setConnectionTimeout(this.kalturaConfiguration.getTimeout());
		}
		client.getHttpConnectionManager().setParams(connParams);
		return client;
	}

	private String extractParamsFromCallQueue(KalturaParams kparams, KalturaFiles kfiles) {
		
		String url = this.kalturaConfiguration.getEndpoint() + "/api_v3/index.php?service=";
		
		// append the basic params
		kparams.put("apiVersion", this.getApiVersion());
		kparams.put("clientTag", this.kalturaConfiguration.getClientTag());
		kparams.add("format", this.kalturaConfiguration.getServiceFormat());
		kparams.add("ignoreNull", true);
		
		if (isMultiRequest) {
			url += "multirequest";
			int i = 1;
			for (KalturaServiceActionCall call : this.callsQueue) {
				KalturaParams callParams = call.getParamsForMultiRequest(i);
				kparams.add(callParams);
				KalturaFiles callFiles = call.getFilesForMultiRequest(i);
				kfiles.add(callFiles);
				i++;
			}

			// map params
			for (String key : this.multiRequestParamsMap.keySet()) {
				String requestParam = key;
				String resultParam = this.multiRequestParamsMap.get(key);

				if (kparams.containsKey(requestParam)) {
					kparams.put(requestParam, resultParam);
				}
			}
			
			// Clean
			this.isMultiRequest = false;
			this.multiRequestParamsMap.clear();
			
		} else {
			KalturaServiceActionCall call = this.callsQueue.get(0);
			url += call.getService() + "&action=" + call.getAction();
			kparams.add(call.getParams());
			kfiles.add(call.getFiles());
		}
		
		// cleanup
		this.callsQueue.clear();
		
		kparams.put("sig", this.signature(kparams));
		return url;
	}

	public void startMultiRequest() {
		isMultiRequest = true;
	}

	public Element getElementByXPath(Element element, String xPath) throws KalturaApiException
	{
		try 
		{
			return XmlUtils.getElementByXPath(element, xPath);
		}
		catch (XPathExpressionException xee)
		{
			throw new KalturaApiException("XPath expression exception evaluating result");
		}
	}
	
	public List<KalturaObjectBase> createArray(Element arrayNode) throws KalturaApiException
	{
		List<KalturaObjectBase> list = new ArrayList<KalturaObjectBase>();
		for(int i = 0; i < arrayNode.getChildNodes().getLength(); i++)
		{
			Element node = (Element)arrayNode.getChildNodes().item(i);
			list.add((KalturaObjectBase)KalturaObjectFactory.create(node));
		}
		return list;
	}
	
	public KalturaMultiResponse doMultiRequest() throws KalturaApiException
	{
		Element multiRequestResult = doQueue();

		KalturaMultiResponse multiResponse = new KalturaMultiResponse();
	   
		for(int i = 0; i < multiRequestResult.getChildNodes().getLength(); i++) 
		{
			Element arrayNode = (Element)multiRequestResult.getChildNodes().item(i);
			
			try
			{
				KalturaApiException exception = getExceptionOnAPIError(arrayNode);
				if (exception != null)
				{
					multiResponse.add(exception);
				}	
				else if (getElementByXPath(arrayNode, "objectType") != null)
				{
			   		multiResponse.add(KalturaObjectFactory.create(arrayNode));
				}
				else if (getElementByXPath(arrayNode, "item/objectType") != null)
				{
			   		multiResponse.add(createArray(arrayNode));
				}
				else
				{
					multiResponse.add(arrayNode.getTextContent());
				}
			}
			catch (KalturaApiException e)
			{
				multiResponse.add(e);
			}
	   }
	   return multiResponse;
	}
	
	
	public void mapMultiRequestParam(int resultNumber, int requestNumber, String requestParamName) {
		this.mapMultiRequestParam(resultNumber, null, requestNumber, requestParamName);
	}

	public void mapMultiRequestParam(int resultNumber, String resultParamName, int requestNumber, String requestParamName) {
		String resultParam = "{" + resultNumber + ":result";
		if (resultParamName != null && resultParamName != "") resultParam += resultParamName;
		resultParam += "}";

		String requestParam = requestNumber + ":" + requestParamName;

		this.multiRequestParamsMap.put(requestParam, resultParam);
	}

	private String signature(KalturaParams kparams) {
		String str = "";
		for (String key : kparams.keySet()) {
			str += (key + kparams.get(key));
		}

		MessageDigest mdEnc = null;
		try {
			mdEnc = MessageDigest.getInstance("MD5");
		} catch (NoSuchAlgorithmException e) {
			e.printStackTrace();
		}		
		mdEnc.update(str.getBytes(), 0, str.length());
		String md5 = new BigInteger(1, mdEnc.digest()).toString(16); // Encrypted string
		
		return md5;
	}

	private void validateXmlResult(Element resultXml) throws KalturaApiException {
		
		Element resultElement = null;
   		resultElement = getElementByXPath(resultXml, "/xml/result");
						
		if (resultElement != null) {
			return;			
		} else {
			throw new KalturaApiException("Invalid result");
		}
	}

	private KalturaApiException getExceptionOnAPIError(Element result) throws KalturaApiException {
		Element errorElement = getElementByXPath(result, "error");
		if (errorElement == null)
		{
			return null;
		}
		
		Element messageElement = getElementByXPath(errorElement, "message");
		Element codeElement = getElementByXPath(errorElement, "code");
		if (messageElement == null || codeElement == null)
		{
			return null;
		}
		
		return new KalturaApiException(messageElement.getTextContent(),codeElement.getTextContent());
	}

	private void throwExceptionOnAPIError(Element result) throws KalturaApiException {
		KalturaApiException exception = getExceptionOnAPIError(result);
		if (exception != null)
		{
			throw exception;
		}
	}

	private PostMethod getPostMultiPartWithFiles(PostMethod method, KalturaParams kparams, KalturaFiles kfiles) {
 
		String boundary = "---------------------------" + System.currentTimeMillis();
		List <Part> parts = new ArrayList<Part>();
		parts.add(new StringPart (HttpMethodParams.MULTIPART_BOUNDARY, boundary));
 
		for(Entry<String, String> itr : kparams.entrySet()) {
			parts.add(new StringPart (itr.getKey(), itr.getValue()));	   
		}
		
		for (String key : kfiles.keySet()) {
			final KalturaFile kFile = kfiles.get(key);
			parts.add(new StringPart (key, "filename="+kFile.getName()));
			if (kFile.getFile() != null) {
				// use the file
				File file = kFile.getFile();
	 		try {
					parts.add(new FilePart(key, file));
	 		} catch (FileNotFoundException e) {
					// TODO this sort of leaves the submission in a weird state... -AZ
				if (logger.isEnabled())
					logger.error("Exception while iterating over kfiles", e);		  
	 		}
			} else {
				// use the input stream
				PartSource fisPS = new PartSource() {
					public long getLength() {
						return kFile.getSize();
 		}
					public String getFileName() {
						return kFile.getName();
					}
					public InputStream createInputStream() throws IOException {
						return kFile.getInputStream();
					}
				};
				parts.add(new FilePart(key, fisPS));
			}
		}
	 
		Part allParts[] = new Part[parts.size()];
		allParts = parts.toArray(allParts);
	 
		method.setRequestEntity(new MultipartRequestEntity(allParts, method.getParams()));
 
		return method;
	}
		
	private PostMethod addParams(PostMethod method, KalturaParams kparams) {
		
		for(Entry<String, String> itr : kparams.entrySet()) {
			method.addParameter(itr.getKey(), itr.getValue());
		}
		
		return method;
		
	}
	
	public String generateSession(String adminSecretForSigning, String userId, KalturaSessionType type, int partnerId) throws Exception
	{
		return this.generateSession(adminSecretForSigning, userId, type, partnerId, 86400);
	}
	
	public String generateSession(String adminSecretForSigning, String userId, KalturaSessionType type, int partnerId, int expiry) throws Exception
	{
		return this.generateSession(adminSecretForSigning, userId, type, partnerId, expiry, "");
	}

	public String generateSession(String adminSecretForSigning, String userId, KalturaSessionType type, int partnerId, int expiry, String privileges) throws Exception
	{
		try
		{
			// initialize required values
			int rand = (int)(Math.random() * 32000);
			expiry += (int)(System.currentTimeMillis() / 1000);
			
			// build info string
			StringBuilder sbInfo = new StringBuilder();
			sbInfo.append(partnerId).append(";"); // index 0 - partner ID
			sbInfo.append(partnerId).append(";"); // index 1 - partner pattern - using partner ID
			sbInfo.append(expiry).append(";"); // index 2 - expiration timestamp
			sbInfo.append(type.getHashCode()).append(";"); // index 3 - session type
			sbInfo.append(rand).append(";"); // index 4 - random number
			sbInfo.append(userId).append(";"); // index 5 - user ID
			sbInfo.append(privileges); // index 6 - privileges
			
			// sign info with SHA1 algorithm
			MessageDigest algorithm = MessageDigest.getInstance("SHA1");
			algorithm.reset();
			algorithm.update(adminSecretForSigning.getBytes());
			algorithm.update(sbInfo.toString().getBytes());
			byte infoSignature[] = algorithm.digest();
			
			// convert signature to hex:
			String signature = this.convertToHex(infoSignature);
			
			// build final string to base64 encode
			StringBuilder sbToEncode = new StringBuilder();
			sbToEncode.append(signature.toString()).append("|").append(sbInfo.toString());

			String hashedString = new String(Base64.encodeBase64(sbToEncode.toString().getBytes()));
			
			// remove line breaks in the session string
			String ks = hashedString.replace("\n", "");
			ks = ks.replace("\r", "");
			
			// return the generated session key (KS)
			return ks;
		} catch (NoSuchAlgorithmException ex)
		{
			throw new Exception(ex);
		}
	}

	// new function to convert byte array to Hex
	private String convertToHex(byte[] data) { 
		StringBuffer buf = new StringBuffer();
		for (int i = 0; i < data.length; i++) { 
			int halfbyte = (data[i] >>> 4) & 0x0F;
			int two_halfs = 0;
			do { 
				if ((0 <= halfbyte) && (halfbyte <= 9)) 
					buf.append((char) ('0' + halfbyte));
				else 
					buf.append((char) ('a' + (halfbyte - 10)));
				halfbyte = data[i] & 0x0F;
			} while(two_halfs++ < 1);
		} 
		return buf.toString();
	} 
	
}

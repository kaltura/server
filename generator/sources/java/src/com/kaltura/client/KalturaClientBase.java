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

import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.InputStream;
import java.io.UnsupportedEncodingException;
import java.math.BigInteger;
import java.net.SocketTimeoutException;
import java.net.URLEncoder;
import java.security.GeneralSecurityException;
import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;
import java.util.Map.Entry;
import java.util.Random;
import java.util.zip.GZIPInputStream;

import javax.crypto.Cipher;
import javax.crypto.spec.IvParameterSpec;
import javax.crypto.spec.SecretKeySpec;
import javax.xml.xpath.XPathExpressionException;

import org.apache.commons.codec.binary.Base64;
import org.apache.commons.httpclient.ConnectTimeoutException;
import org.apache.commons.httpclient.DefaultHttpMethodRetryHandler;
import org.apache.commons.httpclient.Header;
import org.apache.commons.httpclient.HttpClient;
import org.apache.commons.httpclient.HttpConnectionManager;
import org.apache.commons.httpclient.HttpException;
import org.apache.commons.httpclient.HttpStatus;
import org.apache.commons.httpclient.MultiThreadedHttpConnectionManager;
import org.apache.commons.httpclient.ProxyHost;
import org.apache.commons.httpclient.SimpleHttpConnectionManager;
import org.apache.commons.httpclient.methods.PostMethod;
import org.apache.commons.httpclient.methods.multipart.FilePart;
import org.apache.commons.httpclient.methods.multipart.MultipartRequestEntity;
import org.apache.commons.httpclient.methods.multipart.Part;
import org.apache.commons.httpclient.methods.multipart.PartSource;
import org.apache.commons.httpclient.methods.multipart.StringPart;
import org.apache.commons.httpclient.params.HttpConnectionManagerParams;
import org.apache.commons.httpclient.params.HttpMethodParams;
import org.w3c.dom.Element;

import com.kaltura.client.enums.KalturaSessionType;
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
    
    // KS v2 constants
    private static final int BLOCK_SIZE = 16;
    private static final String FIELD_EXPIRY = "_e";
    private static final String FIELD_USER = "_u";
	private static final String FIELD_TYPE = "_t";
	private static final int RANDOM_SIZE = 16; 

	private static final int MAX_DEBUG_RESPONSE_STRING_LENGTH = 1024;
	protected KalturaConfiguration kalturaConfiguration;
    protected String sessionId;
    protected List<KalturaServiceActionCall> callsQueue;
    protected boolean isMultiRequest;
    protected KalturaParams multiRequestParamsMap;

	private static KalturaLogger logger = KalturaLogger.getLogger(KalturaClientBase.class);
    
    private Header[] responseHeaders = null; 
    
    private boolean acceptGzipEncoding = true;
    
    protected static final String HTTP_HEADER_ACCEPT_ENCODING = "Accept-Encoding";

	protected static final String HTTP_HEADER_CONTENT_ENCODING = "Content-Encoding";

	protected static final String ENCODING_GZIP = "gzip";
    /**
	 * Set whether to accept GZIP encoding, that is, whether to
	 * send the HTTP "Accept-Encoding" header with "gzip" as value.
	 * <p>Default is "true". Turn this flag off if you do not want
	 * GZIP response compression even if enabled on the HTTP server.
	 */
	public void setAcceptGzipEncoding(boolean acceptGzipEncoding) {
		this.acceptGzipEncoding = acceptGzipEncoding;
	}
    /**
	 * Return whether to accept GZIP encoding, that is, whether to
	 * send the HTTP "Accept-Encoding" header with "gzip" as value.
	 */
	public boolean isAcceptGzipEncoding() {
		return acceptGzipEncoding;
	}
    
    /**
	 * Determine whether the given response is a GZIP response.
	 * <p>Default implementation checks whether the HTTP "Content-Encoding"
	 * header contains "gzip" (in any casing).
	 * @param postMethod the PostMethod to check
	 */
	protected boolean isGzipResponse(PostMethod postMethod) {
		Header encodingHeader = postMethod.getResponseHeader(HTTP_HEADER_CONTENT_ENCODING);
		if (encodingHeader == null || encodingHeader.getValue() == null) {
			return false;
		}
		return (encodingHeader.getValue().toLowerCase().indexOf(ENCODING_GZIP) != -1);
	}
    
    /**
	 * Extract the response body from the given executed remote invocation
	 * request.
	 * <p>The default implementation simply fetches the PostMethod's response
	 * body stream. If the response is recognized as GZIP response, the
	 * InputStream will get wrapped in a GZIPInputStream.
	 * @param config the HTTP invoker configuration that specifies the target service
	 * @param postMethod the PostMethod to read the response body from
	 * @return an InputStream for the response body
	 * @throws IOException if thrown by I/O methods
	 * @see #isGzipResponse
	 * @see java.util.zip.GZIPInputStream
	 * @see org.apache.commons.httpclient.methods.PostMethod#getResponseBodyAsStream()
	 * @see org.apache.commons.httpclient.methods.PostMethod#getResponseHeader(String)
	 */
	protected InputStream getResponseBody(PostMethod postMethod)
			throws IOException {

		if (isGzipResponse(postMethod)) {
			return new GZIPInputStream(postMethod.getResponseBodyAsStream());
		}
		else {
			return postMethod.getResponseBodyAsStream();
		}
	}
    
    public Header[] getResponseHeaders()
    {
        return responseHeaders;
    }

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
		String responseString = null;
		try {
			PostMethod method = createPostMethod(kparams, kfiles, url);
			responseString = executeMethod(client, method);	
		} finally {
			closeHttpClient(client);
		}
		
		Element responseXml = XmlUtils.parseXml(responseString);
		Element resultXml = this.validateXmlResult(responseXml);
		this.throwExceptionOnAPIError(resultXml);
				
		return resultXml;
	}

    protected String readRemoteInvocationResult(InputStream is)
    	throws IOException {
    
        try {
    	  return doReadRemoteInvocationResult(is);
        }
        finally {
    	  is.close();
        }
    }

    protected String doReadRemoteInvocationResult(InputStream is)
    	throws IOException {
    
        byte[] buf = new byte[1024];
        ByteArrayOutputStream out = new ByteArrayOutputStream();
        int len;
        while ( (len = is.read(buf)) > 0)
        {
        	out.write(buf,0,len);
        }
        return new String(out.toByteArray());
    }

	protected String executeMethod(HttpClient client, PostMethod method) throws KalturaApiException {
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

			// Read the response body
            InputStream responseBodyIS = null;
            if (isGzipResponse(method)) {
                responseBodyIS = new GZIPInputStream(method.getResponseBodyAsStream());
                if (logger.isEnabled()) logger.debug("Using gzip compression to handle response for: "+method.getName()+" "+method.getPath()+"?"+method.getQueryString());
            } else {
                responseBodyIS = method.getResponseBodyAsStream();
                if (logger.isEnabled()) logger.debug("No gzip compression for this response");
            }
            String responseBody = readRemoteInvocationResult(responseBodyIS);
            responseHeaders = method.getResponseHeaders();
            
            // print server debug info
            String serverName = null;
            String serverSession = null;
            for(Header header : responseHeaders)
            {
            	if (header.getName().compareTo("X-Me") == 0)
                    serverName = header.getValue();
            	else if (header.getName().compareTo("X-Kaltura-Session") == 0)
                    serverSession = header.getValue();
			}
			if (serverName != null || serverSession != null)
				logger.debug("Server: [" + serverName + "], Session: [" + serverSession + "]");

			// Deal with the response.
			// Use caution: ensure correct character encoding and is not binary data
			responseString = new String (responseBody.getBytes(), UTF8_CHARSET); // Unicon: this MUST be set to UTF-8 charset -AZ
			if (logger.isEnabled())
			{
				if(responseString.length() < MAX_DEBUG_RESPONSE_STRING_LENGTH) {
					logger.debug(responseString);
				} else {
					logger.debug("Received long response. (length : " + responseString.length() + ")");
				}
			}
			
			return responseString;
			
		} catch ( HttpException e ) {
			if (logger.isEnabled())
				logger.error( "Fatal protocol violation: " + e.getMessage ( ) ,e);
			throw new KalturaApiException("Protocol exception occured while executing request");
		} catch ( SocketTimeoutException e) {
			if (logger.isEnabled())
				logger.error( "Fatal transport error: " + e.getMessage ( ), e);
			throw new KalturaApiException("Request was timed out");
		} catch ( ConnectTimeoutException e) {
			if (logger.isEnabled())
				logger.error( "Fatal transport error: " + e.getMessage ( ), e);
			throw new KalturaApiException("Connection to server was timed out");
		} catch ( IOException e ) {
			if (logger.isEnabled())
				logger.error( "Fatal transport error: " + e.getMessage ( ), e);
			throw new KalturaApiException("I/O exception occured while reading request response");
		}  finally {
			// Release the connection.
			method.releaseConnection ( );
		}
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
        
        if (isAcceptGzipEncoding()) {
			method.addRequestHeader(HTTP_HEADER_ACCEPT_ENCODING, ENCODING_GZIP);
		}

		// Provide custom retry handler is necessary
		method.getParams().setParameter(HttpMethodParams.RETRY_HANDLER,
				new DefaultHttpMethodRetryHandler (3, false));
		return method;
	}

	protected HttpClient createHttpClient() {
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
	
	/**
	 * We need to make sure that we shut down the connection.
	 * The possible connection manager types are taken from here:
	 * http://hc.apache.org/httpclient-legacy/apidocs/org/apache/commons/httpclient/HttpConnectionManager.html
	 * 
	 * The issue details is described here:
	 * http://fuyun.org/2009/09/connection-close-in-httpclient/
	 * 
	 * @param client The client we wish to close
	 */
	protected void closeHttpClient(HttpClient client) {
		HttpConnectionManager mgr = client.getHttpConnectionManager();
		if (mgr instanceof SimpleHttpConnectionManager) {
		    ((SimpleHttpConnectionManager)mgr).shutdown();
		}
		
		if(mgr instanceof MultiThreadedHttpConnectionManager) {
			((MultiThreadedHttpConnectionManager)mgr).shutdown();
		}
	}

	private String extractParamsFromCallQueue(KalturaParams kparams, KalturaFiles kfiles) throws KalturaApiException {
		
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

	private String signature(KalturaParams kparams) throws KalturaApiException {
		String str = "";
		for (String key : kparams.keySet()) {
			str += (key + kparams.get(key));
		}

		MessageDigest mdEnc = null;
		try {
			mdEnc = MessageDigest.getInstance("MD5");
		} catch (NoSuchAlgorithmException e) {
			throw new KalturaApiException("Failed to sign parameters");
		}		
		mdEnc.update(str.getBytes(), 0, str.length());
		String md5 = new BigInteger(1, mdEnc.digest()).toString(16); // Encrypted string
		
		return md5;
	}

	private Element validateXmlResult(Element resultXml) throws KalturaApiException {
		
		Element resultElement = null;
   		resultElement = getElementByXPath(resultXml, "/xml/result");
						
		if (resultElement != null) {
			return resultElement;			
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
			
			byte[] infoSignature = signInfoWithSHA1(adminSecretForSigning + (sbInfo.toString()));
			
			// convert signature to hex:
			String signature = this.convertToHex(infoSignature);
			
			// build final string to base64 encode
			StringBuilder sbToEncode = new StringBuilder();
			sbToEncode.append(signature.toString()).append("|").append(sbInfo.toString());
			
			// encode the signature and info with base64
			String hashedString = new String(Base64.encodeBase64(sbToEncode.toString().getBytes()));
			
			// remove line breaks in the session string
			String ks = hashedString.replace("\n", "");
			ks = hashedString.replace("\r", "");
			
			// return the generated session key (KS)
			return ks;
		} catch (NoSuchAlgorithmException ex)
		{
			throw new Exception(ex);
		}
	}

	public String generateSessionV2(String adminSecretForSigning, String userId, KalturaSessionType type, int partnerId, int expiry, String privileges) throws Exception
	{
		try {
		// build fields array
		KalturaParams fields = new KalturaParams();
		String[] privilegesArr = privileges.split(",");
		for (String curPriv : privilegesArr) {
			String privilege = curPriv.trim();
			if(privilege.length() == 0)
				continue;
			if(privilege.equals("*"))
				privilege = "all:*";
			
			String[] splittedPriv = privilege.split(":");
			if(splittedPriv.length>1) {
				fields.add(splittedPriv[0], URLEncoder.encode(splittedPriv[1], UTF8_CHARSET));
			} else {
				fields.add(splittedPriv[0], "");
			}
		}
		
		Integer expiryInt = (int)(System.currentTimeMillis() / 1000) + expiry;
		String expStr = expiryInt.toString();
		fields.put(FIELD_EXPIRY,  expStr);
		fields.put(FIELD_TYPE, Integer.toString(type.getHashCode()));
		fields.put(FIELD_USER, userId);
		
		// build fields string
		byte[] randomBytes = createRandomByteArray(RANDOM_SIZE);
		byte[] fieldsByteArray = fields.toQueryString().getBytes();
		int totalLength = randomBytes.length + fieldsByteArray.length;
		byte[] fieldsAndRandomBytes = new byte[totalLength];
		System.arraycopy(randomBytes, 0, fieldsAndRandomBytes, 0, randomBytes.length);
		System.arraycopy(fieldsByteArray, 0, fieldsAndRandomBytes, randomBytes.length, fieldsByteArray.length);

		byte[] infoSignature = signInfoWithSHA1(fieldsAndRandomBytes);
		byte[] input = new byte[infoSignature.length + fieldsAndRandomBytes.length];
		System.arraycopy(infoSignature, 0, input, 0, infoSignature.length);
		System.arraycopy(fieldsAndRandomBytes,0,input,infoSignature.length, fieldsAndRandomBytes.length);
		
		// encrypt and encode
		byte[] encryptedFields = aesEncrypt(adminSecretForSigning, input);
		String prefix = "v2|" + partnerId + "|";
		
		byte[] output = new byte[encryptedFields.length + prefix.length()];
		System.arraycopy(prefix.getBytes(), 0, output, 0, prefix.length());
		System.arraycopy(encryptedFields,0,output,prefix.length(), encryptedFields.length);
		
		String encodedKs = new String(Base64.encodeBase64(output));
		encodedKs = encodedKs.replaceAll("\\+", "-");
		encodedKs = encodedKs.replaceAll("/", "_");
		encodedKs = encodedKs.replace("\n", "");
		encodedKs = encodedKs.replace("\r", "");
		
		return encodedKs;
		} catch (GeneralSecurityException ex) {
			logger.error("Failed to generate v2 session.");
			throw new Exception(ex);
		} 
	}
	
	private byte[] signInfoWithSHA1(String text) throws GeneralSecurityException {
		return signInfoWithSHA1(text.getBytes());
	}
	
	private byte[] signInfoWithSHA1(byte[] data) throws GeneralSecurityException {
		MessageDigest algorithm = MessageDigest.getInstance("SHA1");
		algorithm.reset();
		algorithm.update(data);
		byte infoSignature[] = algorithm.digest();
		return infoSignature;
	}
	
	private byte[] aesEncrypt(String secretForSigning, byte[] text) throws GeneralSecurityException, UnsupportedEncodingException {
		// Key
		byte[] hashedKey = signInfoWithSHA1(secretForSigning);
		byte[] keyBytes = new byte[BLOCK_SIZE];
		System.arraycopy(hashedKey,0,keyBytes,0,BLOCK_SIZE);
		SecretKeySpec key = new SecretKeySpec(keyBytes, "AES");
		
		// IV
		byte[] ivBytes = new byte[BLOCK_SIZE];
		IvParameterSpec iv = new IvParameterSpec(ivBytes);
		
		// Text
		int textSize = ((text.length + BLOCK_SIZE - 1) / BLOCK_SIZE) * BLOCK_SIZE;
		byte[] textAsBytes = new byte[textSize];
		Arrays.fill(textAsBytes, (byte)0);
		System.arraycopy(text, 0, textAsBytes, 0, text.length);
		
		// Encrypt
		Cipher cipher = Cipher.getInstance("AES/CBC/NOPADDING");
	    cipher.init(Cipher.ENCRYPT_MODE, key, iv);
        return cipher.doFinal(textAsBytes);
	}
	
	
	private byte[] createRandomByteArray(int size)	{
		byte[] b = new byte[size];
		new Random().nextBytes(b);
		return b;
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

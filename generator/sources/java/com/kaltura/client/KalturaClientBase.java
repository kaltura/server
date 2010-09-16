package com.kaltura.client;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.math.BigInteger;
import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import java.util.ArrayList;
import java.util.List;

import javax.xml.xpath.XPathExpressionException;

import org.apache.commons.httpclient.DefaultHttpMethodRetryHandler;
import org.apache.commons.httpclient.HttpClient;
import org.apache.commons.httpclient.HttpException;
import org.apache.commons.httpclient.HttpStatus;
import org.apache.commons.httpclient.methods.PostMethod;
import org.apache.commons.httpclient.methods.multipart.FilePart;
import org.apache.commons.httpclient.methods.multipart.MultipartRequestEntity;
import org.apache.commons.httpclient.methods.multipart.Part;
import org.apache.commons.httpclient.methods.multipart.StringPart;
import org.apache.commons.httpclient.params.HttpConnectionManagerParams;
import org.apache.commons.httpclient.params.HttpMethodParams;
import org.apache.log4j.Logger;
import org.w3c.dom.Element;

import com.kaltura.client.utils.XmlUtils;

/**
 * Contains non-generated client logic. Includes the doQueue method which is responsible for
 * making HTTP calls to the Kaltura server.
 * 
 * @author jpotts
 *
 */
public class KalturaClientBase {

    protected KalturaConfiguration kalturaConfiguration;
    protected String sessionId;
    protected List<KalturaServiceActionCall> callsQueue;
    protected boolean isMultiRequest;
    protected KalturaParams multiRequestParamsMap;

    private static Logger logger = Logger.getLogger(KalturaClientBase.class);
    
    public KalturaClientBase() {    	
    }
    
    public KalturaClientBase(KalturaConfiguration config) {
        this.kalturaConfiguration = config;
        this.callsQueue = new ArrayList<KalturaServiceActionCall>();
        this.multiRequestParamsMap = new KalturaParams();
    }
    
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
        if (!kparams.containsKey("partnerId"))
            kparams.addIntIfNotNull("partnerId", this.kalturaConfiguration.getPartnerId());

        if (kparams.get("partnerId").equals("-1"))
            kparams.addIntIfNotNull("partnerId", this.kalturaConfiguration.getPartnerId());

        kparams.addStringIfNotNull("ks", this.sessionId);

        KalturaServiceActionCall call = new KalturaServiceActionCall(service, action, kparams, kfiles);
        this.callsQueue.add(call);
    }

    public Element doQueue() throws KalturaApiException {
        if (this.callsQueue.isEmpty()) return null;

        logger.debug("service url: [" + this.kalturaConfiguration.getEndpoint() + "]");

        KalturaParams kparams = new KalturaParams();
        KalturaFiles kfiles = new KalturaFiles();

        // append the basic params
        kparams.put("apiVersion", this.kalturaConfiguration.getApiVersion());
        kparams.put("clientTag", this.kalturaConfiguration.getClientTag());
        kparams.addIntIfNotNull("format", this.kalturaConfiguration.getServiceFormat().getHashCode());

        String url = this.kalturaConfiguration.getEndpoint() + "/api_v3/index.php?service=";

        if (isMultiRequest) {
            url += "multirequest";
            int i = 1;
            for (KalturaServiceActionCall call : this.callsQueue) {
                KalturaParams callParams = call.getParamsForMultiRequest(i++);
                kparams.add(callParams);
                kfiles.add(call.getFiles());
            }

            // map params
            for (String key : this.multiRequestParamsMap.keySet()) {
                String requestParam = key;
                String resultParam = this.multiRequestParamsMap.get(key);

                if (kparams.containsKey(requestParam)) {
                    kparams.put(requestParam, resultParam);
                }
            }
        } else {
            KalturaServiceActionCall call = this.callsQueue.get(0);
            url += call.getService() + "&action=" + call.getAction();
            kparams.add(call.getParams());
            kfiles.add(call.getFiles());
        }

        // cleanup
        this.callsQueue.clear();
        this.isMultiRequest = false;
        this.multiRequestParamsMap.clear();

        kparams.put("sig", this.signature(kparams));

        logger.debug("full reqeust url: [" + url + "]");

        // build request
        HttpClient client = new HttpClient();
		HttpConnectionManagerParams connParams = client.getHttpConnectionManager().getParams();
		connParams.setSoTimeout(this.kalturaConfiguration.getTimeout());
		client.getHttpConnectionManager().setParams(connParams);
		
        PostMethod method = new PostMethod(url);
        
        if (!kfiles.isEmpty()) {        	
            method = this.getPostMultiPartWithFiles(method, kparams, kfiles);        	
        } else {
            method = this.addParams(method, kparams);            
        }

		// Provide custom retry handler is necessary
		method.getParams().setParameter(HttpMethodParams.RETRY_HANDLER,
				new DefaultHttpMethodRetryHandler (3, false));

		String responseString = "";
		try {
			// Execute the method.
			int statusCode = client.executeMethod(method);

			if (statusCode != HttpStatus.SC_OK) {
				System.err.println ( "Method failed: " + method.getStatusLine ( ) );
			}

			// Read the response body.
			byte[] responseBody = method.getResponseBody ( );

			// Deal with the response.
			// Use caution: ensure correct character encoding and is not binary
			// data
			responseString = new String (responseBody);
			logger.debug(responseString);
			
		} catch ( HttpException e ) {
			System.err.println ( "Fatal protocol violation: " + e.getMessage ( ) );
			e.printStackTrace ( );
		} catch ( IOException e ) {
			System.err.println ( "Fatal transport error: " + e.getMessage ( ) );
			e.printStackTrace ( );
		} finally {
			// Release the connection.
			method.releaseConnection ( );
		}			
		        
        Element responseXml = XmlUtils.parseXml(responseString);
        
        this.validateXmlResult(responseXml);
      
        Element resultXml = null;
        try {
        	resultXml = XmlUtils.getElementByXPath(responseXml, "/xml/result");
        } catch (XPathExpressionException xee) {
    		throw new KalturaApiException("XPath expression exception evaluating result");
    	}	
        
        this.throwExceptionOnAPIError(resultXml);
        
        return resultXml;
    }

    public void startMultiRequest() {
        isMultiRequest = true;
    }

    
    public KalturaMultiResponse doMultiRequest() throws KalturaApiException 
    {
    	Element multiRequestResult = doQueue();

        KalturaMultiResponse multiResponse = new KalturaMultiResponse();
       
        for(int i = 0; i < multiRequestResult.getChildNodes().getLength(); i++) 
        {
            Element arrayNode = (Element)multiRequestResult.getChildNodes().item(i);
            if (arrayNode.getElementsByTagName("objectType").getLength() == 0)
            {
            	multiResponse.add(arrayNode.getTextContent());
            }
            else
            {
            	multiResponse.add(KalturaObjectFactory.create(arrayNode));
            }
       }
        
 /*       foreach (Element arrayNode in multiRequestResult.ChildNodes)
        {
            if (arrayNode["error"] != null)
                multiResponse.Add(new KalturaAPIException(arrayNode["error"]["code"].InnerText, arrayNode["error"]["message"].InnerText));
            else if (arrayNode["objectType"] != null)
                multiResponse.Add(KalturaObjectFactory.Create(arrayNode));
            else
                multiResponse.Add(arrayNode.InnerText);
        }
*/
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
    	try {
    		resultElement = XmlUtils.getElementByXPath(resultXml, "/xml/result");
    	} catch (XPathExpressionException xee) {
    		throw new KalturaApiException("XPath expression exception evaluating result");
    	}
    	                
        if (resultElement != null) {
            return;            
        } else {
        	throw new KalturaApiException("Invalid result");
        }
    }

    private void throwExceptionOnAPIError(Element result) throws KalturaApiException {
    	
    	Element resultElement = null;
    	try {
    		resultElement = XmlUtils.getElementByXPath(result, "error");
    	} catch (XPathExpressionException xee) {
    		throw new KalturaApiException("XPath expression exception evaluating result");
    	}
    	        
        if (resultElement != null) {
        	throw new KalturaApiException(resultElement.getTextContent());
        }
    }

    private PostMethod getPostMultiPartWithFiles(PostMethod postMethod, KalturaParams kparams, KalturaFiles kfiles) {
 
    	String boundary = "---------------------------" + System.currentTimeMillis();
    	List <Part> parts = new ArrayList<Part>();
    	parts.add(new StringPart (HttpMethodParams.MULTIPART_BOUNDARY, boundary));
 
    	for (String key : kparams.keySet()) {
    		parts.add(new StringPart (key,kparams.get(key)));       
    	}
     
    	for (String key : kfiles.keySet()) {
    		File file = kfiles.get(key);
    		try {
    			parts.add(new StringPart (key, "filename="+file.getName()));
    			parts.add(new FilePart(key, file));
    		} catch (FileNotFoundException e) {
    			logger.error("Exception while iterating over kfiles", e);          
    		}
    	}
      
    	Part allParts[] = new Part[parts.size()];
    	int i=0;
    	for (Part p : parts) {
    		allParts[i] = p;
    		++i;
    	}
     
    	postMethod.setRequestEntity(new MultipartRequestEntity(allParts, postMethod.getParams()));
 
    	return postMethod;
    }
	    
    private PostMethod addParams(PostMethod method, KalturaParams kparams) {
    	
		for (String key : kparams.keySet()) {
			method.addParameter(key, kparams.get(key));
		}
		
		return method;
		
    }
           
}

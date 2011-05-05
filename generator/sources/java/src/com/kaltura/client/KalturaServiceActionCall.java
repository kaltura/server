package com.kaltura.client;

/**
 * A KalturaServiceActionCall is what the client queues to represent a request to the Kaltura server.
 * 
 * @author jpotts
 *
 */
public class KalturaServiceActionCall {
	private String service;
    private String action;
    private KalturaParams params;
    private KalturaFiles files;

    public String getService() {
        return this.service;
    }

    public String getAction() {    
    	return this.action;
    }

    public KalturaParams getParams() {
        return this.params;
    }

    public KalturaFiles getFiles() {
        return this.files;
    }

    public KalturaParams getParamsForMultiRequest(int multiRequestNumber) {
        KalturaParams multiRequestParams = new KalturaParams();
        multiRequestParams.put(multiRequestNumber + ":service", this.service);
        multiRequestParams.put(multiRequestNumber + ":action", this.action);
        for (String param : this.params.keySet()) {
            multiRequestParams.put(multiRequestNumber + ":" + param, this.params.get(param));
        }

        return multiRequestParams;
    }

    public KalturaServiceActionCall(String service, String action, KalturaParams kparams) {
        this(service, action, kparams, new KalturaFiles());
    }

    public KalturaServiceActionCall(String service, String action, KalturaParams kparams, KalturaFiles kfiles) {
        this.service = service;
        this.action = action;
        this.params = kparams;
        this.files = kfiles;
    }

}

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
    
    public KalturaServiceActionCall(String service, String action, KalturaParams kparams) {
        this(service, action, kparams, new KalturaFiles());
    }

    public KalturaServiceActionCall(String service, String action, KalturaParams kparams, KalturaFiles kfiles) {
        this.service = service;
        this.action = action;
        this.params = kparams;
        this.files = kfiles;
    }

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
        
        multiRequestParams.addMulti(multiRequestNumber, "service", this.service);
        multiRequestParams.addMulti(multiRequestNumber, "action", this.action);
        multiRequestParams.addMulti(multiRequestNumber, this.params);
        
        return multiRequestParams;
    }

    public KalturaFiles getFilesForMultiRequest(int multiRequestNumber) {
    	
        KalturaFiles multiRequestFiles = new KalturaFiles();
        multiRequestFiles.add(Integer.toString(multiRequestNumber), this.files);
        return multiRequestFiles;
    }

}

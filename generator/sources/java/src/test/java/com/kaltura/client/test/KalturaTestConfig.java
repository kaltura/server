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
package com.kaltura.client.test;

import java.io.IOException;
import java.io.InputStream;
import java.util.Properties;

public class KalturaTestConfig {
	private static Properties properties;
	
	private static final String PARTNER_ID = "partnerId";
	private static final String ADMIN_SECRET = "adminSecret";
	private static final String ENDPOINT = "serviceUrl";
	private static final String TEST_URL = "testUrl";
	private static final String UPLOAD_VIDEO = "uploadVideo";
	private static final String UPLOAD_IMAGE = "uploadImage";
	private static final String USER_ID = "userId";
	
	public KalturaTestConfig() throws IOException{
		if(properties == null){
			InputStream inputStream = getClass().getClassLoader().getResourceAsStream("test.properties");
			properties = new Properties();
			properties.load(inputStream);
		}
	}
	
	public int getPartnerId(){
		return Integer.parseInt(properties.getProperty(PARTNER_ID));
	}
	
	public String getAdminSecret(){
		return properties.getProperty(ADMIN_SECRET);
	}
	
	public String getServiceUrl(){
		return properties.getProperty(ENDPOINT);
	}
	
	public String getTestUrl(){
		return properties.getProperty(TEST_URL);
	}
	
	public String getUploadVideo(){
		return properties.getProperty(UPLOAD_VIDEO);
	}
	
	public String getUploadImage(){
		return properties.getProperty(UPLOAD_IMAGE);
	}
	
	public String getUserId(){
		return properties.getProperty(USER_ID);
	}
}

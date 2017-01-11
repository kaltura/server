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

import java.util.HashMap;

/**
 * Helper class that provides a collection of Files.
 * 
 * @author jpotts
 * @author azeckoski
 *
 */
public class KalturaFiles extends HashMap<String, KalturaFile> {
	
	private static final long serialVersionUID = -5838275045069221834L;
	
	private static final String PARAMS_SEPERATOR = ":";

	public void add(KalturaFiles files) {
		this.putAll(files);
	}
	
	public void add(String objectName, KalturaFiles files) {
		for (java.util.Map.Entry<String, KalturaFile> itr : files.entrySet()) {
			this.put(objectName + PARAMS_SEPERATOR + itr.getKey(), itr.getValue());           
		}
	}

	public void add(String key, KalturaFile value) {
		if (value == null)
			return;
		this.put(key, value);
	}
}

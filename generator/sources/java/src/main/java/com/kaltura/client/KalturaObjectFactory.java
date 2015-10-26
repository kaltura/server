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

import org.w3c.dom.Element;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;
import com.kaltura.client.KalturaApiException;
import java.lang.reflect.Constructor;

/**
 * This class was generated using generate.php
 * against an XML schema provided by Kaltura.
 * @date Thu, 09 Feb 12 10:24:52 +0200
 * 
 * MANUAL CHANGES TO THIS CLASS WILL BE OVERWRITTEN.
 */

public class KalturaObjectFactory {
    
    @SuppressWarnings("unchecked")
	public static <T> T create(Element xmlElement, Class<T> fallbackClazz) throws KalturaApiException {
    	NodeList objectTypeNodes = xmlElement.getElementsByTagName("objectType");
        Node objectTypeNode = objectTypeNodes.item(0);    

		Class<T> clazz = null;
        if (objectTypeNode != null) {
	        String objectType = objectTypeNode.getTextContent();
	        
			try {
				clazz = (Class<T>) Class.forName("com.kaltura.client.types." + objectType);
			} catch (ClassNotFoundException e1) {
				clazz = null;
			}
        }
        
        if(clazz == null){
			if(fallbackClazz != null) {
				clazz = fallbackClazz;
			} else {
				throw new KalturaApiException("Invalid object type" );
			}
        }

        try {
            Constructor<?> ctor = clazz.getConstructor(Element.class);
            return (T) ctor.newInstance(xmlElement);
        } catch (Exception e) {
        	 throw new KalturaApiException("Failed to construct object");
        }
    }
}

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

import java.util.ArrayList;

import com.kaltura.client.enums.KalturaMediaType;
import com.kaltura.client.enums.KalturaMetadataObjectType;
import com.kaltura.client.types.KalturaCategory;
import com.kaltura.client.types.KalturaCategoryEntry;
import com.kaltura.client.types.KalturaCategoryEntryFilter;
import com.kaltura.client.types.KalturaCategoryEntryListResponse;
import com.kaltura.client.types.KalturaDetachedResponseProfile;
import com.kaltura.client.types.KalturaMediaEntry;
import com.kaltura.client.types.KalturaMetadata;
import com.kaltura.client.types.KalturaMetadataFilter;
import com.kaltura.client.types.KalturaMetadataListResponse;
import com.kaltura.client.types.KalturaMetadataProfile;
import com.kaltura.client.types.KalturaResponseProfile;
import com.kaltura.client.types.KalturaResponseProfileHolder;
import com.kaltura.client.types.KalturaResponseProfileMapping;


public class ResponseProfileTest extends BaseTest{

	public void testEntryCategoriesAndMetadata() throws Exception {
		KalturaMediaEntry entry = null;
		KalturaCategory category = null;
		KalturaMetadataProfile categoryMetadataProfile = null;
		KalturaResponseProfile responseProfile = null;
		
		try{
			String xsd = "<xsd:schema xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\">\n";
			xsd += "	<xsd:element name=\"metadata\">\n";
			xsd += "		<xsd:complexType>\n";
			xsd += "			<xsd:sequence>\n";
			xsd += "				<xsd:element name=\"Choice\" minOccurs=\"0\" maxOccurs=\"1\">\n";
			xsd += "					<xsd:annotation>\n";
			xsd += "						<xsd:documentation></xsd:documentation>\n";
			xsd += "						<xsd:appinfo>\n";
			xsd += "							<label>Example choice</label>\n";
			xsd += "							<key>choice</key>\n";
			xsd += "							<searchable>true</searchable>\n";
			xsd += "							<description>Example choice</description>\n";
			xsd += "						</xsd:appinfo>\n";
			xsd += "					</xsd:annotation>\n";
			xsd += "					<xsd:simpleType>\n";
			xsd += "						<xsd:restriction base=\"listType\">\n";
			xsd += "							<xsd:enumeration value=\"on\" />\n";
			xsd += "							<xsd:enumeration value=\"off\" />\n";
			xsd += "						</xsd:restriction>\n";
			xsd += "					</xsd:simpleType>\n";
			xsd += "				</xsd:element>\n";
			xsd += "				<xsd:element name=\"FreeText\" minOccurs=\"0\" maxOccurs=\"1\" type=\"textType\">\n";
			xsd += "					<xsd:annotation>\n";
			xsd += "						<xsd:documentation></xsd:documentation>\n";
			xsd += "						<xsd:appinfo>\n";
			xsd += "							<label>Free text</label>\n";
			xsd += "							<key>freeText</key>\n";
			xsd += "							<searchable>true</searchable>\n";
			xsd += "							<description>Free text</description>\n";
			xsd += "						</xsd:appinfo>\n";
			xsd += "					</xsd:annotation>\n";
			xsd += "				</xsd:element>\n";
			xsd += "			</xsd:sequence>\n";
			xsd += "		</xsd:complexType>\n";
			xsd += "	</xsd:element>\n";
			xsd += "	<xsd:complexType name=\"textType\">\n";
			xsd += "		<xsd:simpleContent>\n";
			xsd += "			<xsd:extension base=\"xsd:string\" />\n";
			xsd += "		</xsd:simpleContent>\n";
			xsd += "	</xsd:complexType>\n";
			xsd += "	<xsd:complexType name=\"objectType\">\n";
			xsd += "		<xsd:simpleContent>\n";
			xsd += "			<xsd:extension base=\"xsd:string\" />\n";
			xsd += "		</xsd:simpleContent>\n";
			xsd += "	</xsd:complexType>\n";
			xsd += "	<xsd:simpleType name=\"listType\">\n";
			xsd += "		<xsd:restriction base=\"xsd:string\" />\n";
			xsd += "	</xsd:simpleType>\n";
			xsd += "</xsd:schema>";
			
			String xml = "<metadata>\n";
			xml += "	<Choice>on</Choice>\n";
			xml += "	<FreeText>example text </FreeText>\n";
			xml += "</metadata>";
						
			entry = createEntry();
			category = createCategory();
			categoryMetadataProfile = createMetadataProfile(KalturaMetadataObjectType.CATEGORY, xsd);

			KalturaMetadataFilter metadataFilter = new KalturaMetadataFilter();
			metadataFilter.metadataObjectTypeEqual = KalturaMetadataObjectType.CATEGORY;
			metadataFilter.metadataProfileIdEqual = categoryMetadataProfile.id;

			KalturaResponseProfileMapping metadataMapping = new KalturaResponseProfileMapping();
			metadataMapping.filterProperty = "objectIdEqual";
			metadataMapping.parentProperty = "categoryId";
			
			ArrayList<KalturaResponseProfileMapping> metadataMappings = new ArrayList<KalturaResponseProfileMapping>();
			metadataMappings.add(metadataMapping);

			KalturaDetachedResponseProfile metadataResponseProfile = new KalturaDetachedResponseProfile();
			metadataResponseProfile.name = "metadata";
			metadataResponseProfile.filter = metadataFilter;
			metadataResponseProfile.mappings = metadataMappings;
			
			ArrayList<KalturaDetachedResponseProfile> categoryEntryRelatedProfiles = new ArrayList<KalturaDetachedResponseProfile>();
			categoryEntryRelatedProfiles.add(metadataResponseProfile);

			KalturaCategoryEntryFilter categoryEntryFilter = new KalturaCategoryEntryFilter();
			
			KalturaResponseProfileMapping categoryEntryMapping = new KalturaResponseProfileMapping();
			categoryEntryMapping.filterProperty = "entryIdEqual";
			categoryEntryMapping.parentProperty = "id";
			
			ArrayList<KalturaResponseProfileMapping> categoryEntryMappings = new ArrayList<KalturaResponseProfileMapping>();
			categoryEntryMappings.add(categoryEntryMapping);
			
			KalturaDetachedResponseProfile categoryEntryResponseProfile = new KalturaDetachedResponseProfile();
			categoryEntryResponseProfile.name = "categoryEntry";
			categoryEntryResponseProfile.relatedProfiles = categoryEntryRelatedProfiles;
			categoryEntryResponseProfile.filter = categoryEntryFilter;
			categoryEntryResponseProfile.mappings = categoryEntryMappings;
			
			ArrayList<KalturaDetachedResponseProfile> entryRelatedProfiles = new ArrayList<KalturaDetachedResponseProfile>();
			entryRelatedProfiles.add(categoryEntryResponseProfile);
			
			responseProfile = new KalturaResponseProfile();
			responseProfile.name = "rp" + System.currentTimeMillis();
			responseProfile.systemName = responseProfile.name;
			responseProfile.relatedProfiles = entryRelatedProfiles;
			
			responseProfile = client.getResponseProfileService().add(responseProfile);
			assertNotNull(responseProfile.id);
			assertNotNull(responseProfile.relatedProfiles);
			assertEquals(1, responseProfile.relatedProfiles.size());
			
			KalturaCategoryEntry categoryEntry = addEntryToCategory(entry.id, category.id);
			KalturaMetadata categoryMetadata = createMetadata(KalturaMetadataObjectType.CATEGORY, Integer.toString(category.id), categoryMetadataProfile.id, xml);
			
			KalturaResponseProfileHolder responseProfileHolder = new KalturaResponseProfileHolder();
			responseProfileHolder.id = responseProfile.id;
	
			startAdminSession();
			client.setResponseProfile(responseProfileHolder);
			KalturaMediaEntry getEntry = client.getMediaService().get(entry.id);
			assertEquals(getEntry.id, entry.id);
			
			assertNotNull(getEntry.relatedObjects);
			assertTrue(getEntry.relatedObjects.containsKey(categoryEntryResponseProfile.name));
			KalturaCategoryEntryListResponse categoryEntryList = (KalturaCategoryEntryListResponse) getEntry.relatedObjects.get(categoryEntryResponseProfile.name);
			assertEquals(1, categoryEntryList.totalCount);
			KalturaCategoryEntry getCategoryEntry = categoryEntryList.objects.get(0);
			assertEquals(getCategoryEntry.createdAt, categoryEntry.createdAt);

			assertNotNull(getCategoryEntry.relatedObjects);
			assertTrue(getCategoryEntry.relatedObjects.containsKey(metadataResponseProfile.name));
			KalturaMetadataListResponse metadataList = (KalturaMetadataListResponse) getCategoryEntry.relatedObjects.get(metadataResponseProfile.name);
			assertEquals(1, metadataList.totalCount);
			KalturaMetadata getMetadata = metadataList.objects.get(0);
			assertEquals(categoryMetadata.id, getMetadata.id);
			assertEquals(xml, getMetadata.xml);
		}
		finally{
			if(responseProfile != null && responseProfile.id > 0)
				deleteResponseProfile(responseProfile.id);

			if(entry != null && entry.id != null)
				deleteEntry(entry.id);

			if(category != null && category.id > 0)
				deleteCategory(category.id);

			if(categoryMetadataProfile != null && categoryMetadataProfile.id > 0)
				deleteMetadataProfile(categoryMetadataProfile.id);
		}
	}

	protected KalturaMetadata createMetadata(KalturaMetadataObjectType objectType, String objectId, int metadataProfileId, String xmlData) throws Exception {
		startAdminSession();

		KalturaMetadata metadata = client.getMetadataService().add(metadataProfileId, objectType, objectId, xmlData);
		assertNotNull(metadata.id);
		
		return metadata;
	}

	protected KalturaMetadataProfile createMetadataProfile(KalturaMetadataObjectType objectType, String xsdData) throws Exception {
		startAdminSession();

		KalturaMetadataProfile metadataProfile = new KalturaMetadataProfile();
		metadataProfile.metadataObjectType = objectType;
		metadataProfile.name = "mp" + System.currentTimeMillis();
		
		metadataProfile = client.getMetadataProfileService().add(metadataProfile, xsdData);
		assertNotNull(metadataProfile.id);
		
		return metadataProfile;
	}

	protected KalturaCategoryEntry addEntryToCategory(String entryId, int categoryId) throws Exception {
		startAdminSession();

		KalturaCategoryEntry categoryEntry = new KalturaCategoryEntry();
		categoryEntry.entryId = entryId;
		categoryEntry.categoryId = categoryId;
		
		categoryEntry = client.getCategoryEntryService().add(categoryEntry);
		assertNotNull(categoryEntry.createdAt);
		
		return categoryEntry;
	}

	protected KalturaMediaEntry createEntry() throws Exception {
		startAdminSession();

		KalturaMediaEntry entry = new KalturaMediaEntry();
		entry.mediaType = KalturaMediaType.VIDEO;
		
		entry = client.getMediaService().add(entry);
		assertNotNull(entry.id);
		
		return entry;
	}

	protected KalturaCategory createCategory() throws Exception {
		startAdminSession();

		KalturaCategory category = new KalturaCategory();
		category.name = "c" + System.currentTimeMillis();
		
		category = client.getCategoryService().add(category);
		assertNotNull(category.id);
		
		return category;
	}

	protected void deleteCategory(int id) throws Exception {
		startAdminSession();
		client.getCategoryService().delete(id);
	}

	protected void deleteEntry(String id) throws Exception {
		startAdminSession();
		client.getBaseEntryService().delete(id);
	}

	protected void deleteResponseProfile(int id) throws Exception {
		startAdminSession();
		client.getResponseProfileService().delete(id);
	}

	protected void deleteMetadataProfile(int id) throws Exception {
		startAdminSession();
		client.getMetadataProfileService().delete(id);
	}
	
}

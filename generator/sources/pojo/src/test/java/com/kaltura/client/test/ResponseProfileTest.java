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
			metadataFilter.setMetadataObjectTypeEqual(KalturaMetadataObjectType.CATEGORY);
			metadataFilter.setMetadataProfileIdEqual(categoryMetadataProfile.getId());

			KalturaResponseProfileMapping metadataMapping = new KalturaResponseProfileMapping();
			metadataMapping.setFilterProperty("objectIdEqual");
			metadataMapping.setParentProperty("categoryId");
			
			ArrayList<KalturaResponseProfileMapping> metadataMappings = new ArrayList<KalturaResponseProfileMapping>();
			metadataMappings.add(metadataMapping);

			KalturaDetachedResponseProfile metadataResponseProfile = new KalturaDetachedResponseProfile();
			metadataResponseProfile.setName("metadata");
			metadataResponseProfile.setFilter(metadataFilter);
			metadataResponseProfile.setMappings(metadataMappings);
			
			ArrayList<KalturaDetachedResponseProfile> categoryEntryRelatedProfiles = new ArrayList<KalturaDetachedResponseProfile>();
			categoryEntryRelatedProfiles.add(metadataResponseProfile);

			KalturaCategoryEntryFilter categoryEntryFilter = new KalturaCategoryEntryFilter();
			
			KalturaResponseProfileMapping categoryEntryMapping = new KalturaResponseProfileMapping();
			categoryEntryMapping.setFilterProperty("entryIdEqual");
			categoryEntryMapping.setParentProperty("id");
			
			ArrayList<KalturaResponseProfileMapping> categoryEntryMappings = new ArrayList<KalturaResponseProfileMapping>();
			categoryEntryMappings.add(categoryEntryMapping);
			
			KalturaDetachedResponseProfile categoryEntryResponseProfile = new KalturaDetachedResponseProfile();
			categoryEntryResponseProfile.setName("categoryEntry");
			categoryEntryResponseProfile.setRelatedProfiles(categoryEntryRelatedProfiles);
			categoryEntryResponseProfile.setFilter(categoryEntryFilter);
			categoryEntryResponseProfile.setMappings(categoryEntryMappings);
			
			ArrayList<KalturaDetachedResponseProfile> entryRelatedProfiles = new ArrayList<KalturaDetachedResponseProfile>();
			entryRelatedProfiles.add(categoryEntryResponseProfile);
			
			responseProfile = new KalturaResponseProfile();
			responseProfile.setName("rp" + System.currentTimeMillis());
			responseProfile.setSystemName(responseProfile.getName());
			responseProfile.setRelatedProfiles(entryRelatedProfiles);
			
			responseProfile = client.getResponseProfileService().add(responseProfile);
			assertNotNull(responseProfile.getId());
			assertNotNull(responseProfile.getRelatedProfiles());
			assertEquals(1, responseProfile.getRelatedProfiles().size());
			
			KalturaCategoryEntry categoryEntry = addEntryToCategory(entry.getId(), category.getId());
			KalturaMetadata categoryMetadata = createMetadata(KalturaMetadataObjectType.CATEGORY, Integer.toString(category.getId()), categoryMetadataProfile.getId(), xml);
			
			KalturaResponseProfileHolder responseProfileHolder = new KalturaResponseProfileHolder();
			responseProfileHolder.setId(responseProfile.getId());
	
			startAdminSession();
			client.setResponseProfile(responseProfileHolder);
			KalturaMediaEntry getEntry = client.getMediaService().get(entry.getId());
			assertEquals(getEntry.getId(), entry.getId());
			
			assertNotNull(getEntry.getRelatedObjects());
			assertTrue(getEntry.getRelatedObjects().containsKey(categoryEntryResponseProfile.getName()));
			KalturaCategoryEntryListResponse categoryEntryList = (KalturaCategoryEntryListResponse) getEntry.getRelatedObjects().get(categoryEntryResponseProfile.getName());
			assertEquals(1, categoryEntryList.getTotalCount());
			KalturaCategoryEntry getCategoryEntry = categoryEntryList.getObjects().get(0);
			assertEquals(getCategoryEntry.getCreatedAt(), categoryEntry.getCreatedAt());

			assertNotNull(getCategoryEntry.getRelatedObjects());
			assertTrue(getCategoryEntry.getRelatedObjects().containsKey(metadataResponseProfile.getName()));
			KalturaMetadataListResponse metadataList = (KalturaMetadataListResponse) getCategoryEntry.getRelatedObjects().get(metadataResponseProfile.getName());
			assertEquals(1, metadataList.getTotalCount());
			KalturaMetadata getMetadata = metadataList.getObjects().get(0);
			assertEquals(categoryMetadata.getId(), getMetadata.getId());
			assertEquals(xml, getMetadata.getXml());
		}
		finally{
			if(responseProfile != null && responseProfile.getId() > 0)
				deleteResponseProfile(responseProfile.getId());

			if(entry != null && entry.getId() != null)
				deleteEntry(entry.getId());

			if(category != null && category.getId() > 0)
				deleteCategory(category.getId());

			if(categoryMetadataProfile != null && categoryMetadataProfile.getId() > 0)
				deleteMetadataProfile(categoryMetadataProfile.getId());
		}
	}

	protected KalturaMetadata createMetadata(KalturaMetadataObjectType objectType, String objectId, int metadataProfileId, String xmlData) throws Exception {
		startAdminSession();

		KalturaMetadata metadata = client.getMetadataService().add(metadataProfileId, objectType, objectId, xmlData);
		assertNotNull(metadata.getId());
		
		return metadata;
	}

	protected KalturaMetadataProfile createMetadataProfile(KalturaMetadataObjectType objectType, String xsdData) throws Exception {
		startAdminSession();

		KalturaMetadataProfile metadataProfile = new KalturaMetadataProfile();
		metadataProfile.setMetadataObjectType(objectType);
		metadataProfile.setName("mp" + System.currentTimeMillis());
		
		metadataProfile = client.getMetadataProfileService().add(metadataProfile, xsdData);
		assertNotNull(metadataProfile.getId());
		
		return metadataProfile;
	}

	protected KalturaCategoryEntry addEntryToCategory(String entryId, int categoryId) throws Exception {
		startAdminSession();

		KalturaCategoryEntry categoryEntry = new KalturaCategoryEntry();
		categoryEntry.setEntryId(entryId);
		categoryEntry.setCategoryId(categoryId);
		
		categoryEntry = client.getCategoryEntryService().add(categoryEntry);
		assertNotNull(categoryEntry.getCreatedAt());
		
		return categoryEntry;
	}

	protected KalturaMediaEntry createEntry() throws Exception {
		startAdminSession();

		KalturaMediaEntry entry = new KalturaMediaEntry();
		entry.setMediaType(KalturaMediaType.VIDEO);
		
		entry = client.getMediaService().add(entry);
		assertNotNull(entry.getId());
		
		return entry;
	}

	protected KalturaCategory createCategory() throws Exception {
		startAdminSession();

		KalturaCategory category = new KalturaCategory();
		category.setName("c" + System.currentTimeMillis());
		
		category = client.getCategoryService().add(category);
		assertNotNull(category.getId());
		
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

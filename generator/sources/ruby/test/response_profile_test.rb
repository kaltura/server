# ===================================================================================================
#                           _  __     _ _
#                          | |/ /__ _| | |_ _  _ _ _ __ _
#                          | ' </ _` | |  _| || | '_/ _` |
#                          |_|\_\__,_|_|\__|\_,_|_| \__,_|
#
# This file is part of the Kaltura Collaborative Media Suite which allows users
# to do with audio, video, and animation what Wiki platfroms allow them to do with
# text.
#
# Copyright (C) 2006-2011  Kaltura Inc.
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU Affero General Public License as
# published by the Free Software Foundation, either version 3 of the
# License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU Affero General Public License for more details.
#
# You should have received a copy of the GNU Affero General Public License
# along with this program.  If not, see <http:#www.gnu.org/licenses/>.
#
# @ignore
# ===================================================================================================
require 'test_helper'

class ResponseProfileTest < Test::Unit::TestCase
	def uniqid(prefix)
		return prefix + ('a'..'z').to_a.shuffle[0,8].join
	end

	def createEntry()
		entry = Kaltura::KalturaMediaEntry.new()
		entry.media_type = Kaltura::KalturaMediaType::VIDEO
		entry.name = uniqid('test_')
		entry.description = uniqid('test ')
		entry.tags = @uniqueTag

		entry = @client.media_service.add(entry)

		return entry
	end

	def createMetadata(metadataProfileId, objectType, objectId, xmlData)
		metadata = Kaltura::KalturaMetadata.new()
		metadata.metadata_object_type = objectType
		metadata.object_id = objectId

		metadata = @client.metadata_service.add(metadataProfileId, objectType, objectId, xmlData)

		return metadata
	end

	def createMetadataProfile(objectType, xsdData)
		metadataProfile = Kaltura::KalturaMetadataProfile.new()
		metadataProfile.metadata_object_type = objectType
		metadataProfile.name = uniqid('test_')
		metadataProfile.system_name = uniqid('test_')

		metadataProfile = @client.metadata_profile_service.add(metadataProfile, xsdData)

		return metadataProfile
	end

	def createEntriesWithMetadataObjects(entriesCount, metadataProfileCount = 2)
		entries = []
		metadataProfiles = {}

		for i in 1 .. metadataProfileCount
			index = i.to_s
			xsd = '<xsd:schema xmlns:xsd="http://www.w3.org/2001/XMLSchema">
	<xsd:element name="metadata">
		<xsd:complexType>
			<xsd:sequence>
				<xsd:element name="Choice' + index + '" minOccurs="0" maxOccurs="1">
					<xsd:annotation>
						<xsd:documentation></xsd:documentation>
						<xsd:appinfo>
							<label>Example choice ' + index + '</label>
							<key>choice' + index + '</key>
							<searchable>true</searchable>
							<description>Example choice ' + index + '</description>
						</xsd:appinfo>
					</xsd:annotation>
					<xsd:simpleType>
						<xsd:restriction base="listType">
							<xsd:enumeration value="on" />
							<xsd:enumeration value="off" />
						</xsd:restriction>
					</xsd:simpleType>
				</xsd:element>
				<xsd:element name="FreeText' + index + '" minOccurs="0" maxOccurs="1" type="textType">
					<xsd:annotation>
						<xsd:documentation></xsd:documentation>
						<xsd:appinfo>
							<label>Free text ' + index + '</label>
							<key>freeText' + index + '</key>
							<searchable>true</searchable>
							<description>Free text ' + index + '</description>
						</xsd:appinfo>
					</xsd:annotation>
				</xsd:element>
			</xsd:sequence>
		</xsd:complexType>
	</xsd:element>
	<xsd:complexType name="textType">
		<xsd:simpleContent>
			<xsd:extension base="xsd:string" />
		</xsd:simpleContent>
	</xsd:complexType>
	<xsd:complexType name="objectType">
		<xsd:simpleContent>
			<xsd:extension base="xsd:string" />
		</xsd:simpleContent>
	</xsd:complexType>
	<xsd:simpleType name="listType">
		<xsd:restriction base="xsd:string" />
	</xsd:simpleType>
</xsd:schema>'

			metadataProfiles[i.to_s] = createMetadataProfile(Kaltura::KalturaMetadataObjectType::ENTRY, xsd)
		end

		for i in 1 .. entriesCount
			entry = createEntry()
			entries.push(entry)

			for j in 1 .. metadataProfileCount
				index = j.to_s
				xml = '<metadata>
	<Choice' + index + '>on</Choice' + index + '>
	<FreeText' + index + '>example text ' + index + '</FreeText' + index + '>
</metadata>'

				createMetadata(metadataProfiles[j.to_s].id, Kaltura::KalturaMetadataObjectType::ENTRY, entry.id, xml)
			end
		end

		return [entries, metadataProfiles]
	end

	# this test adds entries, metadata-profiles and  metadata objects and retrieves the list of the entries with their metadata using response-profile
	should "list entries with their metadata using response-profile" do

		@uniqueTag = uniqid('tag_')
		entriesTotalCount = 4
		entriesPageSize = 4
		metadataPageSize = 2

		entries, metadataProfiles = createEntriesWithMetadataObjects(entriesTotalCount)

		entriesFilter = Kaltura::KalturaMediaEntryFilter.new()
		entriesFilter.tags_like = @uniqueTag
		entriesFilter.status_in = Kaltura::KalturaEntryStatus::PENDING + ',' + Kaltura::KalturaEntryStatus::NO_CONTENT

		entriesPager = Kaltura::KalturaFilterPager.new()
		entriesPager.page_size = entriesPageSize

		metadataFilter = Kaltura::KalturaMetadataFilter.new()
		metadataFilter.metadata_object_type_equal = Kaltura::KalturaMetadataObjectType::ENTRY

		metadataMapping = Kaltura::KalturaResponseProfileMapping.new()
		metadataMapping.filter_property = 'objectIdEqual'
		metadataMapping.parent_property = 'id'

		metadataPager = Kaltura::KalturaFilterPager.new()
		metadataPager.page_size = metadataPageSize

		metadataResponseProfile = Kaltura::KalturaDetachedResponseProfile.new()
		metadataResponseProfile.name = uniqid('test_')
		metadataResponseProfile.type = Kaltura::KalturaResponseProfileType::INCLUDE_FIELDS
		metadataResponseProfile.fields = 'id,objectId,createdAt, xml'
		metadataResponseProfile.filter = metadataFilter
		metadataResponseProfile.pager = metadataPager
		metadataResponseProfile.mappings = [metadataMapping]

		responseProfile = Kaltura::KalturaResponseProfile.new()
		responseProfile.name = uniqid('test_')
		responseProfile.system_name = uniqid('test_')
		responseProfile.type = Kaltura::KalturaResponseProfileType::INCLUDE_FIELDS
		responseProfile.fields = 'id,name,createdAt'
		responseProfile.related_profiles = [metadataResponseProfile]

		responseProfile = @client.response_profile_service.add(responseProfile)

		nestedResponseProfile = Kaltura::KalturaResponseProfileHolder.new()
		nestedResponseProfile.id = responseProfile.id

		@client.response_profile = nestedResponseProfile
		list = @client.base_entry_service.list(entriesFilter, entriesPager)

		assert_equal(entriesTotalCount, list.total_count)
		assert_equal(entriesPageSize, list.objects.size())

		list.objects.each do |entry|
			assert_equal(entry.related_objects != nil, true)
			assert_equal(entry.related_objects.include?(metadataResponseProfile.name), true)
			assert_equal(metadataProfiles.size(), entry.related_objects[metadataResponseProfile.name].objects.size())

			entry.related_objects[metadataResponseProfile.name].objects.each do |metadata|
				assert_equal(entry.id, metadata.object_id)
			end
		end
	end
end
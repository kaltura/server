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
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU Affero General Public License for more details.
#
# You should have received a copy of the GNU Affero General Public License
# along with this program.  If not, see <http:#www.gnu.org/licenses/>.
#
# @ignore
# ===================================================================================================
require 'test_helper'

class MetadataProfileServiceTest < Test::Unit::TestCase
  
  # this test adds a metadata_profile and retrieves the list of metadata_profiles to demonstrate the use of kaltura plugins.
  should "creates a metadata_profile and get the metadata_profile list" do
    
    # cleaning up the list
    metadata_profile_filter = Kaltura::KalturaMetadataProfileFilter.new
    filter_pager = Kaltura::KalturaFilterPager.new
    metadata_profile_list = @client.metadata_profile_service.list(metadata_profile_filter, filter_pager)
    metadata_profile_list.objects.each do |obj|
      @client.metadata_profile_service.delete(obj.id) rescue nil
    end  if metadata_profile_list.objects
      
     # creates a metadata_profile  
     metadata_profile = Kaltura::KalturaMetadataProfile.new
     metadata_profile.name = "test profile"
     metadata_profile.metadata_object_type = Kaltura::KalturaMetadataObjectType::ENTRY 
     metadata_profile.create_mode = Kaltura::KalturaMetadataProfileCreateMode::API 
     
     created_metadata_profile = @client.metadata_profile_service.add(metadata_profile, "<xsd:schema></xsd:schema>", "viewsData") 

     assert_not_nil created_metadata_profile.id

     # list the metadata_profiles 
     metadata_profile_filter = Kaltura::KalturaMetadataProfileFilter.new
     filter_pager = Kaltura::KalturaFilterPager.new      
     
     metadata_profile_list = @client.metadata_profile_service.list(metadata_profile_filter, filter_pager)    
     
     assert_equal metadata_profile_list.total_count, 1
     
    assert_nil @client.metadata_profile_service.delete(created_metadata_profile.id)   
  end
end
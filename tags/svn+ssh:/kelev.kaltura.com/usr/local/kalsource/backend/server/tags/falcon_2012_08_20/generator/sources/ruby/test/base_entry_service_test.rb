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

class BaseEntryServiceTest < Test::Unit::TestCase
  
  # this test uploads a file to kaltura and creates an entry using the uploaded file.
  should "upload a file and create an entry" do
      
      base_entry = Kaltura::KalturaBaseEntry.new
      base_entry.type = Kaltura::KalturaEntryType::DOCUMENT
      base_entry.name = "kaltura_test"
      pdf_file = File.open("test/media/test.pdf")
      
      pdf_token = @client.base_entry_service.upload(pdf_file)
      created_entry = @client.base_entry_service.add_from_uploaded_file(base_entry, pdf_token)
      
      assert_not_nil created_entry.id
      assert_nil @client.base_entry_service.delete(created_entry.id)
   end
  
  # this test uploads a file to kaltura and creates an entry using the uploaded file.   
   should "upload a file and create an entry" do
       
       base_entry = Kaltura::KalturaBaseEntry.new
       base_entry.type = Kaltura::KalturaEntryType::AUTOMATIC
       base_entry.name = "kaltura_test"
       swf_file = File.open("test/media/test.swf")
       
       pdf_token = @client.base_entry_service.upload(swf_file)
       created_entry = @client.base_entry_service.add_from_uploaded_file(base_entry, pdf_token)
       
       assert_not_nil created_entry.id
       assert_nil @client.base_entry_service.delete(created_entry.id)
    end
    
    # this test simulates an api response with 'not supported' attributes and try to parse and generate an entry object out of it.
    should "silently ignore any fields returned from the server that it does not recognize" do
        
        response_body = 
        <<-XML
        <?xml version='1.0' encoding='utf-8'?><xml><result><objectType>KalturaBaseEntry</objectType><id>0_npdg4rrs</id><not_supported_attr>not_supported_attr val</not_supported_attr><name>102_1321456940</name><description></description><partnerId>102</partnerId><userId></userId><tags></tags><adminTags></adminTags><categories></categories><status>2</status><moderationStatus>6</moderationStatus><moderationCount>0</moderationCount><type>10</type><createdAt>1321456940</createdAt><rank>0</rank><totalRank>0</totalRank><votes>0</votes><groupId></groupId><partnerData></partnerData><downloadUrl>http://ec2-174-129-124-16.compute-1.amazonaws.com/p/102/sp/10200/raw/entry_id/0_npdg4rrs/version/100000</downloadUrl><searchText>  102_1321456940 </searchText><licenseType>-1</licenseType><version>100000</version><thumbnailUrl>http://ec2-174-129-124-16.compute-1.amazonaws.com/p/102/sp/10200/thumbnail/entry_id/0_npdg4rrs/version/0</thumbnailUrl><accessControlId>4</accessControlId><startDate></startDate><endDate></endDate></result><executionTime>0.110525846481</executionTime></xml>
        XML
        created_entry = @client.parse_to_objects(response_body)
        
        assert_instance_of Kaltura::KalturaBaseEntry, created_entry
        assert_not_nil created_entry.id
     end
    
    # this test creates an entry and retrieves the list of entries and count from kaltura by setting a filter.
    should "get the base entry list" do
      
      # cleaning up the list
      base_entry_filter = Kaltura::KalturaBaseEntryFilter.new
      base_entry_filter.name_multi_like_or = "kaltura_test"
      filter_pager = Kaltura::KalturaFilterPager.new
      base_entry_list = @client.base_entry_service.list(base_entry_filter, filter_pager)
      base_entry_list.objects.each do |obj|
        @client.base_entry_service.delete(obj.id) rescue nil
      end  if base_entry_list.objects
      
      base_entry = Kaltura::KalturaBaseEntry.new
      base_entry.type = Kaltura::KalturaEntryType::AUTOMATIC
      base_entry.name = "kaltura_test"
      swf_file = File.open("test/media/test.swf")
      
      pdf_token = @client.base_entry_service.upload(swf_file)
      created_entry = @client.base_entry_service.add_from_uploaded_file(base_entry, pdf_token)
      
      assert_not_nil created_entry.id
      
      base_entry_filter = Kaltura::KalturaBaseEntryFilter.new
      base_entry_filter.name_multi_like_or = "kaltura_test"
      filter_pager = Kaltura::KalturaFilterPager.new
      base_entry_list = @client.base_entry_service.list(base_entry_filter, filter_pager)
      
      assert_equal base_entry_list.total_count, 1
      
      count = @client.base_entry_service.count(base_entry_filter)     
      
      assert_equal count.to_i, 1
            
      assert_nil @client.base_entry_service.delete(created_entry.id)
    end
    
    # this test creates an entry and retrieves it back using the id.
    should "get the base entry" do
      
      base_entry = Kaltura::KalturaBaseEntry.new
      base_entry.type = Kaltura::KalturaEntryType::DOCUMENT
      base_entry.name = "kaltura_test"
      swf_file = File.open("test/media/test.pdf")
      
      pdf_token = @client.base_entry_service.upload(swf_file)
      created_entry = @client.base_entry_service.add_from_uploaded_file(base_entry, pdf_token)
      
      assert_not_nil created_entry.id
      
      base_entry = @client.base_entry_service.get(created_entry.id)
      
      assert_not_nil base_entry
      assert_instance_of Kaltura::KalturaDocumentEntry, base_entry
      assert_equal base_entry.id, created_entry.id
      assert_nil @client.base_entry_service.delete(base_entry.id)
    end
    
    # this test creates couple of entries and retrieves them back using the ids
    should "get the base entries using the ids" do
      
      base_entry = Kaltura::KalturaBaseEntry.new
      base_entry.type = Kaltura::KalturaEntryType::DOCUMENT
      base_entry.name = "kaltura_test"
      swf_file = File.open("test/media/test.pdf")
      
      pdf_token = @client.base_entry_service.upload(swf_file)
      created_entry1 = @client.base_entry_service.add_from_uploaded_file(base_entry, pdf_token)
      
      assert_not_nil created_entry1.id
      
      base_entry = Kaltura::KalturaBaseEntry.new
      base_entry.type = Kaltura::KalturaEntryType::AUTOMATIC
      base_entry.name = "kaltura_test"
      swf_file = File.open("test/media/test.swf")
      
      pdf_token = @client.base_entry_service.upload(swf_file)
      created_entry2 = @client.base_entry_service.add_from_uploaded_file(base_entry, pdf_token)
      
      assert_not_nil created_entry2.id
      
      base_entry_list = @client.base_entry_service.get_by_ids("#{created_entry1.id},#{created_entry2.id}")
      
      assert_not_nil base_entry_list
      assert_instance_of Array, base_entry_list
      assert_equal base_entry_list.count, 2          
      assert_nil @client.base_entry_service.delete(created_entry1.id)
      assert_nil @client.base_entry_service.delete(created_entry2.id)
    end
    
    # this test tries toretrieve an entry with invalid id.
    should "throw an error for invalid base entry id" do
      
      assert_raise Kaltura::KalturaAPIError do
        @client.base_entry_service.get("invalid_base_entry_id")
      end
      
    end    
    
    # this test creates an entry and updates the metadata of it.
    should "update the base entry metadata" do
      
      base_entry = Kaltura::KalturaBaseEntry.new
      base_entry.type = Kaltura::KalturaEntryType::AUTOMATIC
      base_entry.name = "kaltura test"
      swf_file = File.open("test/media/test.swf")
      
      pdf_token = @client.base_entry_service.upload(swf_file)
      created_entry = @client.base_entry_service.add_from_uploaded_file(base_entry, pdf_token)
      
      assert_not_nil created_entry.id
      
      base_entry = Kaltura::KalturaBaseEntry.new
      base_entry.name = "kaltura test updated"
      base_entry.description = "kaltura test description"
      base_entry_updated = @client.base_entry_service.update(created_entry.id, base_entry)
      
      assert_not_nil base_entry_updated
      assert_instance_of Kaltura::KalturaBaseEntry, base_entry_updated
      assert_equal base_entry_updated.name, "kaltura test updated"
      assert_equal base_entry_updated.description, "kaltura test description"
      
      assert_nil @client.base_entry_service.delete(base_entry_updated.id)
    end
    
    # this test creates an entry and updates it's thumbnail.
    should "upload a thumbnail for the base entry " do
    
      base_entry = Kaltura::KalturaBaseEntry.new
      base_entry.type = Kaltura::KalturaEntryType::DOCUMENT
      base_entry.name = "kaltura_test"
      pdf_file = File.open("test/media/test.pdf")
      
      pdf_token = @client.base_entry_service.upload(pdf_file)
      created_entry = @client.base_entry_service.add_from_uploaded_file(base_entry, pdf_token)
      
      assert_not_nil created_entry.id
    
      img_file = File.open("test/media/test.png")
            
      updated_entry = @client.base_entry_service.update_thumbnail_jpeg(created_entry.id, img_file)
      
      assert_not_nil updated_entry.thumbnail_url     
      assert_not_equal updated_entry.thumbnail_url, created_entry.thumbnail_url
      assert_nil @client.base_entry_service.delete(updated_entry.id)
    end
    
    # this test creates an entry and set it's moderation flags.
    should "set the moderation flags" do
    
      base_entry = Kaltura::KalturaBaseEntry.new
      base_entry.type = Kaltura::KalturaEntryType::DOCUMENT
      base_entry.name = "kaltura_test"
      pdf_file = File.open("test/media/test.pdf")
      
      pdf_token = @client.base_entry_service.upload(pdf_file)
      created_entry = @client.base_entry_service.add_from_uploaded_file(base_entry, pdf_token)
      
      assert_not_nil created_entry.id
      
      # first list the flags. should be empty      
      moderation_flag_list = @client.base_entry_service.list_flags(created_entry.id)
      
      assert_equal moderation_flag_list.total_count, 0

      # add a new flag for moderate
      flag = Kaltura::KalturaModerationFlag.new
      flag.flagged_entry_id = created_entry.id
      flag.flag_type = Kaltura::KalturaModerationFlagType::SEXUAL_CONTENT
      flag = @client.base_entry_service.flag(flag)
      
      # list the flags, should be 1
      moderation_flag_list = @client.base_entry_service.list_flags(created_entry.id)
      
      assert_equal moderation_flag_list.total_count, 1
      assert_equal moderation_flag_list.objects[0].status, Kaltura::KalturaModerationFlagStatus::PENDING
      
      # approve the flags
      @client.base_entry_service.approve(created_entry.id)
      
      # list the flags, should be empty
      moderation_flag_list = @client.base_entry_service.list_flags(created_entry.id)
      
      assert_equal moderation_flag_list.total_count, 0
      
      # get the entry and check the moderation status
      created_entry = @client.base_entry_service.get(created_entry.id)
      
      assert_equal created_entry.moderation_status, Kaltura::KalturaEntryModerationStatus::APPROVED
      
      assert_nil @client.base_entry_service.delete(created_entry.id)
    end

end

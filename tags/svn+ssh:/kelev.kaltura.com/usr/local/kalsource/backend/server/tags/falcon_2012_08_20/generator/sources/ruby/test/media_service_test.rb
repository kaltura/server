# coding: utf-8

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
require 'uri'

class MediaServiceTest < Test::Unit::TestCase
  
  # this test uploads a video file to kaltura and creates a media entry using the uploaded file.
  should "upload a video file and create a kaltura entry" do
        
    media_entry = Kaltura::KalturaMediaEntry.new
    media_entry.name = "kaltura_test1"
    media_entry.media_type = Kaltura::KalturaMediaType::VIDEO
    video_file = File.open("test/media/test.wmv")
    
    video_token = @client.media_service.upload(video_file)
    created_entry1 = @client.media_service.add_from_uploaded_file(media_entry, video_token)
    
    assert_not_nil created_entry1.id
    assert_nil @client.media_service.delete(created_entry1.id)
  end
  
  # this test uploads a image file to kaltura and creates a media entry using the uploaded file.  
  should "upload a image file and create a kaltura entry" do
    
    media_entry = Kaltura::KalturaMediaEntry.new
    media_entry.name = "kaltura_test2"
    media_entry.media_type = Kaltura::KalturaMediaType::IMAGE
    video_file = File.open("test/media/test.png")
    
    video_token = @client.media_service.upload(video_file)
    created_entry2 = @client.media_service.add_from_uploaded_file(media_entry, video_token)
    
    assert_not_nil created_entry2.id
    assert_nil @client.media_service.delete(created_entry2.id)
  end
  
  # this test uploads a video file to kaltura reading from a url and creates a media entry using the uploaded file.  
  should "upload a flv file from url and create a kaltura entry" do
    
    media_entry = Kaltura::KalturaMediaEntry.new
    media_entry.name = "kaltura_test3"
    media_entry.media_type = Kaltura::KalturaMediaType::VIDEO
    
    created_entry3 = @client.media_service.add_from_url(media_entry, URI.escape("http://www.kaltura.org/kalorg/kaltura-api-clients-generators/trunk/sources/csharp/KalturaClientTester/DemoVideo.flv", Regexp.new("[^#{URI::PATTERN::UNRESERVED}]")))
    
    assert_not_nil created_entry3.id
    assert_equal created_entry3.status, Kaltura::KalturaEntryStatus::IMPORT
    assert_nil @client.media_service.delete(created_entry3.id)
  end
  
  # this test creates an entry and ranks it  
  should "set the rank attributes" do
    
    media_entry = Kaltura::KalturaMediaEntry.new
    media_entry.name = "kaltura_test"
    media_entry.media_type = Kaltura::KalturaMediaType::VIDEO
    video_file = File.open("test/media/test.wmv")
    
    video_token = @client.media_service.upload(video_file)
    created_entry = @client.media_service.add_from_uploaded_file(media_entry, video_token)
        
    assert_not_nil created_entry.id
    assert_equal created_entry.rank, 0
    assert_equal created_entry.total_rank, 0
    assert_equal created_entry.votes, 0
    
    @client.media_service.anonymous_rank(created_entry.id, 5)
    created_entry = @client.media_service.get(created_entry.id)
        
    assert_equal created_entry.rank, 5
    assert_equal created_entry.total_rank, 5
    assert_equal created_entry.votes, 1
    
    @client.media_service.anonymous_rank(created_entry.id, 2)
    created_entry = @client.media_service.get(created_entry.id)
        
    assert_equal created_entry.rank, 3.5
    assert_equal created_entry.total_rank, 7
    assert_equal created_entry.votes, 2
            
    assert_nil @client.media_service.delete(created_entry.id)
  end
  
  # this test creates an entry and updates the thumbnail of it using a image url  
  should "update the thumbnail from url" do
    
    media_entry = Kaltura::KalturaMediaEntry.new
    media_entry.name = "kaltura_test"
    media_entry.media_type = Kaltura::KalturaMediaType::VIDEO
    video_file = File.open("test/media/test.wmv")
    
    video_token = @client.media_service.upload(video_file)
    created_entry = @client.media_service.add_from_uploaded_file(media_entry, video_token)
        
    assert_not_nil created_entry.id
    
    updated_entry = @client.media_service.update_thumbnail_from_url(created_entry.id, "http://corp.kaltura.com/sites/kaltura-website/files/logo_0.png")
        
    assert_not_nil updated_entry
    assert_not_nil updated_entry.thumbnail_url
    assert_not_equal created_entry.thumbnail_url, updated_entry.thumbnail_url
            
    assert_nil @client.media_service.delete(created_entry.id)
  end
  
  # this test tries to create a media entry with invalid token.
  should "not create a media entry for a invalid token" do
       
    assert_raise Kaltura::KalturaAPIError do
      media_entry = Kaltura::KalturaMediaEntry.new
      media_entry.media_type = Kaltura::KalturaMediaType::VIDEO
  
      @created_entry = @client.media_service.add_from_uploaded_file(media_entry, "invalid token")
    end  
  end 
  
  # this test validates the response of a multi request when thre are no requests in the queue 
  should "get empty array for multi request when there are no requests in the queue" do
    
    @client.start_multirequest
    
    retVal = @client.do_multirequest
    
    assert_equal retVal, [] 
  end 
  
  # this test starts multirequest couple of times  
  should "not raise any errors for double start multi request" do
    
    assert_nothing_raised Kaltura::KalturaAPIError do
        @client.start_multirequest
        @client.start_multirequest
    end
  end
  
  # this test calls do_multirequest without stating it. 
  should "get an empty array for the multirequest when it calls without starting it" do
    
    media_entry = Kaltura::KalturaMediaEntry.new
    media_entry.name = "kaltura_test3"
    media_entry.media_type = Kaltura::KalturaMediaType::VIDEO
    video_file = File.open("test/media/test.wmv")
    
    video_token = @client.media_service.upload(video_file)
    
    ceated_media_entry = @client.media_service.add_from_uploaded_file(media_entry, video_token)
    
    media_entry_filter = Kaltura::KalturaMediaEntryFilter.new
    media_entry_filter.name_multi_like_or = "kaltura_test3"
    filter_pager = Kaltura::KalturaFilterPager.new
    @client.media_service.list(media_entry_filter, filter_pager)
    
    retVal = @client.do_multirequest
    
    assert_equal retVal, [] 
    
    assert_nil @client.media_service.delete(ceated_media_entry.id)  
  end
  
  # this test uses multi request to retrieve the media entry list and create a new entry.  
  should "get the media list and create a media entry" do
    
    media_entry = Kaltura::KalturaMediaEntry.new
    media_entry.name = "kaltura_test3"
    media_entry.media_type = Kaltura::KalturaMediaType::VIDEO
    video_file = File.open("test/media/test.wmv")
    
    video_token = @client.media_service.upload(video_file)
    
    @client.start_multirequest
    
    @client.media_service.add_from_uploaded_file(media_entry, video_token)
    
    media_entry_filter = Kaltura::KalturaMediaEntryFilter.new
    media_entry_filter.name_multi_like_or = "kaltura_test3"
    filter_pager = Kaltura::KalturaFilterPager.new
    @client.media_service.list(media_entry_filter, filter_pager)
    
    retVal = @client.do_multirequest
    
    assert_instance_of Kaltura::KalturaMediaEntry, retVal[0]
    assert_instance_of Kaltura::KalturaMediaListResponse, retVal[1]    
    assert_not_nil retVal[0].id
    assert_not_nil retVal[1].total_count
    assert_nil @client.media_service.delete(retVal[0].id)  
  end
  
  # this test retrieves error objects when a action in a multi request fails.  
  should "return error objects for failed actions" do
    
    media_entry = Kaltura::KalturaMediaEntry.new
    media_entry.media_type = Kaltura::KalturaMediaType::VIDEO
    
    @client.start_multirequest
    
    @client.media_service.add_from_uploaded_file(media_entry, "invalid_video_token")
    
    media_entry_filter = Kaltura::KalturaMediaEntryFilter.new
    filter_pager = Kaltura::KalturaFilterPager.new
    @client.media_service.list(media_entry_filter, filter_pager)
    
    retVal = @client.do_multirequest
        
    assert_instance_of Kaltura::KalturaAPIError, retVal[0]
    assert_instance_of Kaltura::KalturaMediaListResponse, retVal[1]
    assert_not_nil retVal[1].total_count
  end
  
  # this test uses utf8 content in the media entry meta data.
  should "support UTF content in entry metadata" do
    
    media_entry = Kaltura::KalturaMediaEntry.new
    media_entry.name = "שלום"
    media_entry.description = @description
    
    media_entry.media_type = Kaltura::KalturaMediaType::VIDEO
    video_file = File.open("test/media/test.wmv")
    
    video_token = @client.media_service.upload(video_file)
    created_entry = @client.media_service.add_from_uploaded_file(media_entry, video_token)
    
    assert_not_nil created_entry.id
    
    media_entry = @client.base_entry_service.get(created_entry.id)
    
    assert_equal media_entry.name, "שלום"
    assert_equal media_entry.description, @description
    assert_nil @client.media_service.delete(media_entry.id)
  end
  
  # this test resets content in the media entry meta data.
  should "support setting nil for content in entry metadata" do
    
    media_entry = Kaltura::KalturaMediaEntry.new
    media_entry.name = "kaltura_test1"
    media_entry.description = "kaltura_test1 description"
    media_entry.media_type = Kaltura::KalturaMediaType::VIDEO
    video_file = File.open("test/media/test.wmv")
    
    video_token = @client.media_service.upload(video_file)
    created_entry = @client.media_service.add_from_uploaded_file(media_entry, video_token)
    
    assert_not_nil created_entry.id
    
    media_entry = @client.base_entry_service.get(created_entry.id)
    
    assert_equal media_entry.name, "kaltura_test1"
    assert_equal media_entry.description, "kaltura_test1 description"
    
    # update the desc with value
    media_entry = Kaltura::KalturaBaseEntry.new
    media_entry.name = "kaltura_test1 updated"
    media_entry.description = nil
    media_entry_updated = @client.base_entry_service.update(created_entry.id, media_entry)
    
    assert_equal media_entry_updated.description, nil
    
    assert_nil @client.media_service.delete(media_entry_updated.id)
  end
  
  def setup
    super
    @description=
    <<-DESC
          שלום עולם
    DESC
  end
  
end

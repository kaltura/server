# ===================================================================================================
#													 _	__		 _ _
#													| |/ /__ _| | |_ _	_ _ _ __ _
#													| ' </ _` | |	_| || | '_/ _` |
#													|_|\_\__,_|_|\__|\_,_|_| \__,_|
#
# This file is part of the Kaltura Collaborative Media Suite which allows users
# to do with audio, video, and animation what Wiki platfroms allow them to do with
# text.
#
# Copyright (C) 2006-2011	Kaltura Inc.
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU Affero General Public License as
# published by the Free Software Foundation, either version 3 of the
# License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	See the
# GNU Affero General Public License for more details.
#
# You should have received a copy of the GNU Affero General Public License
# along with this program.	If not, see <http:#www.gnu.org/licenses/>.
#
# @ignore
# ===================================================================================================
require 'test_helper'

class BaseEntryServiceTest < Test::Unit::TestCase
	
	# this test uploads a file to kaltura and creates an entry using the uploaded file.
	should "upload a file and create an entry" do
			
		base_entry = Kaltura::KalturaBaseEntry.new
		base_entry.type = Kaltura::KalturaEntryType::MEDIA_CLIP
		base_entry.name = "kaltura_test"
		media_file = File.open("test/media/test.mov")

		upload_token = @client.upload_token_service.add()
		assert_not_nil upload_token.id
		upload_token = @client.upload_token_service.upload(upload_token.id, media_file)

		created_entry = @client.base_entry_service.add(base_entry)
		assert_not_nil created_entry.id
		
		resource = Kaltura::KalturaUploadedFileTokenResource.new
		resource.token = upload_token.id
		@client.media_service.add_content(created_entry.id, resource)
		
		assert_nil @client.base_entry_service.delete(created_entry.id)
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
		end	if base_entry_list.objects
		
		unique_id = (0...8).map { (97 + rand(26)).chr }.join
			
		base_entry = Kaltura::KalturaBaseEntry.new
		base_entry.type = Kaltura::KalturaEntryType::MEDIA_CLIP
		base_entry.name = "kaltura_test"
		base_entry.tags = unique_id
		media_file = File.open("test/media/test.mov")

		upload_token = @client.upload_token_service.add()
		assert_not_nil upload_token.id
		upload_token = @client.upload_token_service.upload(upload_token.id, media_file)

		created_entry = @client.base_entry_service.add(base_entry)
		assert_not_nil created_entry.id
		
		resource = Kaltura::KalturaUploadedFileTokenResource.new
		resource.token = upload_token.id
		@client.media_service.add_content(created_entry.id, resource)

		base_entry_filter = Kaltura::KalturaBaseEntryFilter.new
		base_entry_filter.tags_like = unique_id
		base_entry_filter.status_in = (0...8).to_a.join(",")
		filter_pager = Kaltura::KalturaFilterPager.new
		base_entry_list = @client.base_entry_service.list(base_entry_filter, filter_pager)
		
		assert_equal 1, base_entry_list.total_count
		
		count = @client.base_entry_service.count(base_entry_filter)		 
		assert_equal 1, count
					
		assert_nil @client.base_entry_service.delete(created_entry.id)
	end
	
	# this test creates an entry and retrieves it back using the id.
	should "get the base entry" do
		
		base_entry = Kaltura::KalturaBaseEntry.new
		base_entry.type = Kaltura::KalturaEntryType::MEDIA_CLIP
		base_entry.name = "kaltura_test"
		media_file = File.open("test/media/test.mov")

		upload_token = @client.upload_token_service.add()
		assert_not_nil upload_token.id
		upload_token = @client.upload_token_service.upload(upload_token.id, media_file)

		created_entry = @client.base_entry_service.add(base_entry)
		assert_not_nil created_entry.id
		
		resource = Kaltura::KalturaUploadedFileTokenResource.new
		resource.token = upload_token.id
		@client.media_service.add_content(created_entry.id, resource)

		base_entry = @client.base_entry_service.get(created_entry.id)
		
		assert_not_nil base_entry
		assert_instance_of Kaltura::KalturaMediaEntry, base_entry
		assert_equal base_entry.id, created_entry.id
		assert_nil @client.base_entry_service.delete(base_entry.id)
	end
	
	# this test creates couple of entries and retrieves them back using the ids
	should "get the base entries using the ids" do
		
		base_entry = Kaltura::KalturaBaseEntry.new
		base_entry.type = Kaltura::KalturaEntryType::MEDIA_CLIP
		base_entry.name = "kaltura_test"
		media_file = File.open("test/media/test.mov")

		upload_token = @client.upload_token_service.add()
		assert_not_nil upload_token.id
		upload_token = @client.upload_token_service.upload(upload_token.id, media_file)

		created_entry1 = @client.base_entry_service.add(base_entry)
		assert_not_nil created_entry1.id
		
		resource = Kaltura::KalturaUploadedFileTokenResource.new
		resource.token = upload_token.id
		@client.media_service.add_content(created_entry1.id, resource)

		base_entry = Kaltura::KalturaBaseEntry.new
		base_entry.type = Kaltura::KalturaEntryType::MEDIA_CLIP
		base_entry.name = "kaltura_test"
		media_file = File.open("test/media/test.mov")

		upload_token = @client.upload_token_service.add()
		assert_not_nil upload_token.id
		upload_token = @client.upload_token_service.upload(upload_token.id, media_file)

		created_entry2 = @client.base_entry_service.add(base_entry)
		assert_not_nil created_entry2.id
		
		resource = Kaltura::KalturaUploadedFileTokenResource.new
		resource.token = upload_token.id
		@client.media_service.add_content(created_entry2.id, resource)
		
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
		base_entry.type = Kaltura::KalturaEntryType::MEDIA_CLIP
		base_entry.name = "kaltura test"
		media_file = File.open("test/media/test.mov")

		upload_token = @client.upload_token_service.add()
		assert_not_nil upload_token.id
		upload_token = @client.upload_token_service.upload(upload_token.id, media_file)

		created_entry = @client.base_entry_service.add(base_entry)
		assert_not_nil created_entry.id
		
		resource = Kaltura::KalturaUploadedFileTokenResource.new
		resource.token = upload_token.id
		@client.media_service.add_content(created_entry.id, resource)

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
		base_entry.type = Kaltura::KalturaEntryType::MEDIA_CLIP
		base_entry.name = "kaltura_test"
		media_file = File.open("test/media/test.mov")

		upload_token = @client.upload_token_service.add()
		assert_not_nil upload_token.id
		upload_token = @client.upload_token_service.upload(upload_token.id, media_file)

		created_entry = @client.base_entry_service.add(base_entry)
		assert_not_nil created_entry.id
		
		resource = Kaltura::KalturaUploadedFileTokenResource.new
		resource.token = upload_token.id
		@client.media_service.add_content(created_entry.id, resource)

		img_file = File.open("test/media/test.png")
					
		updated_entry = @client.base_entry_service.update_thumbnail_jpeg(created_entry.id, img_file)
		
		assert_not_nil updated_entry.thumbnail_url		 
		assert_not_equal updated_entry.thumbnail_url, created_entry.thumbnail_url
		assert_nil @client.base_entry_service.delete(updated_entry.id)
	end
	
	# this test creates an entry and set it's moderation flags.
	should "set the moderation flags" do
	
		base_entry = Kaltura::KalturaBaseEntry.new
		base_entry.type = Kaltura::KalturaEntryType::MEDIA_CLIP
		base_entry.name = "kaltura_test"
		media_file = File.open("test/media/test.mov")
	
		upload_token = @client.upload_token_service.add()
		assert_not_nil upload_token.id
		upload_token = @client.upload_token_service.upload(upload_token.id, media_file)
	
		created_entry = @client.base_entry_service.add(base_entry)
		assert_not_nil created_entry.id
		
		resource = Kaltura::KalturaUploadedFileTokenResource.new
		resource.token = upload_token.id
		@client.media_service.add_content(created_entry.id, resource)
	
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

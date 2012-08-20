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

class ConfigurationTest < Test::Unit::TestCase
  
  # this test validates the session id 
  should "not have a nil client session" do
    assert_not_nil @client.ks
  end
  
  # this test generates a exception inside the client library code. 
  should "raise an error for invalid api calls" do
    
    assert_raise NoMethodError do
      base_entry = Kaltura::KalturaBaseEntry.new
      base_entry.type = Kaltura::KalturaEntryType::DOCUMENT
      base_entry.name = "kaltura_test"
      pdf_file = File.open("test/media/test.pdf")
      pdf_token = @client.invalid_service.upload(pdf_file)
    end
  end
  
  # this test invoke the api actions in https channel
  should "support HTTPS" do
    config = YAML.load_file("kaltura.yml")
        
    partner_id = config["test"]["partner_id"]
    service_url = config["test"]["service_url"]
    administrator_secret = config["test"]["administrator_secret"]
    timeout = config["test"]["timeout"]
    
    service_url.gsub(/http:/, "https:/")
    
    config = Kaltura::KalturaConfiguration.new(partner_id, service_url)
    config.logger = Logger.new(STDOUT)
    config.timeout = timeout
    
    @client = Kaltura::KalturaClient.new( config )
    assert_equal @client.ks, Kaltura::KalturaNotImplemented
    
    session = @client.session_service.start( administrator_secret, '', Kaltura::KalturaSessionType::ADMIN )
    @client.ks = session
    
    assert_not_nil @client.ks
  end
  
  # this test generates a exception inside the client library code. 
  should "raise an error for invalid service url" do
    
    config = YAML.load_file("kaltura.yml")
        
    partner_id = config["test"]["partner_id"]
    service_url = "http://invalid-service-url"
    administrator_secret = config["test"]["administrator_secret"]
    
    config = Kaltura::KalturaConfiguration.new(partner_id, service_url)
    config.logger = Logger.new(STDOUT)
    
    @client = Kaltura::KalturaClient.new( config )
    
    assert_raise Kaltura::KalturaAPIError do
      session = @client.session_service.start( administrator_secret, '', Kaltura::KalturaSessionType::ADMIN )
    end
  end
  
  # this test tries to retrieve a session key for a invalid configuration.
  should "not create a valid client session for invalid credentials" do
    
    config = YAML.load_file("kaltura.yml")
        
    partner_id = config["test"]["partner_id"]
    service_url = config["test"]["service_url"]
    
    config = Kaltura::KalturaConfiguration.new(partner_id, service_url)
    config.logger = Logger.new(STDOUT)
    
    @client = Kaltura::KalturaClient.new( config )
    
    assert_raise Kaltura::KalturaAPIError do
      session = @client.session_service.start( "invalid_administrator_secret", '', Kaltura::KalturaSessionType::ADMIN )
      @client.ks = session
    end
    
    assert_equal @client.ks, Kaltura::KalturaNotImplemented
  end
  
  # this test uses a session created in client side to comunicate with api.
  should "upload a file and create an entry using the session created in client side" do
      
      config = YAML.load_file("kaltura.yml")
  
      partner_id = config["test"]["partner_id"]
      service_url = config["test"]["service_url"]
      administrator_secret = config["test"]["administrator_secret"]
      timeout = config["test"]["timeout"]
  
      config = Kaltura::KalturaConfiguration.new(partner_id, service_url)
      config.logger = Logger.new(STDOUT)
      config.timeout = timeout
  
      @client = Kaltura::KalturaClient.new( config )
    
      assert_equal @client.ks, Kaltura::KalturaNotImplemented
    
      @client.generate_session(administrator_secret, '', Kaltura::KalturaSessionType::ADMIN, partner_id)
      
      assert_not_nil @client.ks
      
      base_entry = Kaltura::KalturaBaseEntry.new
      base_entry.type = Kaltura::KalturaEntryType::DOCUMENT
      base_entry.name = "kaltura_test"
      pdf_file = File.open("test/media/test.pdf")
      
      
      pdf_token = @client.base_entry_service.upload(pdf_file)
      created_entry = @client.base_entry_service.add_from_uploaded_file(base_entry, pdf_token)
      
      assert_not_nil created_entry.id
      assert_nil @client.base_entry_service.delete(created_entry.id)
   end
  
  # this test is to validate the timeout configuration setting.
  should "time out the request" do
    
    config = YAML.load_file("kaltura.yml")
        
    partner_id = config["test"]["partner_id"]
    service_url = config["test"]["service_url"]
    administrator_secret = config["test"]["administrator_secret"]
    timeout = 0
    
    config = Kaltura::KalturaConfiguration.new(partner_id, service_url)
    config.logger = Logger.new(STDOUT)
    config.timeout = timeout
    
    exception = assert_raise Kaltura::KalturaAPIError do
      @client = Kaltura::KalturaClient.new( config )
      session = @client.session_service.start( administrator_secret, '', Kaltura::KalturaSessionType::ADMIN )
      
      @client.ks = session
      
      media_entry = Kaltura::KalturaMediaEntry.new
      media_entry.name = "kaltura_test1"
      media_entry.media_type = Kaltura::KalturaMediaType::VIDEO
      video_file = File.open("test/media/test.wmv")
  
      video_token = @client.media_service.upload(video_file)
    end
    
    assert_equal exception.message, 'Request Timeout'
  end
end

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

class AccessControlServiceTest < Test::Unit::TestCase
  
  # this test create a access control object and reset the restrictions using and empty array.
  should "be able to send empty array to the api and reset the values" do
    
    # cleaning up the list
    access_control_filter = Kaltura::KalturaAccessControlFilter.new
    filter_pager = Kaltura::KalturaFilterPager.new
    access_control_list = @client.access_control_service.list(access_control_filter, filter_pager)
    access_control_list.objects.each do |obj|
      @client.access_control_service.delete(obj.id) rescue nil
    end  if access_control_list.objects
        
    access_control = Kaltura::KalturaAccessControl.new
    access_control.name = "kaltura_test"
    access_control.is_default = Kaltura::KalturaNullableBoolean::FALSE_VALUE
    
    access_control.restrictions = []
    
    restriction1 = Kaltura::KalturaCountryRestriction.new
    restriction1.country_restriction_type = Kaltura::KalturaCountryRestrictionType::RESTRICT_COUNTRY_LIST
    restriction1.country_list = 'UK,LK'
    access_control.restrictions << restriction1

    restriction2 = Kaltura::KalturaSiteRestriction.new
    restriction2.site_restriction_type = Kaltura::KalturaSiteRestrictionType::RESTRICT_SITE_LIST
    restriction2.site_list = 'http://www.kaltura.com'
    access_control.restrictions << restriction2
    
    created_access_control = @client.access_control_service.add(access_control)
    
    assert_not_nil created_access_control.id
    assert_equal created_access_control.restrictions.size, 2
    
    # edited access control
    edited_access_control = Kaltura::KalturaAccessControl.new
    edited_access_control.name = access_control.name  
    edited_access_control.restrictions = []
    
    updated_access_control = @client.access_control_service.update(created_access_control.id, edited_access_control)
    
    assert_equal updated_access_control.restrictions, nil
    assert_nil @client.access_control_service.delete(updated_access_control.id)
  end
end
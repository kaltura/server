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

	# this test create a access control object and reset the rules using and empty array.
	should "be able to send empty array to the api and reset the values" do

		# cleaning up the list

		country_uk = Kaltura::KalturaStringValue.new
		country_uk.value = 'UK'

		country_lk = Kaltura::KalturaStringValue.new
		country_lk.value = 'LK'
		
		country_condition = Kaltura::KalturaCountryCondition.new
		country_condition.values = []
		country_condition.values << country_uk
		country_condition.values << country_lk
		
		country_rule = Kaltura::KalturaRule.new
		country_rule.actions = []
		country_rule.actions << Kaltura::KalturaAccessControlBlockAction.new
		country_rule.conditions = []
		country_rule.conditions << country_condition

		site = Kaltura::KalturaStringValue.new
		site.value = 'http://www.kaltura.com'
		
		site_condition = Kaltura::KalturaSiteCondition.new
		site_condition.values = []
		site_condition.values << site
		
		site_rule = Kaltura::KalturaRule.new
		site_rule.actions = []
		site_rule.actions << Kaltura::KalturaAccessControlBlockAction.new
		site_rule.conditions = []
		site_rule.conditions << site_condition

		access_control = Kaltura::KalturaAccessControlProfile.new
		access_control.name = "kaltura_test"
		access_control.is_default = Kaltura::KalturaNullableBoolean::FALSE_VALUE

		access_control.rules = []
		access_control.rules << country_rule
		access_control.rules << site_rule
		
		created_access_control = @client.access_control_profile_service.add(access_control)

		assert_not_nil created_access_control.id
		assert_equal created_access_control.rules.size, 2

		# edited access control
		edited_access_control = Kaltura::KalturaAccessControlProfile.new
		edited_access_control.name = access_control.name
		edited_access_control.rules = []

		updated_access_control = @client.access_control_profile_service.update(created_access_control.id, edited_access_control)

		assert_equal updated_access_control.rules, nil
		assert_nil @client.access_control_profile_service.delete(updated_access_control.id)
	end
end
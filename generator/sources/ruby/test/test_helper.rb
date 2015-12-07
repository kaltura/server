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
require 'rubygems'
require 'test/unit'
require 'shoulda'
require 'yaml'
require 'logger'

require 'kaltura'

class Test::Unit::TestCase

  # read the kaltura config file
  # initiate a kaltura configuration object
  # initiate kaltura client object
  # get the sesion object and assigns it to the client
  def setup
    config_file = YAML.load_file("kaltura.yml")
        
    partner_id = config_file["test"]["partner_id"]
    service_url = config_file["test"]["service_url"]
    administrator_secret = config_file["test"]["administrator_secret"]
    timeout = config_file["test"]["timeout"]
    
    config = Kaltura::KalturaConfiguration.new(partner_id, service_url)
    config.logger = Logger.new(STDOUT)
    config.timeout = timeout
    
    @client = Kaltura::KalturaClient.new( config )
    session = @client.session_service.start( administrator_secret, '', Kaltura::KalturaSessionType::ADMIN )
    @client.ks = session
  end

end

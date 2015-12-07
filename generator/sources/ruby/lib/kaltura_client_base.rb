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
require 'json'
require 'net/http'
require 'digest/md5'
require 'rexml/document'
require 'rest-client'
require 'openssl'
require 'digest/sha1'
require 'base64'
require 'date'
require 'yaml'

module Kaltura
	class KalturaNotImplemented; end

	class KalturaClientBase
		attr_accessor 	:config
		attr_reader 	:is_multirequest
		attr_reader 	:responseHeaders
		def initialize(config)
			@should_log = false
			@config = config
			@calls_queue = []
			@client_configuration = {}
			@request_configuration = {}

			if @config.logger != nil
				@should_log = true
			end
		end

		def queue_service_action_call(service, action, return_type, params = {}, files = {})
			# in start session partner id is optional (default nil). if partner id was not set, use the one in the config

			@request_configuration.each do |key, value|
				add_param(params, key, value)
			end

			call = KalturaServiceActionCall.new(service, action, return_type, params, files);
			@calls_queue.push(call);
		end

		def do_queue()
			begin
				@responseHeaders = {}
				start_time = Time.now

				if @calls_queue.length == 0
					@is_multirequest = false
					return []
				end

				log('service url: [' + @config.service_url + ']')

				# append the basic params
				params = {}
				files = {}
					
				url = @config.service_url+"/api_v3/"
				if (@is_multirequest)
					url += "service/multirequest/"
					i = 0
					@calls_queue.each do |call|
						call_params = call.get_params_for_multirequest(i)
						params.merge!(call_params)
						call_files = call.get_files_for_multirequest(i)
						files.merge!(call_files)
						i = i.next
					end
				else
					call = @calls_queue[0]
					url += "service/#{call.service}/action/#{call.action}/"
					params.merge!(call.params)
					files = call.files
				end
				
				add_param(params, "format", @config.format)
				@client_configuration.each do |key, value|
					add_param(params, key, value)
				end

				signature = signature(params)
				add_param(params, "kalsig", signature)

				log("url: " + url)
				log("params: " + params.to_yaml)

				result = do_http_request(url, params, files)

				@responseHeaders = result.headers
				log("server: [" + result.headers[:x_me].to_s + "], session: [" + result.headers[:x_kaltura_session].to_s + "]")

				log("result (xml): " + result.body)

				result_object = parse_to_objects(result.body)

				# reset
				reset_request()

				log("result (object yaml dump): " + result_object.to_yaml)

				end_time = Time.now

				log("execution time for [#{url}]: [#{end_time - start_time}]")

				return result_object

			rescue KalturaAPIError => e
				raise e
			rescue Exception => e
				raise KalturaAPIError.new("KALTURA_RUBY_CLIENT_ERROR", e.to_s)
			end
		end

		def reset_request()
			@calls_queue = []
			@is_multirequest = false
		end

		def get_serve_url()
			url = @config.service_url+"/api_v3/service/"

			call = @calls_queue[0]
			url += call.service + "/action/" + call.action
			params = call.params

			# reset
			@calls_queue = []
			@is_multirequest = false

			query_string = '?'
			params.each do |name, value|
				query_string << "#{name}=#{CGI::escape(value.to_s)}&"
			end

			serve_url = "#{url}#{query_string}"

			log("serve_url: " + serve_url)

			return serve_url
		end

		def do_http_request(url, params, files)

			headers = @config.requestHeaders
			headers[ 'Accept'] = 'text/xml';
				
			payload = {}
				
			if(files.size > 0)
				payload = files
				payload[:json] = params.to_json
			else
				payload = params.to_json
				headers[ 'Content-Type'] = 'application/json';
			end
			
			options = {
				:method => :post, 
				:url => url, 
				:headers => headers,
				:timeout => @config.timeout,
				:open_timeout => @config.timeout,
				:payload => payload
			}

			log("request options: " + JSON.pretty_generate(options))
			res = RestClient::Request.execute(options)

			return res
		end

		def self.object_from_xml(xml_element, return_type = nil)
			instance = nil
			if xml_element.elements.size > 0
				if xml_element.elements[1].name == 'item' # array or map
					if (xml_element.elements[1].elements['itemKey'].nil?) # array
						instance = []
						xml_element.elements.each('item') do | element |
							instance.push(KalturaClientBase.object_from_xml(element, return_type))
						end
					else # map
						instance = {}
						xml_element.elements.each('item') do | element |
							item_key = element.get_text('itemKey').to_s
							instance[item_key] = KalturaClientBase.object_from_xml(element, return_type)
						end
					end
				else # object
					object_type_element = xml_element.get_text('objectType')
					if (object_type_element != nil)
						object_class = xml_element.get_text('objectType').value.to_s
						kalturaModule = Module.const_get("Kaltura")
						
						begin
							instance = kalturaModule.const_get(object_class).new
						rescue NameError => e
							if(return_type != nil)
								instance = kalturaModule.const_get(return_type).new
							else
								raise e
							end
						end

						instance.from_xml(xml_element);
					else # error
						error_element = xml_element.elements['error']
						if (error_element != nil)
							code = xml_element.elements["error/code"].text
							message = xml_element.elements["error/message"].text

							instance = KalturaAPIError.new(code, message)
						end
					end
				end
			elsif return_type == nil
				return nil 
			else # simple type
				value = xml_element.text
				if return_type == "int"
					return value.to_i
				end
				
				return value
			end

			return instance;
		end

		def self.camelcase_to_underscore(val)
			val.gsub(/(.)([A-Z])/,'\1_\2').downcase
		end

		def parse_to_objects(data)
			parse_xml_to_objects(data)
		end

		def parse_xml_to_objects(xml)
			doc = REXML::Document.new(xml)
			raise_exception_if_error(doc)
			if (@is_multirequest)
				results = {}
				request_index = 0
				doc.elements.each('xml/result/*') do | element |
					results[request_index] = KalturaClientBase.object_from_xml(element, @calls_queue[request_index].return_type)
					request_index += 1
				end
				return results
			else
				doc.elements.each('xml/result') do | element |
					return KalturaClientBase.object_from_xml(element, @calls_queue.first.return_type)
				end
			end
		end

		def raise_exception_if_error(doc)
			if is_error(doc)
				code = doc.elements["xml/result/error/code"].text
				message = doc.elements["xml/result/error/message"].text
				raise KalturaAPIError.new(code, message)
			end
		end

		def is_error(doc)
			return doc.elements["xml/result/error/message"] && doc.elements["xml/result/error/code"];
		end

		def start_multirequest()
			@is_multirequest = true
		end

		def do_multirequest()
			return do_queue()
		end

		def signature(params)
			kParams = params.select { |key, value| !value.is_a?(File) }
			str = kParams.keys.map {|key| key.to_s }.sort.map {|key|
				"#{key}#{params[key]}"
			}.join("")

			Digest::MD5.hexdigest(str)
		end

		def add_param(params, name, value)
			if value == KalturaNotImplemented
				return
			elsif value == nil
				params[name + '__null'] = ''
			elsif value.is_a? Hash
				params[name] = {}
				if value.empty?
					add_param(params[name], "-", "");
				else
					value.each do |sub_name, sub_value|
						add_param(params[name], sub_name, sub_value);
					end
				end
			elsif value.is_a? Array
				if value.empty?
					params[name] = {}
					add_param(params[name], "-", "");
				else
					params[name] = Array.new(value.size)
					value.each_with_index do |ele, i|
						if ele.is_a? KalturaObjectBase
							add_param(params[name], i, ele.to_params)
						end
					end
				end
			elsif value.is_a? KalturaObjectBase
				add_param(params, name, value.to_params)
			else
				params[name] = value
			end
		end

		# Escapes a query parameter. Taken from RFuzz
		def escape(s)
			s.to_s.gsub(/([^ a-zA-Z0-9_.-]+)/n) {
				'%' + $1.unpack('H2'*$1.size).join('%').upcase
			}.tr(' ', '+')
		end

		def log(msg)
			if @should_log
				config.logger.log(Logger::INFO, msg)
			end
		end

		def generate_session(admin_secret, user_id, kaltura_session_type, partner_id, expiry=86400, privileges=nil)

			session = "#{partner_id};#{partner_id};#{Time.now.to_i + expiry};#{kaltura_session_type};#{rand.to_s.gsub("0.", "")};#{user_id};#{privileges};"

			digest_generator = OpenSSL::Digest.new('sha1')

			digest_generator.update(admin_secret)
			digest_generator.update(session)

			digest = digest_generator.hexdigest

			signature = digest + "|" + session
			b64 = Base64.encode64(signature)
			cleaned = b64.gsub("\n","")

			self.ks = cleaned
		end
	end

	class KalturaServiceActionCall
		attr_accessor :service
		attr_accessor :action
		attr_accessor :return_type
		attr_accessor :params
		attr_accessor :files
		
		def initialize(service, action, return_type, params, files)
			@service = service
			@action = action
			@return_type = return_type
			@params = parse_params(params)
			@files = files
		end

		def parse_params(params)
			new_params = {}
			params.each do |key, val|
				if val.kind_of? Hash
					new_params[key] = parse_params(val)
				else
					new_params[key] = val
				end
			end
			return new_params
		end

		def get_params_for_multirequest(multirequest_index)
			multirequest_params = {}
			multirequest_params[multirequest_index.to_s] = {}
			multirequest_params[multirequest_index.to_s]["service"] = @service
			multirequest_params[multirequest_index.to_s]["action"] = @action
			@params.each do |key, value|
				multirequest_params[multirequest_index.to_s][key] = value
			end
			return multirequest_params
		end
		
		def get_files_for_multirequest(multirequest_index)
			multirequest_params = {}
			@files.each do |key, value|
				multirequest_params[multirequest_index.to_s + ":" + key] = value
			end
			return multirequest_params
		end
	end

	class KalturaObjectBase
		attr_accessor :object_type
		attr_accessor :related_objects

		def from_xml(xml_element)
			self.related_objects = KalturaClientBase.object_from_xml(xml_element.elements['relatedObjects'], 'KalturaListResponse')
		end
		
		def to_params
			params = {};
			params["objectType"] = self.class.name.split('::').last
			instance_variables.each do |var|
				value = instance_variable_get(var)
				var = var.to_s.sub('@', '')
				kvar = camelcase(var)
				if (value != nil)
					if (value.is_a? KalturaObjectBase)
						params[kvar] = value.to_params;
					else
						params[kvar] = value;
					end
				else
					params[kvar] = value;
				end
			end
			return params;
		end

		def to_b(val)
			return [true, 'true', 1, '1'].include?(val.is_a?(String) ? val.downcase : val)
		end

		def camelcase(val)
			val = val.split('_').map { |e| e.capitalize }.join()
			val[0,1].downcase + val[1,val.length]
		end
	end

	class KalturaServiceBase
		attr_accessor :client
		def initialize(client)
			@client = client
		end
	end

	class KalturaConfiguration
		attr_accessor :logger
		attr_accessor :service_url
		attr_accessor :format
		attr_accessor :timeout
		attr_accessor :requestHeaders
		#
		# Adding service_url to the initialize signature to pass url to your own kaltura ce instance
		# Default is still set to http://www.kaltura.com.
		#
		def initialize()
			@service_url 	= "http://www.kaltura.com"
			@format 		= 2 # xml
			@timeout 		= 120
			@requestHeaders = {}
		end

		def service_url=(url)
			@service_url = url.chomp('/')
		end
	end

	class KalturaAPIError < RuntimeError
		attr_reader :code
		attr_reader :message
		def initialize(code, message)
			@code = code
			@message = message
		end
	end
end
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
require 'rest_client'

require 'openssl'
require 'digest/sha1'
require 'base64'
require 'date'
require 'yaml'

module Kaltura
  class KalturaNotImplemented; end
  
	class KalturaClientBase
		attr_accessor 	:config
		attr_accessor 	:ks
		attr_reader 	:is_multirequest
	
		def initialize(config)
		  @ks = KalturaNotImplemented
			@should_log = false
			@config = config
			@calls_queue = []
	
			if @config.logger != nil
				@should_log = true
			end
		end
		
		def queue_service_action_call(service, action, params = {})
			# in start session partner id is optional (default nil). if partner id was not set, use the one in the config
			if !params.key?('partnerId')
				params['partnerId'] = config.partner_id
				params.delete('partnerId__null') if params.key?('partnerId__null')
			end	
			
			add_param(params, 'ks', @ks);
			
			call = KalturaServiceActionCall.new(service, action, params);
			@calls_queue.push(call);
		end
	
		def do_queue()
		  begin 
  			start_time = Time.now
			
  			if @calls_queue.length == 0
  				@is_multirequest = false
  				return []
  			end
					
  			log('service url: [' + @config.service_url + ']')
			
  			# append the basic params
  			params = {}
  			add_param(params, "format", @config.format)
  			add_param(params, "clientTag", @config.client_tag)
			
  			url = @config.service_url+"/api_v3/index.php?service="
  			if (@is_multirequest)
  				url += "multirequest"
  				i = 1
  				@calls_queue.each do |call|
  					call_params = call.get_params_for_multirequest(i)
  					params.merge!(call_params)
  					i = i.next
  				end
  			else
  				call = @calls_queue[0]
  				url += call.service + "&action=" + call.action
  				params.merge!(call.params)
  			end
			
  			# reset
  			@calls_queue = []
  			@is_multirequest = false
			
  			signature = signature(params)
  			add_param(params, "kalsig", signature)
			
  			log("url: " + url)
  			log("params: " + params.to_yaml)
								
  			result = do_http_request(url, params)
			
  			log("result (xml): " + result.body)
  			
  			result_object = parse_to_objects(result.body)
			
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

    def get_serve_url()
			url = @config.service_url+"/api_v3/index.php?service="

			call = @calls_queue[0]
			url += call.service + "&action=" + call.action
			params = call.params
			
			# reset
			@calls_queue = []
			@is_multirequest = false
			
			query_string = ''
      params.each do |name, value|
        query_string << "&#{name}=#{CGI::escape(value.to_s)}"
      end
    
      serve_url = "#{url}#{query_string}"
      
      log("serve_url: " + serve_url)
      
      return serve_url      
    end
		
    def do_http_request(url, params)
      
      options = {:method => :post, :url => url, :payload => params}
      
      options.merge!(:timeout => @config.timeout) if @config.timeout
      options.merge!(:open_timeout => @config.timeout) if @config.timeout
            
      res = RestClient::Request.execute(options)
      
      return res
    end
		
		def parse_to_objects(data)
			parse_xml_to_objects(data)
		end
		
		def parse_xml_to_objects(xml)
			doc = REXML::Document.new(xml)
			raise_exception_if_error(doc)
			doc.elements.each('xml/result') do | element |
				return KalturaClassFactory.object_from_xml(element)
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
			str = params.keys.map {|key| key.to_s }.sort.map {|key|
				"#{escape(key)}#{escape(params[key])}"
			}.join("")
			
			Digest::MD5.hexdigest(str)
		end
		
		def add_param(params, name, value)
		  if value == KalturaNotImplemented
		    return
			elsif value == nil
				params[name + '__null'] = ''
			elsif value.is_a? Hash
				if value.empty?
					add_param(params, "#{name}:-", "");
				else
					value.each do |sub_name, sub_value|
						add_param(params, "#{name}:#{sub_name}", sub_value);
					end
				end
      elsif value.is_a? Array
        if value.empty?
          add_param(params, "#{name}:-", "");
        else
          value.each_with_index do |ele, i|
            if ele.is_a? KalturaObjectBase
              add_param(params, "#{name}:#{i}", ele.to_params)
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
    
        ks = "#{partner_id};#{partner_id};#{Time.now.to_i + expiry};#{kaltura_session_type};#{rand.to_s.gsub("0.", "")};#{user_id};#{privileges};"

        digest_generator = OpenSSL::Digest::Digest.new('sha1')
        
        digest_generator.update(admin_secret)  
        digest_generator.update(ks) 
        
        digest = digest_generator.hexdigest 
                                    
        signature = digest + "|" + ks                             
        b64 = Base64.encode64(signature)
        cleaned = b64.gsub("\n","")
        
        @ks = cleaned
    end
	end
	
	class KalturaServiceActionCall
		attr_accessor :service
		attr_accessor :action
		attr_accessor :params
	
		def initialize(service, action, params = array())
			@service = service
			@action = action
			@params = parse_params(params)
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
			multirequest_params[multirequest_index.to_s+":service"] = @service
			multirequest_params[multirequest_index.to_s+":action"] = @action
			@params.each_key do |key|
				multirequest_params[multirequest_index.to_s+":"+key] = @params[key]
			end
			return multirequest_params
		end
	end

	class KalturaObjectBase
		attr_accessor :object_type
		
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
		attr_accessor :client_tag
		attr_accessor :timeout
		attr_accessor :partner_id
	
    #
    # Adding service_url to the initialize signature to pass url to your own kaltura ce instance
    # Default is still set to http://www.kaltura.com.
    # 
	
		def initialize(partner_id = -1,service_url="http://www.kaltura.com")
			@service_url 	= service_url
			@format 		= 2 # xml
			@client_tag 	= "ruby"
			@timeout 		= 10
			@partner_id 	= partner_id
		end
		
		def service_url=(url)
			@service_url = url.chomp('/')
		end
	end

	class KalturaClassFactory
    def self.object_from_xml(xml_element)
      instance = nil
      if xml_element.elements.size > 0
        if xml_element.elements[1].name == 'item' # array 
          instance = []
          xml_element.elements.each('item') do | element |
            instance.push(KalturaClassFactory.object_from_xml(element))
          end
        else # object
          object_type_element = xml_element.get_text('objectType')
          if (object_type_element != nil)
            object_class = xml_element.get_text('objectType').value.to_s
            instance = Module.const_get("Kaltura")
            instance = instance.const_get(object_class).new
      
            xml_element.elements.each do | element |
              value = KalturaClassFactory.object_from_xml(element)
              instance.send(self.underscore(element.name) + "=", value) if instance.class.method_defined?(self.underscore(element.name));
            end
          else # error
            error_element = xml_element.elements['error']
            if (error_element != nil)
      				code = xml_element.elements["error/code"].text
      				message = xml_element.elements["error/message"].text
      				
      				instance = KalturaAPIError.new(code, message)              
            end
          end
        end
      else # simple type
        return xml_element.text
      end
      
      return instance;
    end
    
    def self.underscore(val)
      val.gsub(/(.)([A-Z])/,'\1_\2').downcase
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
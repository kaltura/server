require 'rubygems'
require 'json'
require 'net/http'
require 'digest/md5'
require 'rexml/document'

module Kaltura
	class KalturaClientBase
		attr_accessor 	:config
		attr_accessor 	:ks
		attr_reader 	:is_multirequest
	
		def initialize(config)
			@should_log = false
			@config = config
			@calls_queue = []
	
			if @config.logger != nil
				@should_log = true
			end
		end
		
		def queue_service_action_call(service, action, params = {})
			# in start session partner id is optional (default -1). if partner id was not set, use the one in the config
			if (!params.key?('partnerId') || params['partnerId'] == -1)
				params['partnerId'] = config.partner_id;
			end	
			
			add_param(params, 'ks', @ks);
			
			call = KalturaServiceActionCall.new(service, action, params);
			@calls_queue.push(call);
		end
	
		def do_queue()
			start_time = Time.now
			
			if @calls_queue.length == 0
				@is_multirequest = false
				return nil
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
				@calls_queue.each_value do |call|
					call_params = call.get_params_for_multirequest(i.next)
					params.merge!(call_params)
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
			
			result_object = parse_to_objects(result.body)

			log("result (object yaml dump): " + result_object.to_yaml)
			
			end_time = Time.now
			log("execution time for [#{url}]: [#{end_time - start_time}]")
			
			return result_object			
		end
		
		def do_http_request(url, params)
			url = URI.parse(url)
			req = Net::HTTP::Post.new(url.path + '?' + url.query)
			req.set_form_data(params)
			res = Net::HTTP.new(url.host, url.port).start { |http| http.request(req) }
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
			if value == nil
				return
			elsif value.is_a? Hash
				value.each do |sub_name, sub_value|
					add_param(params, "#{name}:#{sub_name}", sub_value);
				end
			elsif value.is_a? KalturaObjectBase
				add_param(params, name, value.to_params)
			else
				params[name] = value
			end
		end
	
		# Escapes a query parameter. Stolen from RFuzz
		def escape(s)
			s.to_s.gsub(/([^ a-zA-Z0-9_.-]+)/n) {
				'%' + $1.unpack('H2'*$1.size).join('%').upcase
			}.tr(' ', '+')
		end
		
		def log(msg)
			if @should_log
				config.logger.log(msg)
			end
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
			multirequest_params[multirequest_index+":service"] = @service
			multirequest_params[multirequest_index+":action"] = @action
			@params.each do |key|
				multirequest_params[multirequest_index+":"+key] = @params[key]
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
				var = var.sub('@', '')
				kvar = camelcase(var)
				if (value != nil)
					if (value.is_a? KalturaObjectBase)
						params[kvar] = value.to_params;
					else
						params[kvar] = value;
					end
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
	
		def initialize(partner_id = -1)
			@service_url 	= "http://www.kaltura.com"
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
						object_class = xml_element.get_text('objectType').value
						instance = Object.const_get(object_class).new
						xml_element.elements.each do | element |
							value = KalturaClassFactory.object_from_xml(element)
							instance.send(self.underscore(element.name) + "=", value);
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
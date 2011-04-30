package com.kaltura.delegates {
	
	import com.kaltura.config.IKalturaConfig;
	import com.kaltura.config.KalturaConfig;
	import com.kaltura.core.KClassFactory;
	import com.kaltura.errors.KalturaError;
	import com.kaltura.events.KalturaEvent;
	import com.kaltura.net.KalturaCall;
	
	import flash.events.ErrorEvent;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.events.HTTPStatusEvent;
	import flash.events.IOErrorEvent;
	import flash.events.SecurityErrorEvent;
	import flash.events.TimerEvent;
	import flash.net.FileReference;
	import flash.net.URLLoader;
	import flash.net.URLLoaderDataFormat;
	import flash.net.URLRequest;
	import flash.net.URLRequestMethod;
	import flash.utils.Timer;
	import flash.utils.getDefinitionByName;
	
	public class WebDelegateBase extends EventDispatcher implements IKalturaCallDelegate {
		
		public static var CONNECT_TIME : int = 60000; //60 secs
		public static var LOAD_TIME : int = 60000; //60 secs
		
		protected var connectTimer:Timer;
		protected var loadTimer:Timer;
		
		protected var _call:KalturaCall;
		protected var _config:KalturaConfig;
		
		protected var loader:URLLoader;
		protected var fileRef:FileReference;
		
		//Setters & getters 
		public function get call():KalturaCall { return _call; }
		public function set call(newVal:KalturaCall):void { _call = newVal; }

		public function get config():IKalturaConfig { return _config; }
		public function set config(newVal:IKalturaConfig):void { _config = newVal as KalturaConfig; }
		
		public function WebDelegateBase(call:KalturaCall = null , config:KalturaConfig = null) 
		{
			this.call = call;
			this.config = config;
			if(!call) return; //maybe a multi request
			connectTimer = new Timer(CONNECT_TIME, 1);
			connectTimer.addEventListener(TimerEvent.TIMER_COMPLETE, onConnectTimeout);
			
			loadTimer = new Timer(LOAD_TIME, 1);
			loadTimer.addEventListener(TimerEvent.TIMER_COMPLETE, onLoadTimeOut);
			
			execute();
		}
		
		public function close():void {
			try {
				loader.close();
			} catch (e:*) { }
			
			connectTimer.stop();
			loadTimer.stop();
		}
		
		protected function onConnectTimeout(event:TimerEvent):void {
			var kError:KalturaError = new KalturaError();
			//kError.errorCode =
			kError.errorMsg = "Connection Timeout: " + CONNECT_TIME/1000 + " sec with no post command from kaltura client.";
			_call.handleError(kError);
			dispatchEvent(new KalturaEvent(KalturaEvent.FAILED, false, false, false, null, kError));
			
			loadTimer.stop();
			close();
		}
		
		protected function onLoadTimeOut(event:TimerEvent):void {
			connectTimer.stop();
			
			close();
			
			var kError:KalturaError = new KalturaError();
			kError.errorMsg = "Post Timeout: "+ LOAD_TIME/1000 + " sec with no post result.";
			_call.handleError(kError);
			dispatchEvent(new KalturaEvent(KalturaEvent.FAILED, false, false, false, null, kError));
		}

		protected function execute():void {
			if (call == null) { throw new Error('No call defined.'); }
			post(); //post the call
		}

		/**
		 * Helper function for sending the call straight to the server
		 */
		protected function post():void {
			
			addOptionalArguments();
			
			formatRequest();
			
			sendRequest();
			
			connectTimer.start();
		}
		
		protected function formatRequest():void 
		{
			//The configuration is stronger then the args
			if(_config.partnerId != null && _call.args["partnerId"] == -1)
				_call.setRequestArgument("partnerId", _config.partnerId); 
				
			if (_config.ks != null)
				_call.setRequestArgument("ks", _config.ks);

			if(_config.clientTag != null)
				_call.setRequestArgument("clientTag", _config.clientTag);
			
			_call.setRequestArgument("ignoreNull", _config.ignoreNull);
			
			//Create signature hash.
			//call.setRequestArgument("kalsig", getMD5Checksum(call));
		}
		
		protected function sendRequest():void {
			//construct the loader
			createURLLoader();
			
			//create the service request for normal calls
			var url : String = _config.protocol + _config.domain +"/"+_config.srvUrl+"?service="+call.service+"&action="+call.action;
			
			if( _call.method == URLRequestMethod.GET )url += "&";
			
			var req:URLRequest = new URLRequest( url );
			req.contentType = "application/x-www-form-urlencoded";
			req.method = call.method; 
			req.data = call.args; 

			loader.dataFormat = URLLoaderDataFormat.TEXT;
			loader.load(req);
		}
		
		protected function createURLLoader():void {
			loader = new URLLoader();
			loader.addEventListener(Event.COMPLETE, onDataComplete);
			loader.addEventListener(HTTPStatusEvent.HTTP_STATUS, onHTTPStatus);
			loader.addEventListener(IOErrorEvent.IO_ERROR, onError);
			loader.addEventListener(SecurityErrorEvent.SECURITY_ERROR, onError);
			loader.addEventListener(Event.OPEN, onOpen);
		}
		
		protected function onHTTPStatus(event:HTTPStatusEvent):void { }
		
		protected function onOpen(event:Event):void {
			connectTimer.stop();
			loadTimer.start();
		}
		
		protected function addOptionalArguments():void {
			//add optional args here
		}
		
		// Event Handlers
		protected function onDataComplete(event:Event):void {
			try{	
				handleResult( XML(event.target.data) );
			}
			catch( e:Error )
			{
				var kErr : KalturaError = new KalturaError();
				kErr.errorCode = String(e.errorID);
				kErr.errorMsg = e.message;
				_call.handleError( kErr );
			} 
		}
		
		protected function onError( event:ErrorEvent ):void {
			clean();
			var kError:KalturaError = createKalturaError( event, loader.data);
			
			if(!kError)
			{
				kError.errorMsg = event.text;
				//kError.errorCode;
			}
				
			call.handleError(kError);
			
			dispatchEvent(new KalturaEvent(KalturaEvent.FAILED, false, false, false, null, kError));
		}
		
		/**
		 * parse the server's response and let the call process it. 
		 * @param result	server's response
		 */		
		protected function handleResult(result:XML):void {
			clean();
			
			var error:KalturaError = validateKalturaResponse(result);
			
			if (error == null) {
				var digestedResult : Object = parse(result);
				call.handleResult( digestedResult );
			} else {
				call.handleError(error);
			}
		}
		
		/**
		 * stop timers and clean event listeners 
		 */		
		protected function clean():void {
			connectTimer.stop();
			loadTimer.stop();
			
			if (loader == null) { return; }
			
			loader.removeEventListener(Event.COMPLETE, onDataComplete);
			loader.removeEventListener(IOErrorEvent.IO_ERROR, onError);
			loader.removeEventListener(SecurityErrorEvent.SECURITY_ERROR, onError);
			loader.removeEventListener(Event.OPEN, onOpen);
		}
		
		/**
		 * create the correct object and populate it with the given values. if the needed class is not found 
		 * in the file, a generic object is created with attributes matching the XML attributes.
		 * Override this parssing function in the specific delegate to create the correct object.
		 * @param	result	instance attributes
		 * @return an instance of the class declared by the given XML.
		 * */ 
		public function parse( result : XML ) : * 
		{ 
			//by defualt create the response object
			var cls : Class;
			try
			{
				cls = getDefinitionByName('com.kaltura.vo.'+ result.result.objectType) as Class;
			}
			catch( e : Error )
			{
				cls = Object;
			}
			var obj : * = (new KClassFactory( cls )).newInstanceFromXML( result.result );
			return obj;
		}
		
		/**
		 * If the result string holds an error, return a KalturaError object with 
		 * relevant values. <br/>
		 * Overide this to create validation object and fill it.
		 * @param result	the string returned from the server.
		 * @return	matching error object 
		 */
		protected function validateKalturaResponse(result:String) : KalturaError 
		{ 
			var kError : KalturaError = null;
			var xml : XML = XML(result);
			if(xml.result.hasOwnProperty('error')){
				kError = new KalturaError();
				kError.errorCode = String(xml.result.error.code);
				kError.errorMsg = xml.result.error.message;
				dispatchEvent(new KalturaEvent(KalturaEvent.FAILED, false, false, false, null, kError));	
			}
			
			return kError;
		}
		
		//Overide this to create error object and fill it
		protected function createKalturaError( event : ErrorEvent , loaderData : * ) : KalturaError 
		{ 
			var ke : KalturaError = new KalturaError();
			return ke; 
		}
	}
}
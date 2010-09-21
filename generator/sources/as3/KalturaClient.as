package com.kaltura
{
	import com.kaltura.config.KalturaConfig;
	import com.kaltura.net.KalturaCall;
	
	import flash.events.EventDispatcher;

	public class KalturaClient extends EventDispatcher
	{	
		protected var _currentConfig:KalturaConfig;
		
		public function KalturaClient( config : KalturaConfig) 
		{
			_currentConfig = config;
		}
		
		//Setters & Getters
		/**
		 * @copy KalturaConfig#partnerId
		 */	
		public function get partnerId():String  { return _currentConfig ? this._currentConfig.partnerId : null; }
		
		/**
		 * @copy KalturaConfig#domain
		 */	
		public function get domain():String { return _currentConfig ? this._currentConfig.domain : null; }
		
		/**
		 * @copy KalturaConfig#ks
		 */
		public function set ks( currentConfig : String ):void  {  _currentConfig.ks = currentConfig; }
		[Bindable]public function get ks():String  { return _currentConfig ? this._currentConfig.ks : null; }
		
		/**
		 * @copy KalturaConfig#protocol
		 */		
		public function set protocol(value:String):void { _currentConfig.protocol = value; }
		public function get protocol():String { return _currentConfig.protocol; }
		
		/**
		 * @copy KalturaConfig#clientTag
		 */
		public function set clientTag(value:String):void { _currentConfig.clientTag = value; }
		public function get clientTag():String { return _currentConfig.clientTag; }
		
		
		public function post(call:KalturaCall):KalturaCall {
			if (_currentConfig) {
				call.config = _currentConfig;
				call.initialize();
				call.execute();
			} else {
				throw new Error("Cannot post a call; no kaltura config has been set.");
			}
			return call;
		}
	}
}
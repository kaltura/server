package com.kaltura.delegates {
	
	import com.kaltura.config.IKalturaConfig;
	import com.kaltura.net.KalturaCall;
	
	import flash.events.IEventDispatcher;
	
	public interface IKalturaCallDelegate extends IEventDispatcher {
		
		function close():void;
		
		function get call():KalturaCall;
		function set call(newVal:KalturaCall):void;
		
		function get config():IKalturaConfig;
		function set config(newVal:IKalturaConfig):void;
	}
	
}
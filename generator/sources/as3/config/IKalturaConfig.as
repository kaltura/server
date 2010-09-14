package com.kaltura.config {
	
	import flash.events.IEventDispatcher;
	
	public interface IKalturaConfig extends IEventDispatcher 
	{	
		function get partnerId():String; 
		function set partnerId(value:String):void;

		function get srvUrl():String;
		function set srvUrl(value:String):void;
		
		function get domain():String;
		function set domain(value:String):void;
	
		function get ks():String;
		function set ks(value:String):void;

	    function get ignoreNull():int;
	    function set ignoreNull(value:int):void;
	}
}
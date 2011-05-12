package com.kaltura.net {

	import com.kaltura.config.KalturaConfig;
	import com.kaltura.errors.KalturaError;
	import com.kaltura.events.KalturaEvent;

	import flash.net.URLVariables;
	import flash.utils.ByteArray;

	public class KalturaFileCall extends KalturaCall {

		public var bytes : ByteArray;
		public var filterFields : String;
		
		public function KalturaFileCall () {
			super ();
		}
	}
}
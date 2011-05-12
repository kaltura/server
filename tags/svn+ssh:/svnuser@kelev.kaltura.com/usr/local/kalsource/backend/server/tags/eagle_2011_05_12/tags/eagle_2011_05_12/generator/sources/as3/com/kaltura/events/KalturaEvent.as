package com.kaltura.events {
	
	import com.kaltura.errors.KalturaError;
	
	import flash.events.Event;

	public class KalturaEvent extends Event {
		
		public static const COMPLETE:String = 'complete';
		public static const FAILED:String = 'failed';

		public var success:Boolean;
		public var data:Object;
		public var error:KalturaError;
		
		public function KalturaEvent(type:String,
									 bubbles:Boolean=false,
									 cancelable:Boolean=false,
									 success:Boolean = false,
									 data:Object = null, 
									 error:KalturaError = null) 
		{
			this.success = success;
			this.data = data;
			this.error = error;
			
			super(type, bubbles, cancelable);
		}
		
		override public function clone():Event {
			return new KalturaEvent(type, bubbles, cancelable, success, data, error);
		}
		
		override public function toString():String {
			return formatToString('KalturaEvent', 'type', 'success', 'data', 'error');
		}
		
	}
}
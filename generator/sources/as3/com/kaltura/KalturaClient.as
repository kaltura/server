package com.kaltura {
	import com.kaltura.commands.QueuedRequest;
	import com.kaltura.config.KalturaConfig;
	import com.kaltura.net.KalturaCall;
	
	import flash.events.Event;
	import flash.events.EventDispatcher;

	public class KalturaClient extends EventDispatcher {
		
		[Event(name="callQueued", type="Event")]
		
		[Event(name="queueFlushed", type="Event")]
		
		public static const CALL_QUEUED:String = "callQueued"; 
		public static const QUEUE_FLUSHED:String = "queueFlushed"; 
		
		
		protected var _currentConfig:KalturaConfig;

		/**
		 * @copy #queueing
		 */
		protected var _queueing:Boolean = false;
		
		/**
		 * the object used to queue requests 
		 */		
		protected var _queue:QueuedRequest;


		public function KalturaClient(config:KalturaConfig) {
			_currentConfig = config;
		}


		//Setters & Getters
		/**
		 * @copy KalturaConfig#partnerId
		 */
		public function get partnerId():String {
			return _currentConfig ? this._currentConfig.partnerId : null;
		}


		/**
		 * @copy KalturaConfig#domain
		 */
		public function get domain():String {
			return _currentConfig ? this._currentConfig.domain : null;
		}


		/**
		 * @copy KalturaConfig#ks
		 */
		public function set ks(currentConfig:String):void {
			_currentConfig.ks = currentConfig;
		}


		[Bindable]
		public function get ks():String {
			return _currentConfig ? this._currentConfig.ks : null;
		}


		/**
		 * @copy KalturaConfig#protocol
		 */
		public function set protocol(value:String):void {
			_currentConfig.protocol = value;
		}


		public function get protocol():String {
			return _currentConfig.protocol;
		}


		/**
		 * @copy KalturaConfig#clientTag
		 */
		public function set clientTag(value:String):void {
			_currentConfig.clientTag = value;
		}


		public function get clientTag():String {
			return _currentConfig.clientTag;
		}


		public function post(call:KalturaCall):KalturaCall {
			if (_currentConfig) {
				call.config = _currentConfig;
				call.initialize();
				if (_queueing && call.queued) {
					queue(call);
				} 
				else {
					call.execute();
				}
			}
			else {
				throw new Error("Cannot post a call; no kaltura config has been set.");
			}
			return call;
		}
		
		/**
		 * add a call to the calls queue 
		 * @param call call to add
		 */
		protected function queue(call:KalturaCall):void {
			if (!_queue) {
				_queue = new QueuedRequest();
			}
			_queue.addAction(call);
			dispatchEvent(new Event(KalturaClient.CALL_QUEUED));
		}
		
		
		/**
		 * post all calls in the queue and reset it.
		 */
		public function flush():void {
			if (_queue) {
				if (_queue.calls.length > 1) {
					post(_queue);
				} else {
					// if there is only one call, don't post it as a multirequest.
					// this makes fiddler easier to use.
					_queue.calls[0].execute();
				}
				_queue = null;
				dispatchEvent(new Event(KalturaClient.QUEUE_FLUSHED));
			}
		}


		/**
		 * determines whether the client works in queueing mode or not.
		 * */
		public function get queueing():Boolean {
			return _queueing;
		}


		/**
		 * @private
		 */
		public function set queueing(value:Boolean):void {
			_queueing = value;
		}

	}
}
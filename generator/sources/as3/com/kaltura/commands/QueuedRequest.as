// ===================================================================================================
//                           _  __     _ _
//                          | |/ /__ _| | |_ _  _ _ _ __ _
//                          | ' </ _` | |  _| || | '_/ _` |
//                          |_|\_\__,_|_|\__|\_,_|_| \__,_|
//
// This file is part of the Kaltura Collaborative Media Suite which allows users
// to do with audio, video, and animation what Wiki platfroms allow them to do with
// text.
//
// Copyright (C) 2006-2011  Kaltura Inc.
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Affero General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Affero General Public License for more details.
//
// You should have received a copy of the GNU Affero General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// @ignore
// ===================================================================================================
package com.kaltura.commands {
	import com.kaltura.delegates.QueuedRequestDelegate;
	import com.kaltura.errors.KalturaError;
	import com.kaltura.events.KalturaEvent;
	import com.kaltura.net.KalturaCall;

	public class QueuedRequest extends KalturaCall {

		/**
		 * a list of calls to execute.
		 */
		public var calls:Vector.<KalturaCall>;

		/**
		 * params mapped between calls.
		 * holds objects of type <code> {fromRequestIndex, fromRequestParam, toRequestIndex, toRequestParam} </code> 
		 */		
		private var _mapParamArr:Array = new Array();


		public function QueuedRequest() {
			service = 'multirequest';
			calls = new Vector.<KalturaCall>();
			queued = false;
		}


	 	/**
		 * add the given call to the calls list
		 * @param kalturaCall	call to add
		 */
		public function addAction(kalturaCall:KalturaCall):void {
			calls.push(kalturaCall);
		}
		
		
		override public function execute():void {
			var keyArray:Array = new Array();
			var valueArr:Array = new Array();
			
			var ind:int = 0;	// the index of the call in the request

			for (var j:int = 0; j < calls.length; j++) {
				if (calls[j] is MultiRequest) {
					var paramOffset:int = ind;	// save offset for param mapping
					// get the param mapping from the original call
					fixParamMapping(calls[j] as MultiRequest, paramOffset);
					
					var actions:Array = (calls[j] as MultiRequest).actions;
					for each (var call:KalturaCall in actions) {
						addCall(ind, call, keyArray, valueArr);
						ind++;
					}
					// add simple params (no offset handling here!!)
					for (var oKey:String in (calls[j] as MultiRequest).addedParams)
						setRequestArgument(oKey, (calls[j] as MultiRequest).addedParams[oKey]);
				}
				else {
					addCall(ind, calls[j], keyArray, valueArr);
					ind ++;
				}
			}

			for (var i:uint = 0; i < keyArray.length; i++)
				setRequestArgument(keyArray[i], valueArr[i]);

			delegate = new QueuedRequestDelegate(this, config);
		}
		
		
		/**
		 * map a result of one call to input of another.
		 * @param fromRequestIndex
		 * @param fromRequestParam
		 * @param toRequestIndex
		 * @param toRequestParam
		 */		
		protected function mapMultiRequestParam(fromRequestIndex:int, fromRequestParam:String, toRequestIndex:int, toRequestParam:String):void {
			var obj:Object = {fromRequestIndex: fromRequestIndex, fromRequestParam: fromRequestParam, toRequestIndex: toRequestIndex, toRequestParam: toRequestParam};
			_mapParamArr.push(obj);
		}
		
		
		/**
		 * get the param mapping from the given multirequest
		 * and add it to the delayed call with new indices 
		 * @param mr	the source MR
		 */
		protected function fixParamMapping(mr:MultiRequest, offset:int):void {
			var params:Array = mr.mapParamArr;
			var o:Object;
			for each (var src:Object in params) {
				// create a new object - don't override the original value.
				o = {fromRequestIndex:src.fromRequestIndex + offset, fromRequestParam:src.fromRequestParam, 
					 toRequestIndex:src.toRequestIndex + offset, toRequestParam:src.toRequestParam};
				_mapParamArr.push(o);
			}
		}
		

		/**
		 * add a call to the actual request
		 * @param ind	the index on which to add the call
		 * @param call	the call to add
		 * @param keyArray 	keys array
		 * @param valueArr	values array
		 */
		protected function addCall(ind:int, call:KalturaCall, keyArray:Array, valueArr:Array):void {
			// add service and action
			keyArray.push((ind + 1) + ":service");
			valueArr.push(call.service);
			keyArray.push((ind + 1) + ":action");
			valueArr.push(call.action);
			
			// add call arguments:
			// if the value was given in the param map, use it. otherwise, use the value given with the key.
			var argsArr:Array = (call.args.toString()).split('&');
			for (var k:int = 0; k < argsArr.length; k++) {
				var inMap:Boolean = false;
				var key:String = decodeURIComponent(argsArr[k].split('=')[0]);
				
				//search the key in param map
				for (var m:int = 0; m < _mapParamArr.length; m++) {
					if (_mapParamArr[m].toRequestParam == key && _mapParamArr[m].toRequestIndex == (ind + 1)) {
						inMap = true;
						keyArray.push((ind + 1) + ":" + key);
						valueArr.push("{" + _mapParamArr[m].fromRequestIndex + ":result:" + _mapParamArr[m].fromRequestParam + "}");
					}
				}
				
				//if not in the multi request map 
				if (!inMap && argsArr[k]) {
					keyArray.push((ind + 1) + ":" + key);
					valueArr.push(decodeURIComponent(argsArr[k].split('=')[1]));
				}
			}
		}
		
		
		/**
		 * let each call handle its matching result 
		 * @param result	an array with all results of all calls.
		 */
		override public function handleResult(result:Object):void {
			this.result = result;
			success = true;
			var results:Array = result as Array;
			for (var i:int = 0; i< calls.length; i++) {
				if (results[i] is KalturaError) {
					calls[i].handleError(results[i]);
				}
				else {
					calls[i].handleResult(results[i]);
				}
			}
		}
		
		
		/**
		 * if we got here, there was a problem with the entire call and we 
		 * don't have individual answers. make each call dispatch an error.  
		 * @param error
		 */		
		override public function handleError(error:KalturaError):void {
			this.error = error;
			success = false;
			error.requestArgs = args;
			for (var i:int = 0; i< calls.length; i++) {
				calls[i].handleError(error);
			}
		}
	}
}
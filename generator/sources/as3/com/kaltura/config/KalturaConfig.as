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
package com.kaltura.config 
{	
	import flash.events.EventDispatcher;

	public class KalturaConfig extends EventDispatcher implements IKalturaConfig
	{
		protected var _partnerId:String; 
		protected var _ks:String;
		protected var _clientTag:String;
		protected var _protocol:String = "http://";
		protected var _domain : String = "www.kaltura.com";
		protected var _srvUrl:String = "/api_v3/index.php"; //will be defined by the genertor
		protected var _ignoreNull : int = 1;
		
		public function KalturaConfig() {
			super();
		}

		/**
		 * the internet protocol to use with outgoing calls,
		 * i.e. <code>http://</code>
		 * */
		public function set protocol(value:String):void { _protocol= value; }
		public function get protocol():String { return _protocol; }

		/**
		 * the domain for posting calls,
		 * i.e. <code>www.kaltura.com</code>
		 */		
		public function set domain(value:String):void { _domain= value; }
		public function get domain():String { return _domain; }
		
		/**
		 * services url on given domain,
		 * i.e. <code>/api_v3/index.php</code>
		 */		
		public function set srvUrl(value:String):void { _srvUrl= value; }
		public function get srvUrl():String { return _srvUrl; }

		/**
		 * id of the partner to which these calls are related
		 */		
		public function get partnerId():String { return _partnerId;	}
		public function set partnerId(value:String):void {  _partnerId = value; }

		/**
		 * Kaltura Session key to use for processing calls
		 */		
		public function get ks():String { return _ks; }
		public function set ks(value:String):void { _ks = value; }
		
		public function get clientTag():String { return _clientTag; }
		public function set clientTag(value:String):void { _clientTag = value; }

		public function get ignoreNull():int { return _ignoreNull; }
		public function set ignoreNull(value:int):void { _ignoreNull = value; }
	}
}
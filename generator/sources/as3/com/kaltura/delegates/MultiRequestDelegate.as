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
package com.kaltura.delegates
{
	import com.kaltura.commands.MultiRequest;
	import com.kaltura.config.KalturaConfig;
	import com.kaltura.errors.KalturaError;
	import com.kaltura.net.KalturaCall;
	
	import flash.utils.getDefinitionByName;
	import flash.utils.getQualifiedClassName;

	public class MultiRequestDelegate extends WebDelegateBase
	{
		public function MultiRequestDelegate(call:KalturaCall, config:KalturaConfig)
		{
			super(call, config);
		}

		override public function parse( result : XML ) : *
		{
			var resArr : Array = new Array();
			for ( var i:int=0; i<(call as MultiRequest).actions.length; i++ )
			{
				var callClassName : String = getQualifiedClassName( (call as MultiRequest).actions[i] );
				var commandName : String = callClassName.split("::")[1];
				var packageArr : Array =  (callClassName.split("::")[0]).split(".");
				var importFrom : String = packageArr[packageArr.length-1];

				var clsName : String = "com.kaltura.delegates."+importFrom+"."+ commandName +"Delegate"; //'com.kaltura.delegates.session.SessionStartDelegate'
				var cls : Class = getDefinitionByName( clsName ) as Class;//(') as Class;

				var myInst : Object = new cls(null , null);

				//build the result as a regular result
				var res:XML = new XML("<result><result/></result>");
                res.result.appendChild(result.result.item[i].children());

				try {
					var obj : Object = (myInst as WebDelegateBase).parse( res );
					resArr.push( obj );
				} catch (e:Error) {
					var kErr : KalturaError = new KalturaError();
					kErr.errorCode = String(e.errorID);
					kErr.errorMsg = e.message;
					resArr.push( kErr );
				}
			}

			return resArr;
		}
	}
}
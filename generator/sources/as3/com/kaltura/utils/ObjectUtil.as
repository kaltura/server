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
package com.kaltura.utils
{
	import flash.utils.describeType;
	
	/**
	 * ObjectUtil class holds different utilities for use with objects
	 */	
	public class ObjectUtil
	{
		/**
		 * retreives a list of all keys on the given object 
		 * @param obj	the object to operate on
		 * @return an array with all keys as strings
		 */		
		public static function getObjectAllKeys( obj : Object ) : Array
		{
			var arr : Array = new Array();
			arr = getObjectStaticKeys( obj );
			arr = arr.concat( getObjectDynamicKeys( obj ) );
			return arr;
		}
		
		/**
		 * retreives a list of all the keys defined at authoring time. 
		 * @param obj	the object to operate on
		 * @return an array with all keys as strings
		 */		
		public static function getObjectStaticKeys( obj : Object ) : Array
		{
			var arr : Array = new Array();
			var classInfo:XML = describeType(obj);
			// don't take keys defined in ObjectProxy (i.e., uid)
			var accessors:XMLList = classInfo..accessor.(@declaredBy != "mx.utils::ObjectProxy"); 
			for each (var v:XML in accessors) {
				arr.push( v.@name.toString() );
			}
			return arr;
		}
		
		/**
		 * retreives a list of all the keys defined at runtime (for dynamic objects). 
		 * @param obj	the object to operate on
		 * @return an array with all keys as strings
		 */
		public static function getObjectDynamicKeys( obj : Object ) : Array
		{
			var arr : Array = new Array();
			for( var str:String in obj )
				arr.push( str );
			
			return arr;
		}
		
		/**
		 * retreives a list of all the values on the given object. 
		 * @param obj	the object to operate on
		 * @return an array with all values
		 */
		public static function getObjectAllValues( obj : Object ) : Array
		{
			var arr : Array = new Array();
			arr = getObjectStaticValues( obj );
			arr = arr.concat( getObjectDynamicValues( obj ) );
			return arr;
		}
		
		/**
		 * retreives a list of all the values of keys defined at authoring time. 
		 * @param obj	the object to operate on
		 * @return an array with all values
		 */
		public static function getObjectStaticValues( obj : Object ) : Array
		{
			var arr : Array = new Array();
			var classInfo:XML = describeType(obj);
			for each (var v:XML in classInfo..variable) 
			arr.push( obj[v.@name] );
			
			return arr;
		}
		
		/**
		 * retreives a list of all the values of keys defined at runtime (for dynamic objects). 
		 * @param obj	the object to operate on
		 * @return an array with all values
		 */
		public static function getObjectDynamicValues( obj : Object ) : Array
		{
			var arr : Array = new Array();
			for( var str:String in obj )
				arr.push( obj[str] );
			
			return arr;
		}
		
		
		/**
		 * compare variables and properties of 2 given objects 
		 * this function deliberately ignors the property uid. 
		 * @param object1
		 * @param object2
		 * @return 
		 * 
		 */		
		public static function compareObjects(object1:Object,object2:Object):Boolean
		{
			var ob1:Object = describeObject(object1);
			var ob2:Object = describeObject(object2);
			//run on obj1, check if the value exist in ob2 and check its value
			for (var o:Object in ob1)
			{
				if (ob2.hasOwnProperty(o))
				{
					if(ob1[o] != ob2[o] && o!="uid")
						return false;
					
				} else 
				{
					return false;
				}
			} 
			//run on obj2, check if the value exist in ob1 and check its value
			for (var p:Object in ob2)
			{
				if (ob1.hasOwnProperty(p))
				{
					if(ob2[p] != ob1[p] && p!="uid")
						return false;
					
				} else 
				{
					return false;
				}
			} 
			
			return true;
		}
		
		/**
		 * Return an object that holds all variables and properties and their values 
		 * @param obj
		 * @return 
		 * 
		 */
		public static function describeObject(obj:Object):Object 
		{ 
			var ob:Object = new Object();
			
			var classInfo:XML = describeType(obj);
			//map all variables
			for each (var v:XML in classInfo..variable) 
			{
				ob[v.@name] = obj[v.@name];
			}
			//map all properties
			for each (var a:XML in classInfo..accessor) 
			{
				ob[a.@name] = obj[a.@name];
			} 
			return ob;
		}
		
		/**
		 * Copy attributes from source object to target object 
		 * @param source
		 * @param target
		 * 
		 */		
		public static function copyObject(source:Object, target:Object):void {
			var atts:Array = getObjectAllKeys(source);
			for (var i:int = 0; i< atts.length; i++) {
				target[atts[i]] = source[atts[i]];
			} 
		}
		
	}
}
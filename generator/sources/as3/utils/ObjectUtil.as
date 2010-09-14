package com.kaltura.utils
{
	import flash.utils.describeType;
	
	public class ObjectUtil
	{
		public static function getObjectAllKeys( obj : Object ) : Array
		{
			var arr : Array = new Array();
			arr = getObjectStaticKeys( obj );
			arr = arr.concat( getObjectDynamicKeys( obj ) );
			return arr;
		}
		
		public static function getObjectStaticKeys( obj : Object ) : Array
		{
			var arr : Array = new Array();
			var classInfo:XML = describeType(obj);

			for each (var v:XML in classInfo..accessor) 
				arr.push( v.@name.toString() );

			return arr;
		}
		
		public static function getObjectDynamicKeys( obj : Object ) : Array
		{
			var arr : Array = new Array();
			for( var str:String in obj )
				arr.push( str );

			return arr;
		}
		
		public static function getObjectAllValues( obj : Object ) : Array
		{
			var arr : Array = new Array();
			arr = getObjectStaticValues( obj );
			arr = arr.concat( getObjectDynamicValues( obj ) );
			return arr;
		}
		
		public static function getObjectStaticValues( obj : Object ) : Array
		{
			var arr : Array = new Array();
			var classInfo:XML = describeType(obj);
			for each (var v:XML in classInfo..variable) 
				arr.push( obj[v.@name] );
			
			return arr;
		}
		
		public static function getObjectDynamicValues( obj : Object ) : Array
		{
			var arr : Array = new Array();
			for( var str:String in obj )
				arr.push( obj[str] );
				
			return arr;
		}
	}
}
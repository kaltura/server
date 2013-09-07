package com.kaltura.core
{
	import com.kaltura.utils.ObjectUtil;
	
	import flash.utils.describeType;
	import flash.utils.getDefinitionByName;

	
	public class KClassFactory 
	{
		/**
		 * the vo class to create
		 * */
	 	public var generator:Class;
	 	
		
	    public function KClassFactory(generator:Class = null)
	    {
			super();
	
	    	this.generator = generator;
	    }
	
		public function newInstanceFromObject( properties:Object = null ):*
		{
			var instance:Object = new generator();

	        if (properties != null)
	        {
	        	for (var p:String in properties)
				{
	        		instance[p] = properties[p];
				}
	       	}
	
	       	return instance;
		}
		
		/**
		 * create instance of "generator" according to the received info 
		 * @param xmlList	properties of the required object
		 * @return 	new "generator" instance with populated properties
		 */
		public function newInstanceFromXML( xmlList:XMLList = null ):*
		{
			var instance:Object = new generator();
			
			var descType:XML;	// describeType of instance, so we only get it once
			
			var props:XMLList = xmlList.children();	// object properties
			var propName:String;	// name of property being processed
			var propChildren:XMLList;	// children of the property being processed
			
	        for each(var prop:XML in props) {
				propName = prop.name().toString();
	        	if(prop.hasComplexContent()) 
				{
					propChildren = prop.children();
					
					// Assumption that Array will always consist of <item></item>
	        		if(propChildren[0].name() == "item") 
					{
	        			instance[propName] = new Array();
	        			for each( var item:XML in propChildren )
	        			{
							instance[propName].push(setObject( item.objectType , item ));
	        			}
	        		}
	        		else //if complex object and not an array
	        		{
						var newComplexObject : *;
						try {
							newComplexObject = setObject( prop.objectType , prop );
							instance[propName] = newComplexObject;
						} 
						catch(e:Error) {
							//if the object can't be cast don't throw an error and try to populate it
							if (!descType) {
								descType = describeType( instance );
							}
							var xmlItem : XML;
							var newObj:Object;
						
							//I am searching for the item to take his exact type and create a new instance from it
							for each (xmlItem in descType.children()) {
								if(propName == xmlItem.@name.toString()){
									var testType : String = String(xmlItem.@type.toString()).replace("::","." );
									newObj = new (getDefinitionByName(testType) as Class);
								}
							}
							
							//now i will copy all the object attributes to this instance so we will have it 
							// even if we are not supporting it
							var objectKeys : Array = ObjectUtil.getObjectAllKeys( newComplexObject );
							for(var i:int=0;i<objectKeys.length; i++)
							{
								//TODO if the value is an empty string, use the default value for the 
								// required type
								if (newComplexObject[objectKeys[i]]) {
									newObj[objectKeys[i]] = newComplexObject[objectKeys[i]];
								}
							}
							
							instance[propName] = newObj;
						}
	        		}
	        	}
	        	else
	        	{
					if (!descType) {
						descType = describeType( instance );
					}
	        		if( propName && !propIsArray(descType, propName) )
	        		{
	        			if(instance[propName] is Boolean)
	        			{
	        				if( prop.toString() == "1")
	        					instance[propName] = true;
	        				else
	        					instance[propName] = false;
	        			}
						else
						{
							instance[propName] = prop.toString(); //casting from String to Number / int if needed
						}
	        		}	
	        	}
	       	}
	
	       	return instance;
		}
		
		/**
		 * checks whether an attribute is of type Array 
		 * @param description	describeType of the class whose attribute is being investigated
		 * @param propName	name of the attribute being investigated
		 * @return true if instance.prop is typed as Array
		 */		
		private function propIsArray(description:XML, propName:String):Boolean {
			if (propName == "objectType") return false;
			
			var props:XMLList = description.children();
			for each (var xmlItem:XML in props) {
				if(propName == xmlItem.@name.toString()){
					var testType:String =  String(xmlItem.@type.toString()).replace("::","." );
					if (testType == "Array") {
						return true;
					}
				}
			}
			return false;
		}
		
		/**
		 * create sub-object 
		 * @param objectType	type of object to create
		 * @param xmlInfo		properties and values of the new object
		 * @return the new object
		 */
		private function setObject(objectType : String , xmlInfo : XML ) : Object
		{
			var cls : Class;
			try {
				cls = getDefinitionByName('com.kaltura.vo.'+ objectType) as Class;
			}
			catch( e : Error ){
				cls = Object;
			}
			
			return new KClassFactory( cls ).newInstanceFromXML( XMLList(xmlInfo));
		}
	}
}

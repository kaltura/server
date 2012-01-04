package com.kaltura.core
{
	import com.kaltura.utils.ObjectUtil;
	
	import flash.utils.describeType;
	import flash.utils.getDefinitionByName;

	
	public class KClassFactory 
	{
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
		
		public function newInstanceFromXML( xmlList:XMLList = null ):*
		{
			var instance:Object = new generator();
	        for each(var prop:XML in xmlList.children())
	        {
	        	if(prop.hasComplexContent())
	        	{
	        		if(prop.children()[0].name() == "item") //Assumption that Array will always consist of <item></item>
	        		{
	        			var arrName : String = prop.name();
	        			instance[arrName] = new Array();
	        			
	        			for each( var item:XML in prop.children() )
	        			{
							instance[arrName].push(setObject( instance , arrName , item.objectType , item ));
	        			}
	        		}
	        		else //if complex object and not an array
	        		{
						try {
							var newComplexObject : * = setObject( instance , prop.name() , prop.objectType , prop );
							instance[prop.name()] = newComplexObject;
						} catch(e:Error) {
							//if the object can't be cast don't throw an error and try to populate it
							var description:XML = describeType( instance );
							var xmlItem : XML;
						
							//I am searching for the item to take his exact type and create a new instance from it
							for each (xmlItem in description.children()) {
								if(prop.name() == xmlItem.@name.toString()){
									var testType : String =  String(xmlItem.@type.toString()).replace("::","." );
									instance[prop.name()] = new (getDefinitionByName(testType) as Class);
								}
							}
							
							//now i will copy all the object attributes to this instance so we will have it even if we are not
							//supporting it
							var objectKeys : Array = ObjectUtil.getObjectAllKeys( newComplexObject );
							for(var i:int=0;i<objectKeys.length-1; i++)
							{
								//TODO if the value is an empty string, use the default value for the 
								// required type
								if (newComplexObject[objectKeys[i]]) {
									instance[prop.name()][objectKeys[i]] = newComplexObject[objectKeys[i]];
								}
							}
						}
	        		}
	        	}
	        	else
	        	{
					//TODO instead of using "is Array" use the class definition 
					
					
	        		if( prop.name() && !propIsArray(instance, prop) )
	        		{
	        			if(instance[prop.name()] is Boolean)
	        			{
	        				if( prop.toString() == "1")
	        					instance[prop.name()] = true;
	        				else
	        					instance[prop.name()] = false;
	        			}
						else
						{
							instance[prop.name()] = prop.toString(); //casting from String to Number / int if needed
						}
	        		}	
	        	}
	       	}
	
	       	return instance;
		}
		
		/**
		 * checks whether an attribute is of type Array 
		 * @param instance	instance of the class whose attribute is being investigated
		 * @param prop	nameof the attribute being investigated
		 * @return true if instance.prop is typed as Array
		 */		
		private function propIsArray(instance:Object, prop:XML):Boolean {
			var description:XML = describeType( instance );
			for each (var xmlItem:XML in description.children()) {
				if(prop.name() == xmlItem.@name.toString()){
					var testType:String =  String(xmlItem.@type.toString()).replace("::","." );
					if (testType == "Array") {
						return true;
					}
				}
			}
			return false;
		}
		
		private function setObject( instance : Object , propName : String , objectType : String , xmlInfo : XML ) : Object
		{
			var cls : Class = null;
			try{
				cls = getDefinitionByName('com.kaltura.vo.'+ objectType) as Class;
			}
			catch( e : Error ){
				cls = Object;
			}
			
			return new KClassFactory( cls ).newInstanceFromXML( XMLList(xmlInfo));
		}
	}
}

<?php

require_once (dirname(__FILE__) . '/../bootstrap.php');

class objectHelper{

		
		/**
		 * 
		 * gets an object and returns his id
		 * @param unknown_type $object
		 * @return string - the given object Id  
		 */
		static public function getObjectId($object)
		{
			if($object instanceof BaseObject)
			{
				$objectId = $object->getByName("Id");	
			}
			else if ($object instanceof KalturaObjectBase)
			{
				//TODO: check if all kaltura objects are supported
				$reflector = new ReflectionObject($object);
				$idProperty  = $reflector->getProperty("id");
				$objectId = $idProperty->getValue($object);
			}
			else 
			{
				$objectId = $object;
			}
				
			return $objectId;
		}

		/**
		 * 
		 * gets an object and returns his type or class name
		 * @param unknown_type $object
		 * @return string - the given object type  
		 */
		static public function getObjectType($object)
		{
			if($object instanceof BaseObject)
			{
				$objectType = get_class($object);	
			}
			else if ($object instanceof KalturaObjectBase)
			{
				//TODO: check if all kaltura objects are supported
				$objectType = get_class($object);
			}
			else 
			{
				$objectType = gettype($object);
			}
				
			return $objectType;
		}
}
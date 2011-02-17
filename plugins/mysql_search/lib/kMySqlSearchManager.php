<?php

class kMySqlSearchManager implements kObjectUpdatedEventConsumer, kObjectAddedEventConsumer
{
	const MYSQL_INDEX_NAME = 'Search';
	
	/**
	 * @param string $baseName
	 * @return string
	 */
	public static function getMySqlSearchObject($baseName, $id)
	{
		$objectClass = self::MYSQL_INDEX_NAME . ucfirst($baseName);
		$peerClass = $objectClass . 'Peer';
		if(!class_exists($peerClass))
			return null;
			
		$object = call_user_func(array($peerClass, 'retrieveByPK'), $id);
		if($object)
			return $object;
			
		if(class_exists($objectClass))
			return new $objectClass();
			
		return null;
	}
	
	/**
	 * @param BaseObject $object
	 * @return bool true if should continue to the next consumer
	 */
	public function objectUpdated(BaseObject $object)
	{
		if(!($object instanceof IIndexable))
			return true;

		$this->saveToMySql($object);
		return true;
	}
	
	/**
	 * @param BaseObject $object
	 * @return bool true if should continue to the next consumer
	 */
	public function objectAdded(BaseObject $object)
	{
		if(!($object instanceof IIndexable))
			return true;

		$this->saveToMySql($object);
		return true;
	}
	
	/**
	 * @param IIndexable $object
	 */
	public function saveToMySql(IIndexable $object)
	{
		$id = $object->getId();
		if(!$id)
		{
			KalturaLog::err("Object [" . get_class($object) . "] id [" . $object->getId() . "] could not be saved to MySql, id is empty");
			return false;
		}
		
		$searchObject = self::getMySqlSearchObject($object->getObjectIndexName(), $id);
		
		$fields = $object->getIndexFieldsMap();
		foreach($fields as $field => $getterName)
		{
			$setterName = str_replace('_', '', $field);
			$getter = "get{$getterName}";
			$setter = "set{$setterName}";
			
			if(!method_exists($searchObject, $setter))
				continue;
				
			$value = call_user_func(array($object, $getter));
			call_user_func(array($searchObject, $setter), $value);
		}
		
		// TODO - load plugins data
		
		$searchObject->save();
	}
}

<?php

class kMySqlSearchManager implements kObjectUpdatedEventConsumer, kObjectAddedEventConsumer
{
	const MYSQL_INDEX_NAME = 'kaltura';
	const MYSQL_MAX_RECORDS = 1000;
	
	/**
	 * @param string $baseName
	 * @return string
	 */
	public static function getMySqlIndexName($baseName)
	{
		return self::MYSQL_INDEX_NAME . '_' . $baseName;
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

		$this->saveToMySql($object, true);
		return true;
	}
	
	/**
	 * Get the [status] column value translated for MySql supported values.
	 * 
	 * @return     int
	 */
	public function getMySqlId($object)
	{
		return crc32($object->getId());
	}

	// TODO remove $force after replace bug solved
	/**
	 * @param IIndexable $object
	 * @param bool $isInsert
	 * @param bool $force
	 * @return string|bool
	 */
	public function getMySqlSaveSql(IIndexable $object, $isInsert = false, $force = false)
	{
		$id = $object->getIntId();
		if(!$id)
		{
			KalturaLog::err("Object [" . get_class($object) . "] id [" . $object->getId() . "] could not be saved to MySql, int_id is empty");
			return false;
		}
		
//		if(!$force && !$isInsert && !$this->saveToMySqlRequired($object))
//			return false;
			
		$data = array('id' => $id);
		
		// NOTE: the order matters
		$dataStrings = array();
		$dataInts = array();
		$dataTimes = array();
		
		$fields = $object->getIndexFieldsMap();
		foreach($fields as $field => $getterName)
		{
			$fieldType = $object->getIndexFieldType($field);
			$getter = "get{$getterName}";
			
			switch($fieldType)
			{
				case IIndexable::FIELD_TYPE_STRING:
					$dataStrings[$field] = $object->$getter();
					break;
					
				case IIndexable::FIELD_TYPE_INTEGER:
					$dataInts[$field] = $object->$getter();
					break;
					
				case IIndexable::FIELD_TYPE_DATETIME:
					$dataTimes[$field] = $object->$getter(null);
					break;
			}
		}
		
		// TODO - remove after solving the replace bug that removes all fields
		$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaSearchDataContributor');
		$mySqlPluginsData = array();
		foreach($pluginInstances as $pluginName => $pluginInstance)
		{
			KalturaLog::debug("Loading $pluginName MySql texts");
			$mySqlPluginData = null;
			try
			{
				$mySqlPluginData = $pluginInstance->getSearchData($object);
			}
			catch(Exception $e)
			{
				KalturaLog::err($e->getMessage());
				continue;
			}
			
			if($mySqlPluginData)
			{
				KalturaLog::debug("MySql data for $pluginName [$mySqlPluginData]");
				$mySqlPluginsData[] = $mySqlPluginData;
			}
		}
		if(count($mySqlPluginsData))
			$dataStrings['plugins_data'] = implode(',', $mySqlPluginsData);
		
		foreach($dataStrings as $key => $value)
		{
			$search=array("\\","\0","\n","\r","\x1a","'",'"');
			$replace=array("\\\\","\\0","\\n","\\r","\\Z","\\'",'\"');
			$value = str_replace($search, $replace, $value);
			$data[$key] = "'$value'";
		}
		
		foreach($dataInts as $key => $value)
		{
			$value = (int)$value;
			$data[$key] = $value;
		}
		
		foreach($dataTimes as $key => $value)
		{
			$value = (int)$value;
			$data[$key] = $value;
		}
		
		$values = implode(',', $data);
		$fields = implode(',', array_keys($data));
		
		$index = kMySqlSearchManager::getMySqlIndexName($object->getObjectIndexName());
		$command = 'insert';
		if(!$isInsert)
			$command = 'replace';
		
		return "$command into $index ($fields) values($values)";
	}
		
	/**
	 * @param string $sql
	 * @param IIndexable $object
	 * @return bool
	 */
	public function execMySql($sql, IIndexable $object)
	{
		KalturaLog::debug($sql);
		
		$con = DbManager::getMySqlConnection();
		$ret = $con->exec($sql);
		if($ret)
			return true;
			
		$arr = $con->errorInfo();
		KalturaLog::err($arr[2]);
		return false;
	}
		
	/**
	 * @param IIndexable $object
	 * @param bool $isInsert
	 * @param bool $force 
	 * TODO remove $force after replace bug solved
	 * 
	 * @return bool
	 */
	public function saveToMySql(IIndexable $object, $isInsert = false, $force = false)
	{
		KalturaLog::debug('Updating MySql for object [' . get_class($object) . '] [' . $object->getId() . ']');
		$sql = $this->getMySqlSaveSql($object, $isInsert, $force);
		if(!$sql)
			return true;
		
		return $this->execMySql($sql, $object);
	}
}

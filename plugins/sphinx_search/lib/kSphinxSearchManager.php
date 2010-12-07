<?php

class kSphinxSearchManager implements kObjectUpdatedEventConsumer, kObjectAddedEventConsumer
{
	const SPHINX_INDEX_NAME = 'kaltura';
	const SPHINX_MAX_RECORDS = 1000;
	
	/**
	 * @param string $baseName
	 * @return string
	 */
	public static function getSphinxIndexName($baseName)
	{
		return self::SPHINX_INDEX_NAME . '_' . $baseName;
	}
	
	/**
	 * @param BaseObject $object
	 * @return bool true if should continue to the next consumer
	 */
	public function objectUpdated(BaseObject $object)
	{
		if(!($object instanceof IIndexable))
			return true;

		$this->saveToSphinx($object);
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

		$this->saveToSphinx($object, true);
		return true;
	}
	
	/**
	 * Get the [status] column value translated for sphinx supported values.
	 * 
	 * @return     int
	 */
	public function getSphinxId($object)
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
	public function getSphinxSaveSql(IIndexable $object, $isInsert = false, $force = false)
	{
		$id = $object->getIntId();
		if(!$id)
		{
			KalturaLog::err("Object [" . get_class($object) . "] id [" . $object->getId() . "] could not be saved to sphinx, int_id is empty");
			return false;
		}
		
//		if(!$force && !$isInsert && !$this->saveToSphinxRequired($object))
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
					
				case IIndexable::FIELD_TYPE_STRING:
					$dataTimes[$field] = $object->$getter(null);
					break;
			}
		}
		
		// TODO - remove after solving the replace bug that removes all fields
		$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaSearchDataContributor');
		$sphinxPluginsData = array();
		foreach($pluginInstances as $pluginName => $pluginInstance)
		{
			KalturaLog::debug("Loading $pluginName sphinx texts");
			$sphinxPluginData = null;
			try
			{
				$sphinxPluginData = $pluginInstance->getSearchData($object);
			}
			catch(Exception $e)
			{
				KalturaLog::err($e->getMessage());
				continue;
			}
			
			if($sphinxPluginData)
			{
				KalturaLog::debug("Sphinx data for $pluginName [$sphinxPluginData]");
				$sphinxPluginsData[] = $sphinxPluginData;
			}
		}
		if(count($sphinxPluginsData))
			$dataStrings['plugins_data'] = implode(',', $sphinxPluginsData);
		
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
		
		$index = kSphinxSearchManager::getSphinxIndexName($object->getObjectIndexName());
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
	public function execSphinx($sql, IIndexable $object)
	{
		KalturaLog::debug($sql);
		
		$sphinxLog = new SphinxLog();
		$sphinxLog->setEntryId($object->getEntryId());
		$sphinxLog->setPartnerId($object->getPartnerId());
		$sphinxLog->setSql($sql);
		$sphinxLog->save(myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_SPHINX_LOG));

		if(!kConf::hasParam('exec_sphinx') || !kConf::get('exec_sphinx'))
			return true;
			
		$con = DbManager::getSphinxConnection();
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
	public function saveToSphinx(IIndexable $object, $isInsert = false, $force = false)
	{
		KalturaLog::debug('Updating sphinx for object [' . get_class($object) . '] [' . $object->getId() . ']');
		$sql = $this->getSphinxSaveSql($object, $isInsert, $force);
		if(!$sql)
			return true;
		
		return $this->execSphinx($sql, $object);
	}
}

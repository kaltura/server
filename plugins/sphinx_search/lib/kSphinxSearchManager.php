<?php

class kSphinxSearchManager implements
	kObjectChangedEventConsumer, 
	kObjectCreatedEventConsumer,
	kObjectDataChangedEventConsumer
{
	const SPHINX_INDEX_NAME = 'kaltura_entry';
	const SPHINX_MAX_RECORDS = 10000;
	
	/**
	 * @param BaseObject $object
	 */
	public function objectCreated(BaseObject $object) 
	{
		if(!($object instanceof entry))
			return;

		$this->saveToSphinx($object, true);
	}

	/**
	 * @param BaseObject $object
	 * @param array $modifiedColumns
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns) 
	{
		if(!($object instanceof entry))
			return;

		if($object->isModified())
			$this->saveToSphinx($object);
	}
	
	/**
	 * @param BaseObject $object
	 */
	public function objectDataChanged(BaseObject $object) 
	{
		if(!class_exists('Metadata') || !($object instanceof Metadata))
			return;

		if($object->getObjectType() == Metadata::TYPE_ENTRY)
		{
			$entry = kMetadataManager::getObjectFromPeer($object);
			if ($entry instanceOf entry)
				$this->saveToSphinx($entry, false, true);
		}
	}
	
	public function array2sphinxData(array $arr, $pluginName)
	{
		if(!isset($arr[$pluginName]))
			return null;
			
		$data = $arr[$pluginName];
		if(is_array($data))
			$data = implode(',', $data);
			
		return $data;
	}
	
	public static function getSphinxFields()
	{
		return array(
			entryPeer::INT_ID,
			entryPeer::ID,
			entryPeer::NAME,
			entryPeer::TAGS,
			entryPeer::CATEGORIES_IDS,
			entryPeer::FLAVOR_PARAMS_IDS,
			entryPeer::SOURCE_LINK,
			entryPeer::KSHOW_ID,
			entryPeer::GROUP_ID,
			entryPeer::DESCRIPTION,
			entryPeer::ADMIN_TAGS,
			entryPeer::CUSTOM_DATA,
			entryPeer::KUSER_ID,
			entryPeer::STATUS,
			entryPeer::TYPE,
			entryPeer::MEDIA_TYPE,
			entryPeer::VIEWS,
			entryPeer::PARTNER_ID,
			entryPeer::MODERATION_STATUS,
			entryPeer::DISPLAY_IN_SEARCH,
			entryPeer::LENGTH_IN_MSECS,
			entryPeer::ACCESS_CONTROL_ID,
			entryPeer::MODERATION_COUNT,
			entryPeer::RANK,
			entryPeer::PLAYS,
			entryPeer::CREATED_AT,
			entryPeer::UPDATED_AT,
			entryPeer::MODIFIED_AT,
			entryPeer::MEDIA_DATE,
			entryPeer::START_DATE,
			entryPeer::END_DATE,
			entryPeer::AVAILABLE_FROM,
		);
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

	public function saveToSphinxRequired($entry)
	{
		$cols = self::getSphinxFields();
		foreach($cols as $col)
			if($entry->isColumnModified($col))
				return true;
				
		return false;
	}
	
	// TODO remove $force after replace bug solved
	/**
	 * @param entry $entry
	 * @param bool $isInsert
	 * @param bool $force
	 * @return string|bool
	 */
	public function getSphinxSaveSql(entry $entry, $isInsert = false, $force = false)
	{
		$id = $entry->getIntId();
		if(!$id)
		{
			KalturaLog::err("Entry [" . $entry->getId() . "] could not be saved to sphinx, int_id is empty");
			return false;
		}
		
		if(!$force && !$isInsert && !$this->saveToSphinxRequired($entry))
			return false;
			
		$categoriesIds = explode(',', $entry->getCategoriesIds());
		$categories = implode(',', $categoriesIds);
		
		$flavorParamsIds = explode(',', $entry->getFlavorParamsIds());
		$flavorParams = implode(',', $flavorParamsIds);
		
		$data = array('id' => $id);
		
		// NOTE: the order matters
		$dataStrings = array();
		$dataInts = array();
		$dataTimes = array();
		
//		if($isInsert)
			$dataStrings['entry_id'] = $entry->getId();
			$dataStrings['str_entry_id'] = $entry->getId();
		
//		if($isInsert || $entry->isColumnModified(entryPeer::NAME))
			$dataStrings['name'] = $entry->getName();
			$dataInts['sort_name'] = kUTF8::str2int64($entry->getName());
			
//		if($isInsert || $entry->isColumnModified(entryPeer::TAGS))
			$dataStrings['tags'] = $entry->getTags();
//		if($isInsert || $entry->isColumnModified(entryPeer::CATEGORIES_IDS))
			$dataStrings['categories'] = $categories;
//		if($isInsert || $entry->isColumnModified(entryPeer::FLAVOR_PARAMS_IDS))
			$dataStrings['flavor_params'] = $flavorParams;
//		if($isInsert || $entry->isColumnModified(entryPeer::SOURCE_LINK))
			$dataStrings['source_link'] = $entry->getSourceLink();
//		if($isInsert || $entry->isColumnModified(entryPeer::KSHOW_ID))
			$dataStrings['kshow_id'] = $entry->getKshowId();
//		if($isInsert || $entry->isColumnModified(entryPeer::GROUP_ID))
			$dataStrings['group_id'] = $entry->getGroupId();
//		if($isInsert || $entry->isColumnModified(entryPeer::DESCRIPTION))
			$dataStrings['description'] = $entry->getDescription();
//		if($isInsert || $entry->isColumnModified(entryPeer::ADMIN_TAGS))
			$dataStrings['admin_tags'] = $entry->getAdminTags();
//		if($isInsert || $entry->isColumnModified(entryPeer::LENGTH_IN_MSECS))
			$dataStrings['duration_type'] = entryPeer::getDurationType($entry->getDurationInt());
			
		
		// TODO - implement as multi values - one value per plugin
//		if($isInsert || $entry->isColumnModified(entryPeer::CUSTOM_DATA))
//		{
			$pluginsData = $entry->getPluginData();
			$sphinxPluginsData = array();
			if($pluginsData && is_array($pluginsData))
			{
				foreach($pluginsData as $pluginName => $pluginData)
				{
					$sphinxPluginData = $this->array2sphinxData($pluginsData, $pluginName);
					if($sphinxPluginData)
						$sphinxPluginsData[] = $sphinxPluginData;
				}
			}
			
			// TODO - remove after solving the replace bug that removes all fields
			try
			{
				if(class_exists('Metadata'))
				{
					KalturaLog::debug("Loading metadata sphinx texts");
					$sphinxPluginData = kMetadataManager::getSearchValuesByObject(Metadata::TYPE_ENTRY, $entry->getId());
					if($sphinxPluginData)
					{
						KalturaLog::debug("Sphinx data for metadata: [$sphinxPluginData]");
						$sphinxPluginsData[] = $sphinxPluginData;
					}
				}
								
				if(count($sphinxPluginsData))
				{
					$dataStrings['plugins_data'] = implode(',', $sphinxPluginsData);
					KalturaLog::debug("Adding plugins_data");
				}
			}
			catch(Exception $e)
			{
				KalturaLog::err($e->getMessage());
			}
			
//		}
		
//		if($isInsert)
			$dataInts['int_entry_id'] = $this->getSphinxId($entry);
//		if($isInsert || $entry->isColumnModified(entryPeer::KUSER_ID))
			$dataInts['kuser_id'] = $entry->getKuserId();
//		if($isInsert || $entry->isColumnModified(entryPeer::STATUS))
			$dataInts['entry_status'] = $entry->getStatus();
//		if($isInsert || $entry->isColumnModified(entryPeer::TYPE))
			$dataInts['type'] = $entry->getType();
//		if($isInsert || $entry->isColumnModified(entryPeer::MEDIA_TYPE))
			$dataInts['media_type'] = $entry->getMediaType();
//		if($isInsert || $entry->isColumnModified(entryPeer::VIEWS))
			$dataInts['views'] = $entry->getViews();
//		if($isInsert || $entry->isColumnModified(entryPeer::PARTNER_ID))
			$dataInts['partner_id'] = $entry->getPartnerId();
//		if($isInsert || $entry->isColumnModified(entryPeer::MODERATION_STATUS))
			$dataInts['moderation_status'] = $entry->getModerationStatus();
//		if($isInsert || $entry->isColumnModified(entryPeer::DISPLAY_IN_SEARCH))
			$dataInts['display_in_search'] = $entry->getDisplayInSearch();
//		if($isInsert || $entry->isColumnModified(entryPeer::LENGTH_IN_MSECS))
			$dataInts['duration'] = $entry->getDurationInt();
//		if($isInsert || $entry->isColumnModified(entryPeer::ACCESS_CONTROL_ID))
			$dataInts['access_control_id'] = $entry->getAccessControlId();
//		if($isInsert || $entry->isColumnModified(entryPeer::MODERATION_COUNT))
			$dataInts['moderation_count'] = $entry->getModerationCount();
//		if($isInsert || $entry->isColumnModified(entryPeer::RANK))
			$dataInts['rank'] = $entry->getRank();
//		if($isInsert || $entry->isColumnModified(entryPeer::PLAYS))
			$dataInts['plays'] = $entry->getPlays();
		
//		if($isInsert || $entry->isColumnModified(entryPeer::CREATED_AT))
			$dataTimes['created_at'] = $entry->getCreatedAt(null);
//		if($isInsert || $entry->isColumnModified(entryPeer::UPDATED_AT))
			$dataTimes['updated_at'] = $entry->getUpdatedAt(null);
//		if($isInsert || $entry->isColumnModified(entryPeer::MODIFIED_AT))
			$dataTimes['modified_at'] = $entry->getModifiedAt(null);
//		if($isInsert || $entry->isColumnModified(entryPeer::MEDIA_DATE))
			$dataTimes['media_date'] = $entry->getMediaDate(null);
//		if($isInsert || $entry->isColumnModified(entryPeer::START_DATE))
			$dataTimes['start_date'] = $entry->getStartDate(null);
//		if($isInsert || $entry->isColumnModified(entryPeer::END_DATE))
			$dataTimes['end_date'] = $entry->getEndDate(null);
//		if($isInsert || $entry->isColumnModified(entryPeer::AVAILABLE_FROM))
			$dataTimes['available_from'] = $entry->getAvailableFrom(null);
		
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
		
		$index = kSphinxSearchManager::SPHINX_INDEX_NAME;
		$command = 'insert';
		if(!$isInsert)
			$command = 'replace';
		
		return "$command into $index ($fields) values($values)";
	}
		
	/*
	public function updateSphinx($entry, $field, $value, $type = PDO::PARAM_STR)
	{
		$index = kSphinxSearchManager::SPHINX_INDEX_NAME;
		
		switch($type)
		{
			case PDO::PARAM_STR:
				$search=array("\\","\0","\n","\r","\x1a","'",'"');
				$replace=array("\\\\","\\0","\\n","\\r","\\Z","\\'",'\"');
				$value = str_replace($search, $replace, $value);
				$value = "'$value'";
				break;
				
			case PDO::PARAM_INT:
				if($value < 0)
					throw new Exception("Entry [" . $entry->getId() . "] field [$field] can't be negative [$value]");
				break;
					
			default:
				throw new Exception("Field [$field] type must be PDO::PARAM_STR or PDO::PARAM_INT");
		}
		
		$id = $entry->getIntId();
		$sql = "replace into $index (id, $field) values($id, $value)";
		
		$this->execSphinx($sql);
	}
	*/
		
	/**
	 * @param string $sql
	 * @param entry $entry
	 * @return bool
	 */
	public function execSphinx($sql, entry $entry)
	{
		KalturaLog::debug($sql);
		
		$sphinxLog = new SphinxLog();
		$sphinxLog->setEntryId($entry->getId());
		$sphinxLog->setPartnerId($entry->getPartnerId());
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
		
	// TODO remove $force after replace bug solved
	/**
	 * @param entry $entry
	 * @param bool $isInsert
	 * @param bool $force
	 * @return bool
	 */
	public function saveToSphinx(entry $entry, $isInsert = false, $force = false)
	{
		$sql = $this->getSphinxSaveSql($entry, $isInsert, $force);
		if(!$sql)
			return true;
		
		return $this->execSphinx($sql, $entry);
	}
}

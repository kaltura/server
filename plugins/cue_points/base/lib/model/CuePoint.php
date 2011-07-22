<?php


/**
 * Skeleton subclass for representing a row from the 'cue_point' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.cuePoint
 * @subpackage model
 */
abstract class CuePoint extends BaseCuePoint implements IIndexable 
{
	const CUSTOM_DATA_FIELD_FORCE_STOP = 'forceStop';
	
	public function getChildren()
	{
		if ($this->isNew())
			return array();
			
		$c = new Criteria();
		$c->add(CuePointPeer::PARENT_ID, $this->getId());
		
		return CuePointPeer::doSelect($c);
	}
	
	public function getPuserId()
	{
		$kuser =  KuserPeer::retrieveByPK($this->getKuserId());
	    if(!$kuser)
			throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID);
			
		return $kuser->getPuserId();
	} 
	
	public function setPuserId($v)
	{
		$kuser = kuserPeer::getKuserByPartnerAndUid($this->getPartnerId(), $v);
	    if($kuser)
			throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID);
			
		return $this->setKuserId($kuser->getId());
	} 
	
	/**
	 * generate unique string id for CuePoint
	 */
	private function calculateId()
	{
		$dc = kDataCenterMgr::getCurrentDc();
		for ($i = 0; $i < 10; $i++)
		{
			$id = $dc["id"].'_'.kString::generateStringId();
			$existingObject = CuePointPeer::retrieveByPK($id);
			if ($existingObject){
				KalturaLog::log(__METHOD__ . ": id [$id] already exists");
			}else{
				return $id;
			}
		}
		
		throw new Exception("Could not find unique id for CuePoint");
	}
	
	/**
	 * return true is CuePoint is an descendant of specifed id or itself
	 * @param string $cuePointId
	 */
	public function isDescendant($cuePointId = null)
	{
		if($this->id == $cuePointId)
			return true;
				
		$children = $this->getChildren();
		foreach($children as $child)
		{
			if ($children->isDescendant($cuePointId))
				return true;
		}
		
		return false;	
	}

	public function save(PropelPDO $con = null)
	{
		if ($this->isNew())
			$this->setId($this->calculateId());
			
		return parent::save($con);
	}
	
	/* (non-PHPdoc)
	 * @see BaseCuePoint::postInsert()
	 */
	public function postInsert(PropelPDO $con = null)
	{
		parent::postInsert($con);
		
		kEventsManager::raiseEvent(new kObjectAddedEvent($this));
	}
	
	
	/* (non-PHPdoc)
	 * @see BaseCuePoint::postUpdate()
	 */
	public function postUpdate(PropelPDO $con = null)
	{
		if ($this->alreadyInSave)
			return parent::postUpdate($con);
		
		$objectUpdated = $this->isModified();
		$objectDeleted = false;
		if($this->isColumnModified(CuePointPeer::STATUS) && $this->getStatus() == CuePointStatus::DELETED)
			$objectDeleted = true;
			
		$ret = parent::postUpdate($con);
		
		if($objectDeleted)
			kEventsManager::raiseEvent(new kObjectDeletedEvent($this));
			
		if($objectUpdated)
			kEventsManager::raiseEvent(new kObjectUpdatedEvent($this));
			
		return $ret;
	}

	public function getRoots()
	{
		$ret = array();
		
		$parent = null;
		$roots = array($this->getId());
		if($this->getParentId())
		{
			$ret[] = 'parent ' . $this->getParentId();
			$parent = CuePointPeer::retrieveByPK($this->getParentId());
		}
		
		while($parent)
		{
			$parentId = $parent->getId();
			if(in_array($parentId, $roots))
				break;
				
			$ret[] = "root $parentId";
			$roots[] = $parentId;
			
			if($parent->getParentId())
				$parent = CuePointPeer::retrieveByPK($parent->getParentId());
			else
				$parent = null;
		}
		
			
		if($this->getEntryId())
			$ret[] = 'entry ' . $this->getEntryId();
		
		return implode(',', $ret);
	}

	/* (non-PHPdoc)
	 * @see IIndexable::getObjectIndexName()
	 */
	public function getObjectIndexName()
	{
		return CuePointPeer::TABLE_NAME;
	}

	/* (non-PHPdoc)
	 * @see IIndexable::getIndexFieldsMap()
	 */
	public function getIndexFieldsMap()
	{
		return array(
			'entry_id' => 'entryId',
			'name' => 'name',
			'text' => 'text',
			'tags' => 'tags',
			'roots' => 'roots',
			'int_cue_point_id' => 'indexedId',
			'cue_point_int_id' => 'intId',
			'partner_id' => 'partnerId',
			'start_time' => 'startTime',
			'end_time' => 'endTime',
			'duration' => 'duration',
			'cue_point_status' => 'status',
			'cue_point_type' => 'type',
			'kuser_id' => 'kuserId',
			'partner_sort_value' => 'partnerSortValue',
			'force_stop' => 'forceStop',
			'created_at' => 'createdAt',
			'updated_at' => 'updatedAt',
			'str_entry_id' => 'entryId',
			'str_cue_point_id' => 'id',
		);
	}

	private static $indexFieldTypes = array(
		'entry_id' => IIndexable::FIELD_TYPE_STRING,
		'name' => IIndexable::FIELD_TYPE_STRING,
		'text' => IIndexable::FIELD_TYPE_STRING,
		'tags' => IIndexable::FIELD_TYPE_STRING,
		'roots' => IIndexable::FIELD_TYPE_STRING,
		'int_cue_point_id' => IIndexable::FIELD_TYPE_INTEGER,
		'cue_point_int_id' => IIndexable::FIELD_TYPE_INTEGER,
		'partner_id' => IIndexable::FIELD_TYPE_INTEGER,
		'start_time' => IIndexable::FIELD_TYPE_INTEGER,
		'end_time' => IIndexable::FIELD_TYPE_INTEGER,
		'duration' => IIndexable::FIELD_TYPE_INTEGER,
		'cue_point_status' => IIndexable::FIELD_TYPE_INTEGER,
		'cue_point_type' => IIndexable::FIELD_TYPE_INTEGER,
		'kuser_id' => IIndexable::FIELD_TYPE_INTEGER,
		'partner_sort_value' => IIndexable::FIELD_TYPE_INTEGER,
		'force_stop' => IIndexable::FIELD_TYPE_INTEGER,
		'created_at' => IIndexable::FIELD_TYPE_DATETIME,
		'updated_at' => IIndexable::FIELD_TYPE_DATETIME,
		'str_entry_id' => IIndexable::FIELD_TYPE_STRING,
		'str_cue_point_id' => IIndexable::FIELD_TYPE_STRING,
	);
	
	/**
	 * @return int
	 */
	public function getIndexedId()
	{
		return crc32($this->getId());
	}
	
	public static function getIndexFieldTypes()
	{
		return self::$indexFieldTypes;
	}
	
	/* (non-PHPdoc)
	 * @see IIndexable::getIndexFieldType()
	 */
	public function getIndexFieldType($field)
	{
		if(isset(self::$indexFieldTypes[$field]))
			return self::$indexFieldTypes[$field];
			
		return null;
	}

	/**
	 * Get the [duration] column value.
	 * 
	 * @return     int
	 */
	public function getDuration()
	{
		$end_time = $this->getEndTime();
		if(is_null($end_time))
			return null;
			
		return $end_time - $this->getStartTime();
	}
	
	/**
	 * Set the value of [duration] column.
	 * 
	 * @param      int $v new value
	 * @return     CuePoint The current object (for fluent API support)
	 */
	public function setDuration($v)
	{
		if(is_null($v))
			return $this->setEndTime(null);
			
		if(is_null($this->getStartTime()))
			throw new Exception("Start time must be set before setting duration");
			
		$v = (int) $v;
		return $this->setEndTime($this->getStartTime() + $v);
		
	} // setDuration()
	
	
	public function getForceStop()		{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_FORCE_STOP);}	

	public function setForceStop($v)	{return $this->putInCustomData(self::CUSTOM_DATA_FIELD_FORCE_STOP, (bool)$v);}
	
} // CuePoint

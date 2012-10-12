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
	const CUSTOM_DATA_FIELD_DEPTH = 'depth';
	const CUSTOM_DATA_FIELD_CHILDREN_COUNT = 'childrenCount';
	const CUSTOM_DATA_FIELD_DIRECT_CHILDREN_COUNT = 'directChildrenCount';
	const CUSTOM_DATA_FIELD_ROOT_PARENT_ID = 'rootParentId';
	
	public function getChildren()
	{
		if ($this->isNew())
			return array();
			
		$c = KalturaCriteria::create(CuePointPeer::OM_CLASS);
		$c->add(CuePointPeer::PARENT_ID, $this->getId());
		
		return CuePointPeer::doSelect($c);
	}
	
	public function getPuserId()
	{
		$kuser =  kuserPeer::retrieveByPKNoFilter($this->getKuserId());
	    if(!$kuser)
			throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID);
			
		return $kuser->getPuserId();
	} 
	
	/**
	 * @param string $v puser id
	 * @param bool $isAdmin
	 * @return CuePoint
	 */
	public function setPuserId($puserId)
	{
		if(!$this->getPartnerId())
			throw new Exception("Partner id must be set in order to load puser [$puserId]");
			
		$this->puserId = $puserId;
		$kuser = kuserPeer::getKuserByPartnerAndUid($this->getPartnerId(), $puserId, true);
		if(!$kuser)
		{
			$isAdmin = false;
//			if($puserId == kCurrentContext::$uid)
//				$isAdmin = kCurrentContext::$is_admin_session;
				
			$kuser = kuserPeer::createKuserForPartner($this->getPartnerId(), $puserId, $isAdmin);
		}
		$this->setKuserId($kuser->getId());
	} 
	
	/**
	 * generate unique string id for CuePoint
	 */
	private function calculateId()
	{
		$currentDcId = kDataCenterMgr::getCurrentDcId();
		for ($i = 0; $i < 10; $i++)
		{
			$id = $currentDcId.'_'.kString::generateStringId();
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
		{
			if(is_null($this->getKuserId()))
				$this->setPuserId(kCurrentContext::$uid, kCurrentContext::$is_admin_session);
				
			$this->setId($this->calculateId());
		}
			
		return parent::save($con);
	}
	
	/* (non-PHPdoc)
	 * @see BaseCuePoint::postInsert()
	 */
	public function postInsert(PropelPDO $con = null)
	{
		parent::postInsert($con);
		
		kEventsManager::raiseEvent(new kObjectAddedEvent($this));
		
		$parent = $this->getParent();
		if($parent)
			$parent->increaseChildrenCountAndSave();
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
		{
			kEventsManager::raiseEvent(new kObjectDeletedEvent($this));
			
			$parent = $this->getParent();
			if($parent)
				$parent->decreaseChildrenCountAndSave();
		}
			
		if($objectUpdated)
			kEventsManager::raiseEvent(new kObjectUpdatedEvent($this));
			
		return $ret;
	}

	public function getRoots()
	{
		$ret = array();
		
		$roots = array($this->getId());
		if($this->getParentId())
		{
			$ret[] = 'P' . $this->getParentId();
			$ret[] = 'R' . $this->getRootParentId();
		}
		
		if($this->getEntryId())
			$ret[] = 'E' . $this->getEntryId();
		
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
			'parent_id' => 'parentId',
			'entry_id' => 'entryId',
			'name' => 'name',
			'system_name' => 'systemName',
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
			'sub_type' => 'subType',
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
		'parent_id' => IIndexable::FIELD_TYPE_STRING,
		'entry_id' => IIndexable::FIELD_TYPE_STRING,
		'name' => IIndexable::FIELD_TYPE_STRING,
		'system_name' => IIndexable::FIELD_TYPE_STRING,
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
		'sub_type' => IIndexable::FIELD_TYPE_INTEGER,
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
		return sprintf('%u', crc32($this->getId()));
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
	
	/* (non-PHPdoc)
	 * @see IIndexable::indexToSearchIndex()
	 */
	public function indexToSearchIndex()
	{
		kEventsManager::raiseEventDeferred(new kObjectReadyForIndexEvent($this));
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
	
	public function getCacheInvalidationKeys()
	{
		return array("cuePoint:id=".$this->getId(), "cuePoint:entryId=".$this->getEntryId());
	}
	
	public function getSearchIndexFieldsEscapeType($fieldName)
	{
		return SearchIndexFieldEscapeType::DEFAULT_ESCAPE;
	}
	
	/**
	 * @return Annotation
	 */
	protected function getParent()
	{
		if(!$this->getParentId())
			return null;
			
		return CuePointPeer::retrieveByPK($this->getParentId());
	}


	/**
	 * @return int
	 */
	public function getRootParentId()
	{
		$ret = $this->getFromCustomData(self::CUSTOM_DATA_FIELD_ROOT_PARENT_ID);
		if(!is_null($ret))
			return $ret;
			
		if(!$this->getParentId())
			return $this->getId();
			
		return $this->getParent()->getRootParentId();
	}
	
	/**
	 * @return int
	 */
	public function getDepth()
	{
		$ret = $this->getFromCustomData(self::CUSTOM_DATA_FIELD_DEPTH);
		if(!is_null($ret))
			return $ret;
			
		if(!$this->getParentId())
			return 0;
			
		return $this->getParent()->getDepth() + 1;
	}
	
	protected function increaseChildrenCountAndSave()
	{
		$this->incInCustomData(self::CUSTOM_DATA_FIELD_DIRECT_CHILDREN_COUNT);
		$this->incInCustomData(self::CUSTOM_DATA_FIELD_CHILDREN_COUNT);
		$this->save();
		
		$parent = $this->getParent();
		if($parent)
			$parent->increaseChildrenCountAndSave();
	}
	
	protected function decreaseChildrenCountAndSave()
	{
		$this->decInCustomData(self::CUSTOM_DATA_FIELD_DIRECT_CHILDREN_COUNT);
		$this->decInCustomData(self::CUSTOM_DATA_FIELD_CHILDREN_COUNT);
		$this->save();
		
		$parent = $this->getParent();
		if($parent)
			$parent->decreaseChildrenCountAndSave();
	}
	
	
	/**
	 * @return int
	 */
	public function getDirectChildrenCount()
	{			
		if ($this->isNew())
			return 0;
			
		$ret = $this->getFromCustomData(self::CUSTOM_DATA_FIELD_DIRECT_CHILDREN_COUNT);
		if(!is_null($ret))
			return $ret;
			
		$c = KalturaCriteria::create(CuePointPeer::OM_CLASS);
		$c->add(CuePointPeer::PARENT_ID, $this->getId());
		$c->applyFilters();
		
		return $c->getRecordsCount();
	}
	
	/**
	 * @return int
	 */
	public function getChildrenCount()
	{			
		if ($this->isNew())
			return 0;
			
		$ret = $this->getFromCustomData(self::CUSTOM_DATA_FIELD_CHILDREN_COUNT);
		if(!is_null($ret))
			return $ret;
			
		$ret = 0;
		foreach($this->getChildren() as $child)
		{
			$ret ++;
			$ret += $child->getChildrenCount();
		}
			
		return $ret;
	}
	
	/**
	 * @param int
	 */
	protected function setDepth($depth)
	{
		$this->putInCustomData(self::CUSTOM_DATA_FIELD_DEPTH, $depth);
	}
	
	/**
	 * @param int
	 */
	protected function setRootParentId($id)
	{
		$this->putInCustomData(self::CUSTOM_DATA_FIELD_ROOT_PARENT_ID, $id);
	}
	
	/* (non-PHPdoc)
	 * @see BaseCuePoint::preInsert()
	 */
	public function preInsert(PropelPDO $con = null)
	{
		$this->setDepth($this->getDepth());
		if($this->getParentId())
			$this->setRootParentId($this->getRootParentId());
		$this->putInCustomData(self::CUSTOM_DATA_FIELD_CHILDREN_COUNT, 0);
		$this->putInCustomData(self::CUSTOM_DATA_FIELD_DIRECT_CHILDREN_COUNT, 0);
	}
} // CuePoint

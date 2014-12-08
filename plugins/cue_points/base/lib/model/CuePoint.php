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
	const CUSTOM_DATA_FIELD_ROOT_PARENT_ID = 'rootParentId';
	const CUSTOM_DATA_FIELD_TRIGGERED_AT = 'triggeredAt';
	
	public function getIndexObjectName() {
		return "CuePointIndex";
	}
	
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
			
		$kuser = kuserPeer::getKuserByPartnerAndUid($this->getPartnerId(), $puserId, true);
		if(!$kuser)
			$kuser = kuserPeer::createKuserForPartner($this->getPartnerId(), $puserId);
			
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
			$existingObject = CuePointPeer::retrieveByPKNoFilter($id);
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
			/* @var $child CuePoint */
			if ($child->isDescendant($cuePointId))
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

	/**
	 * @return int
	 */
	public function getIndexedId()
	{
		return sprintf('%u', crc32($this->getId()));
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
	public function getTriggeredAt()		{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_TRIGGERED_AT);}	

	public function setForceStop($v)	{return $this->putInCustomData(self::CUSTOM_DATA_FIELD_FORCE_STOP, (bool)$v);}
	public function setTriggeredAt($v)	{return $this->putInCustomData(self::CUSTOM_DATA_FIELD_TRIGGERED_AT, (int)$v);}
	
	public function getCacheInvalidationKeys()
	{
		return array("cuePoint:id=".strtolower($this->getId()), "cuePoint:entryId=".strtolower($this->getEntryId()));
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
		$ret = parent::getDepth();
		if(!is_null($ret))
			return $ret;
			
		if(!$this->getParentId())
			return 0;
			
		return $this->getParent()->getDepth() + 1;
	}
	
	protected function increaseChildrenCountAndSave($direct = true)
	{
		if($direct)
			$this->setDirectChildrenCount($this->getDirectChildrenCount() + 1);
			
		$this->setChildrenCount($this->getChildrenCount() + 1);
		$this->save();
		
		$parent = $this->getParent();
		if($parent)
			$parent->increaseChildrenCountAndSave(false);
	}
	
	protected function decreaseChildrenCountAndSave($direct = true)
	{
		if($direct)
			$this->setDirectChildrenCount($this->getDirectChildrenCount() - 1);
			
		$this->setChildrenCount($this->getChildrenCount() - 1);
		$this->save();
		
		$parent = $this->getParent();
		if($parent)
			$parent->decreaseChildrenCountAndSave(false);
	}
	
	/**
	 * @return int
	 */
	public function getDirectChildrenCount()
	{			
		if ($this->isNew())
			return 0;
			
		$ret = parent::getDirectChildrenCount();
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
			
		$ret = parent::getChildrenCount();
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
	 * @param int $v
	 */
	protected function setRootParentId($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_FIELD_ROOT_PARENT_ID, $v);
	}
	
	/* (non-PHPdoc)
	 * @see BaseCuePoint::preInsert()
	 */
	public function preInsert(PropelPDO $con = null)
	{
		$this->setDepth($this->getDepth());
		if($this->getParentId())
			$this->setRootParentId($this->getRootParentId());
		$this->setChildrenCount(0);
		$this->setDirectChildrenCount(0);
		
		return parent::preInsert($con);
	}

	/**
	 * @param entry $entry
	 * @param PropelPDO $con
	 * @return mixed The copied cuepoint
	 */
	public function copyToEntry( $entry, PropelPDO $con = null)
	{
		$cuePointCopy = $this->copy();
		$cuePointCopy->setEntryId($entry->getId());
		$cuePointCopy->save($con);
		return $cuePointCopy;
	}
} // CuePoint

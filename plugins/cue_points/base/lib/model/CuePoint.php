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
class CuePoint extends BaseCuePoint {

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
	    if (! $kuser) {
					throw new KalturaAPIException ( KalturaErrors::INVALID_USER_ID );
		}
		return $kuser->getPuserId();
	} 
	
	public function setPuserId($v)
	{
		$kuser = kuserPeer::getKuserByPartnerAndUid($this->getPartnerId(), $v);
	    if (! $kuser) {
					throw new KalturaAPIException ( KalturaErrors::INVALID_USER_ID );
		}
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
			
		parent::save($con);
	}
	
	public function postUpdate(PropelPDO $con = null)
	{
		if ($this->alreadyInSave)
			return parent::postUpdate($con);
		
		$objectDeleted = false;
		if($this->isColumnModified(CuePointPeer::STATUS) && $this->getStatus() == CuePointStatus::DELETED)
			$objectDeleted = true;
			
		$ret = parent::postUpdate($con);
		if($objectDeleted)
			kEventsManager::raiseEvent(new kObjectDeletedEvent($this));
			
		return $ret;
	}
	
} // CuePoint

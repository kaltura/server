<?php
/**
 * @package plugins.cuePoint
 * @subpackage events
 */
class kCuePointManager implements kObjectDeletedEventConsumer
{
	/* (non-PHPdoc)
	 * @see kObjectDeletedEventConsumer::shouldConsumeDeletedEvent()
	 */
	public function shouldConsumeDeletedEvent(BaseObject $object)
	{
		if($object instanceof entry)
			return true;
			
		if($object instanceof CuePoint)
			return true;
			
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectDeletedEventConsumer::objectDeleted()
	 */
	public function objectDeleted(BaseObject $object, BatchJob $raisedJob = null) 
	{
		if($object instanceof entry)
			$this->entryDeleted($object->getId());
					
		if($object instanceof CuePoint)
			$this->cuePointDeleted($object);
			
		return true;
	}
	
	/**
	 * @param CuePoint $cuePoint
	 */
	protected function cuePointDeleted(CuePoint $cuePoint) 
	{
		$c = new Criteria();
		$c->add(CuePointPeer::PARENT_ID, $cuePoint->getId());
			
		CuePointPeer::setUseCriteriaFilter(false);
		$cuePoints = CuePointPeer::doSelect($c);

		$update = new Criteria();
		$update->add(CuePointPeer::STATUS, CuePointStatus::DELETED);
			
		$con = Propel::getConnection(myDbHelper::DB_HELPER_CONN_MASTER);
		BasePeer::doUpdate($c, $update, $con);

		foreach($cuePoints as $cuePoint)
			kEventsManager::raiseEvent(new kObjectDeletedEvent($cuePoint));
	}
	
	/**
	 * @param int $entryId
	 */
	protected function entryDeleted($entryId) 
	{
		$c = new Criteria();
		$c->add(CuePointPeer::ENTRY_ID, $entryId);
			
		CuePointPeer::setUseCriteriaFilter(false);
		$cuePoints = CuePointPeer::doSelect($c);

		$update = new Criteria();
		$update->add(CuePointPeer::STATUS, CuePointStatus::DELETED);
			
		$con = Propel::getConnection(myDbHelper::DB_HELPER_CONN_MASTER);
		BasePeer::doUpdate($c, $update, $con);

		foreach($cuePoints as $cuePoint)
			kEventsManager::raiseEvent(new kObjectDeletedEvent($cuePoint));
	}

}
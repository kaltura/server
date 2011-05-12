<?php
class kAnnotationManager implements kObjectDeletedEventConsumer
{
	/* (non-PHPdoc)
	 * @see kObjectDeletedEventConsumer::objectDeleted()
	 */
	public function objectDeleted(BaseObject $object, BatchJob $raisedJob = null) 
	{
		KalturaLog::debug("annotation objectDeleted");
		if($object instanceof entry)
			$this->entryDeleted($object->getId());
					
		if($object instanceof Annotation)
			$this->annotationDeleted($object);
			
		return true;
	}
	
	/**
	 * @param Annotation $annotation
	 */
	protected function annotationDeleted(Annotation $annotation) 
	{
		$children = $annotation->getChildren();
		foreach($children as $child)
		{
			$child->setStatus($annotation->getStatus());
			$child->save();
		}
	}
	
	/**
	 * @param int $entryId
	 */
	protected function entryDeleted($entryId) 
	{
		$c = new Criteria();
		$c->add(AnnotationPeer::ENTRY_ID, $entryId);
		$c->add(AnnotationPeer::STATUS, AnnotationStatus::ANNOTATION_STATUS_DELETED, Criteria::NOT_EQUAL);
			
		AnnotationPeer::setUseCriteriaFilter(false);
		$annotations = AnnotationPeer::doSelect($c);
		foreach($annotations as $annotation)
		{
			kEventsManager::raiseEvent(new kObjectDeletedEvent($annotation));
		}

		$update = new Criteria();
		$update->add(AnnotationPeer::STATUS, AnnotationStatus::ANNOTATION_STATUS_DELETED);
			
		$con = Propel::getConnection(AnnotationPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		BasePeer::doUpdate($c, $update, $con);

	}

}
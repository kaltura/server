<?php
/**
 * @package plugins.metadata
 * @subpackage lib
 */
class kMetadataObjectDeletedHandler extends kObjectDeleteHandler implements kObjectChangedEventConsumer
{
	/* (non-PHPdoc)
	 * @see kObjectDeletedEventConsumer::shouldConsumeDeletedEvent()
	 */
	public function shouldConsumeDeletedEvent(BaseObject $object)
	{
		if($object instanceof entry)
			return true;
	
		if($object instanceof kuser)
			return true;
	
		if($object instanceof category)
			return true;
	
		if($object instanceof Partner)
			return true;
			
		if($object instanceof Metadata)
			return true;
			
		if($object instanceof MetadataProfile)
			return true;
			
		return parent::shouldConsumeDeletedEvent($object);
	}
	
	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::shouldConsumeChangedEvent()
	 */
	public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns)
	{
		if($object instanceof Metadata && in_array(MetadataPeer::STATUS, $modifiedColumns) && $object->getStatus() == Metadata::STATUS_INVALID)
			return true;
		
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::objectChanged()
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns)
	{
		$this->syncableDeleted($object->getId(), FileSyncObjectType::METADATA);
	}
	
	/* (non-PHPdoc)
	 * @see kObjectDeleteHandler::objectDeleted()
	 */
	public function objectDeleted(BaseObject $object, BatchJob $raisedJob = null) 
	{
		if($object instanceof entry)
			$this->deleteMetadataObjects(MetadataObjectType::ENTRY, $object->getId());
	
		if($object instanceof kuser)
			$this->deleteMetadataObjects(MetadataObjectType::USER, $object->getId());
	
		if($object instanceof category)
			$this->deleteMetadataObjects(MetadataObjectType::CATEGORY, $object->getId());
	
		if($object instanceof Partner)
			$this->deleteMetadataObjects(MetadataObjectType::PARTNER, $object->getId());
			
		if($object instanceof Metadata)
			$this->metadataDeleted($object);
			
		if($object instanceof MetadataProfile)
			$this->metadataProfileDeleted($object);
	}
	
	/**
	 * @param Metadata $metadata
	 */
	protected function metadataDeleted(Metadata $metadata) 
	{
		$this->syncableDeleted($metadata->getId(), FileSyncObjectType::METADATA);
		
		// updated in the indexing server (sphinx)
		$object = kMetadataManager::getObjectFromPeer($metadata);
		if($object && $object instanceof IIndexable)
			kEventsManager::raiseEvent(new kObjectUpdatedEvent($object));
	}
	
	/**
	 * @param MetadataProfile $metadataProfile
	 */
	protected function metadataProfileDeleted(MetadataProfile $metadataProfile) 
	{
		$this->syncableDeleted($metadataProfile->getId(), FileSyncObjectType::METADATA_PROFILE);
	}
	
	/**
	 * @param int $objectType
	 * @param string $objectId
	 */
	protected function deleteMetadataObjects($objectType, $objectId) 
	{
		$c = new Criteria();
		$c->add(MetadataPeer::OBJECT_TYPE, $objectType);
		$c->add(MetadataPeer::OBJECT_ID, $objectId);
		$c->add(MetadataPeer::STATUS, Metadata::STATUS_DELETED, Criteria::NOT_EQUAL);
	
		$peer = null;
		MetadataPeer::setUseCriteriaFilter(false);
		$metadatas = MetadataPeer::doSelect($c);
		foreach($metadatas as $metadata)
			kEventsManager::raiseEvent(new kObjectDeletedEvent($metadata));
		
		$update = new Criteria();
		$update->add(MetadataPeer::STATUS, Metadata::STATUS_DELETED);
			
		$con = Propel::getConnection(MetadataPeer::DATABASE_NAME);
		BasePeer::doUpdate($c, $update, $con);
	}
}
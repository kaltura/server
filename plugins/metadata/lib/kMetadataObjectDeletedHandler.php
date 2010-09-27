<?php
class kMetadataObjectDeletedHandler implements kObjectDeletedEventConsumer
{
	/**
	 * @param BaseObject $object
	 */
	public function objectDeleted(BaseObject $object) 
	{
		if($object instanceof entry)
			$this->deleteMetadataObjects(Metadata::TYPE_ENTRY, $object->getId());
	}
	
	protected function deleteMetadataObjects($objectType, $objectId) 
	{
		$c = new Criteria();
		$c->add(MetadataPeer::OBJECT_TYPE, $objectType);
		$c->add(MetadataPeer::OBJECT_ID, $objectId);
		$c->add(MetadataPeer::STATUS, KalturaMetadataStatus::DELETED, Criteria::NOT_EQUAL);
		
		$update = new Criteria();
		$update->add(MetadataPeer::STATUS, KalturaMetadataStatus::DELETED);
			
		$con = Propel::getConnection(MetadataPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		$count = BasePeer::doUpdate($c, $update, $con);
		
		$peer = null;
		MetadataPeer::setUseCriteriaFilter(false);
		$metadatas = MetadataPeer::doSelect($c);
		foreach($metadatas as $metadata)
		{
			kEventsManager::raiseEvent(new kObjectDeletedEvent($metadata));
			
			if(!$peer)
				$peer = kMetadataManager::getObjectPeer($metadata->getObjectType());
				
			$peer->saveToSphinx($metadata->getObjectId(), array());
		}
	}
}
<?php
/**
 * @package plugins.metadata
 * @subpackage model
 */
class MetadataEntryPeer extends entryPeer implements IMetadataPeer
{
    public static function validateMetadataObjects($profileField, $objectIds, &$errorMessage)
    {
    	//commenting out until a larger solution will be applied.
    	//Need to allow eache filed should validate configuration flag to disable or enable the execution of the metadata validation method execution
        /** @var MetadataProfileField $profileField */
    	/*
        $dbObjects = entryPeer::retrieveByPKs($objectIds);
    
        if(count($dbObjects) != count($objectIds))
        {
            $errorMessage = 'One of the following objects: '.implode(', ', $objectIds).' was not found';
            return false;
        }
        */
    
        return true;
    }

    public static function getEntry($objectId)
    {
        return self::retrieveByPK($objectId);
    }
    
    public static function validateMetadataObjectAccess($objectId, $objectType)
    {
    	$objectPeer = kMetadataManager::getObjectPeer($objectType);
    	if (!$objectPeer)
    	{
    		KalturaLog::debug("objectPeer not found for object object type [$objectType]");
    		return false;
    	}
    	 
    	$entryDb = $objectPeer::retrieveByPK($objectId);
    	if(!$entryDb)
    	{
    		KalturaLog::debug("Metadata object id with id [$objectId] not found");
    		return false;
    	}
    	
    	// check if all ids are privileged
    	if (kCurrentContext::$ks_object->hasPrivilege(ks::PRIVILEGE_WILDCARD) ||
    			kkCurrentContext::$ks_object->verifyPrivileges(kSessionBase::PRIVILEGE_EDIT, $objectId))
    	{
    		return true;
    	}
    	
    	/* @var $entryDb entry */
    	if(strtolower($entryDb->getPuserId()) != strtolower(kCurrentContext::$ks_uid) &&
    			!$entryDb->isEntitledKuserEdit(kCurrentContext::getCurrentKsKuserId())
    	)
    	{
    		return false;
    	}
    	 
    	return true;
    }
}

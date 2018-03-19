<?php
/**
 * @package plugins.metadata
 * @subpackage model
 */
class MetadataKuserPeer extends kuserPeer implements IMetadataPeer
{
    public static function validateMetadataObjects($profileField, $objectIds, &$errorMessage)
    {
        /** @var MetadataProfileField $profileField */
        
        $partnerId = kCurrentContext::getCurrentPartnerId();
        $dbObjects = kuserPeer::getKuserByPartnerAndUids($partnerId, $objectIds);
        
        if(count($dbObjects) != count($objectIds))
        {
            $errorMessage = 'One of the following objects: '.implode(', ', $objectIds).' was not found';
            return false;
        }
        
        return true;
    }

    public static function getEntry($objectId)
    {
        return null;
    }
    
    public static function validateMetadataObjectAccess($objectId, $objectType)
    {
    	$objectPeer = kMetadataManager::getObjectPeer($objectType);
    	if (!$objectPeer)
    	{
    		KalturaLog::debug("objectPeer not found for object object type [$objectType]");
    		return false;
    	}
    
    	$kuser = $objectPeer::retrieveByPK($objectId);
    	if(!$kuser)
    	{
    		KalturaLog::debug("Metadata object id with id [$objectId] not found");
    		return false;
    	}
    
    	return true;
    }
}
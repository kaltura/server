<?php
/**
 * @package plugins.metadata
 * @subpackage model
 */
class MetadataPartnerPeer extends PartnerPeer implements IMetadataPeer
{
    public static function validateMetadataObjects($profileField, $objectIds, &$errorMessage)
    {
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
    
    	$partner = $objectPeer::retrieveByPK($objectId);
    	if(!$partner)
    	{
    		KalturaLog::debug("Metadata object id with id [$objectId] not found");
    		return false;
    	}
    
    	return true;
    }
}
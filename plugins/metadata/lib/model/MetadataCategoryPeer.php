<?php
/**
 * @package plugins.metadata
 * @subpackage model
 */
class MetadataCategoryPeer extends categoryPeer implements IMetadataPeer
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
    	
    	$categoryDb = $objectPeer::retrieveByPK($objectId);
    	if(!$categoryDb)
    	{
    		KalturaLog::debug("Metadata object id with id [$objectId] not found");
    		return false;
    	}
    	
    	if (kEntitlementUtils::getEntitlementEnforcement())
    	{
    		$currentKuserCategoryKuser = categoryKuserPeer::retrievePermittedKuserInCategory($categoryDb->getId(), kCurrentContext::getCurrentKsKuserId(), array(PermissionName::CATEGORY_EDIT));
    		if(!$currentKuserCategoryKuser || $currentKuserCategoryKuser->getPermissionLevel() != CategoryKuserPermissionLevel::MANAGER)
    		{
    			KalturaLog::debug("Current user is not permitted to access categoru with id [$objectId]");
    			return false;
    		}
    	}
    	
    	return true;
    }
}
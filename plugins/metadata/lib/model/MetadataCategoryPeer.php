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
    
    public static function validateMetadataObjectAccess($objectId)
    {
    	$categoryDb = self::retrieveByPK($objectId);
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
    			KalturaLog::debug("Current user is not permitted to access category with id [$objectId]");
    			return false;
    		}
    	}
    	
    	return true;
    }
}

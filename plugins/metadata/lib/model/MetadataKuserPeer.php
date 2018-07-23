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
    
    public static function validateMetadataObjectAccess($objectId)
    {
    	$kuser = self::retrieveByPK($objectId);
    	if(!$kuser)
    	{
    		KalturaLog::debug("Metadata object id with id [$objectId] not found");
    		return false;
    	}
    
    	return true;
    }
}
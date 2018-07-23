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
    
    public static function validateMetadataObjectAccess($objectId)
    {
    	$partner = self::retrieveByPK($objectId);
    	if(!$partner)
    	{
    		KalturaLog::debug("Metadata object id with id [$objectId] not found");
    		return false;
    	}
    
    	return true;
    }
}
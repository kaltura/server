<?php
/**
 * @package plugins.metadata
 * @subpackage model
 */
class MetadataDynamicObjectPeer extends MetadataPeer implements IMetadataPeer
{
    public static function validateMetadataObjects($profileField, $objectIds, &$errorMessage)
    {
        /** @var MetadataProfileField $profileField */
	    $subMetadataProfileId = $profileField->getRelatedMetadataProfileId();
        $subMetadataProfile = MetadataProfilePeer::retrieveByPK($subMetadataProfileId);
		if (!$subMetadataProfile)
		{
		    $errorMessage = 'Sub metadata profile ' . $subMetadataProfileId . ' was not found';
            return false;
		}
		
		$subMetadataObjects = MetadataPeer::retrieveByObjects($subMetadataProfileId, $subMetadataProfile->getObjectType(), $objectIds);
		if (count($subMetadataObjects) != count($objectIds))
		{
		    $errorMessage = 'One of the following objects: '.implode(', ', $objectIds).' was not found for profile '.$subMetadataProfileId;
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
		return kCurrentContext::$is_admin_session;
	}
}

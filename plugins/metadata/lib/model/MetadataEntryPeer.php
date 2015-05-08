<?php
/**
 * @package plugins.metadata
 * @subpackage model
 */
class MetadataEntryPeer extends entryPeer implements IMetadataPeer
{
    public static function validateMetadataObjects($profileField, $objectIds, &$errorMessage)
    {
        /** @var MetadataProfileField $profileField */
        $dbObjects = entryPeer::retrieveByPKs($objectIds);
    
        if(count($dbObjects) != count($objectIds))
        {
            $errorMessage = 'One of the following objects: '.implode(', ', $objectIds).' was not found';
            return false;
        }
    
        return true;
    }
}

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
}
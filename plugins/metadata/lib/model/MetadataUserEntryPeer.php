<?php
/**
 * @package plugins.metadata
 * @subpackage model
 */
class MetadataUserEntryPeer extends UserEntryPeer implements IMetadataPeer
{
	public static function validateMetadataObjects($profileField, $objectIds, &$errorMessage)
	{
		/** @var MetadataProfileField $profileField */
		$dbObjects = self::retrieveByPKs($objectIds);
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
		$userEntry  = self::retrieveByPK($objectId);
		if(!$userEntry)
		{
			KalturaLog::debug("Metadata object id with id [$objectId] not found");
			return false;
		}

		return true;
	}
}
<?php

class kFileSyncObjectManager
{
	/**
	 * 
	 * @param int $objectType
	 * @param string $objectId
	 * @return ISyncableFile
	 */
	public static function retrieveObject($objectType, $objectId)
	{
		$object = null;
		
		switch ( $objectType )
		{
			case FileSync::FILE_SYNC_OBJECT_TYPE_ENTRY:
				entryPeer::setUseCriteriaFilter ( false );
				$object = entryPeer::retrieveByPK( $objectId );
				entryPeer::setUseCriteriaFilter ( true );
				break;
			case FileSync::FILE_SYNC_OBJECT_TYPE_UICONF:
				uiConfPeer::setUseCriteriaFilter ( false );
				$object = uiConfPeer::retrieveByPK( $objectId );
				uiConfPeer::setUseCriteriaFilter ( true );
				break;
			case FileSync::FILE_SYNC_OBJECT_TYPE_BATCHJOB:
				BatchJobPeer::setUseCriteriaFilter ( false );
				$object = BatchJobPeer::retrieveByPK( $objectId );
				BatchJobPeer::setUseCriteriaFilter ( true );
				break;
			case FileSync::FILE_SYNC_OBJECT_TYPE_FLAVOR_ASSET:
				flavorAssetPeer::setUseCriteriaFilter ( false );
				$object = flavorAssetPeer::retrieveById( $objectId );
				flavorAssetPeer::setUseCriteriaFilter ( true );
				break;
		}
		
		if ( $object == null )
			$object = KalturaPluginManager::loadObject(KalturaPluginManager::OBJECT_TYPE_SYNCABLE, $objectType, array('objectId' => $objectId));
		
		if ( $object == null )
		{
			$error = __METHOD__. " Cannot find object type [" . $objectType . "] with object_id [" . $objectId . "]";
			KalturaLog::err($error);
			throw new Exception ( $error );
		}
		return $object;
	}
	
}
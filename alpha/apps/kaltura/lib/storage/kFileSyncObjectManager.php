<?php
/**
 * @package Core
 * @subpackage storage
 */
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
			case FileSyncObjectType::ENTRY:
				entryPeer::setUseCriteriaFilter ( false );
				$object = entryPeer::retrieveByPK( $objectId );
				entryPeer::setUseCriteriaFilter ( true );
				break;
			case FileSyncObjectType::UICONF:
				uiConfPeer::setUseCriteriaFilter ( false );
				$object = uiConfPeer::retrieveByPK( $objectId );
				uiConfPeer::setUseCriteriaFilter ( true );
				break;
			case FileSyncObjectType::BATCHJOB:
				BatchJobPeer::setUseCriteriaFilter ( false );
				$object = BatchJobPeer::retrieveByPK( $objectId );
				BatchJobPeer::setUseCriteriaFilter ( true );
				break;
			case FileSyncObjectType::FLAVOR_ASSET:
				assetPeer::setUseCriteriaFilter ( false );
				$object = assetPeer::retrieveById( $objectId );
				assetPeer::setUseCriteriaFilter ( true );
				break;
			case FileSyncObjectType::SYNDICATION_FEED:
				syndicationFeedPeer::setUseCriteriaFilter ( false );
				$object = syndicationFeedPeer::retrieveByPK( $objectId );
				syndicationFeedPeer::setUseCriteriaFilter ( true );
				break;
			case FileSyncObjectType::CONVERSION_PROFILE:
				conversionProfile2Peer::setUseCriteriaFilter ( false );
				$object = conversionProfile2Peer::retrieveByPK( $objectId );
				conversionProfile2Peer::setUseCriteriaFilter ( true );
				break;
			case FileSyncObjectType::FILE_ASSET:
				conversionProfile2Peer::setUseCriteriaFilter ( false );
				$object = FileAssetPeer::retrieveByPK( $objectId );
				conversionProfile2Peer::setUseCriteriaFilter ( true );
				break;
		}
		
		if ( $object == null )
			$object = KalturaPluginManager::loadObject('ISyncableFile', $objectType, array('objectId' => $objectId));
		
		if ( $object == null )
		{
			$error = __METHOD__. " Cannot find object type [" . $objectType . "] with object_id [" . $objectId . "]";
			KalturaLog::err($error);
			throw new kFileSyncException($error);
		}
		return $object;
	}
	
}
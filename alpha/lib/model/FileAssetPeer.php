<?php


/**
 * Skeleton subclass for performing query and update operations on the 'file_asset' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package Core
 * @subpackage model
 */
class FileAssetPeer extends BaseFileAssetPeer {

	/**
	 * @param int $objectType
	 * @param string $objectId
	 * @param PropelPDO $con
	 * @return array<FileAsset>
	 */
	public static function retrieveByObject($objectType, $objectId, PropelPDO $con = null)
	{
		$criteria = new Criteria();
		$criteria->add(FileAssetPeer::OBJECT_TYPE, $objectType);
		$criteria->add(FileAssetPeer::OBJECT_ID, $objectId);

		return FileAssetPeer::doSelect($criteria, $con);
	}
	
} // FileAssetPeer

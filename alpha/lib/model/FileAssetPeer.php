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
class FileAssetPeer extends BaseFileAssetPeer implements IRelatedObjectPeer
{
	/**
	 * @param int $objectType
	 * @param string $objectId
	 * @param PropelPDO $con
	 * @param boolean $removeStatusDeleted
	 * @return array<FileAsset>
	 */
	public static function retrieveByObject($objectType, $objectId, PropelPDO $con = null, $removeStatusDeleted = null)
	{
		$criteria = new Criteria();
		$criteria->add(FileAssetPeer::OBJECT_TYPE, $objectType);
		$criteria->add(FileAssetPeer::OBJECT_ID, $objectId);
		if ($removeStatusDeleted)
		{
			$criteria->add(FileAssetPeer::STATUS, FileAssetStatus::DELETED, Criteria::NOT_EQUAL);
		}

		return FileAssetPeer::doSelect($criteria, $con);
	}

	/* (non-PHPdoc)
	 * @see IRelatedObjectPeer::getRootObjects()
	 */
	public function getRootObjects(IRelatedObject $object)
	{
		/* @var $object FileAsset */
		
		$rootObjects = array();
		$parentObject = null;
		switch ($object->getObjectType())
		{
			case FileAssetObjectType::UI_CONF:
				$parentObject = uiConfPeer::retrieveByPK($object->getObjectId());
				break;
		}
		
		if($parentObject)
		{
			/* @var $parentObject IRelatedObjectPeer */
			$peer = $parentObject->getPeer();
			if($peer instanceof IRelatedObjectPeer)
			{
				$parentRoots = $peer->getRootObjects($parentObject);
				if(count($parentRoots))
				{
					$rootObjects = array_merge($rootObjects, $parentRoots);
				}
			}
			$rootObjects[] = $parentObject;
		}
		
		return $rootObjects;
	}

	/* (non-PHPdoc)
	 * @see IRelatedObjectPeer::isReferenced()
	 */
	public function isReferenced(IRelatedObject $object)
	{
		return false;
	}
	
} // FileAssetPeer

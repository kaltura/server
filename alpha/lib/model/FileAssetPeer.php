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
	 * @return array<FileAsset>
	 */
	public static function retrieveByObject($objectType, $objectId, PropelPDO $con = null)
	{
		$criteria = new Criteria();
		$criteria->add(FileAssetPeer::OBJECT_TYPE, $objectType);
		$criteria->add(FileAssetPeer::OBJECT_ID, $objectId);

		return FileAssetPeer::doSelect($criteria, $con);
	}

	public function getFileAssetParentObjects(FileAsset $object)
	{
		switch($object->getObjectType())
		{
			case FileAssetObjectType::UI_CONF:
				return array(uiConfPeer::retrieveByPK($object->getObjectId()));
		}
		
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see IRelatedObjectPeer::getParentObjects()
	 */
	public function getParentObjects(IBaseObject $object)
	{
		return $this->getFileAssetParentObjects($object);
	}

	/* (non-PHPdoc)
	 * @see IRelatedObjectPeer::getRootObjects()
	 */
	public function getRootObjects(IBaseObject $object)
	{
		$parentObjects = $this->getParentObjects($object);
		$rootObjects = array();
		foreach($parentObjects as $parentObject)
		{
			/* @var $parentObject IBaseObject */
			$peer = $parentObject->getPeer();
			$rootAdded = false;
			if($peer instanceof IRelatedObjectPeer)
			{
				$parentRoots = $peer->getRootObjects($parentObject);
				if(count($parentRoots))
				{
					$rootObjects = array_merge($rootObjects, $parentRoots);
					$rootAdded = true;
				}
			}
			if($rootAdded)
				$rootObjects[] = $parentObject;
		}
		
		return $rootObjects;
	}

	/* (non-PHPdoc)
	 * @see IRelatedObjectPeer::isReferenced()
	 */
	public function isReferenced(IBaseObject $object)
	{
		return false;
	}
	
} // FileAssetPeer

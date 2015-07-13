<?php


/**
 * Skeleton subclass for performing query and update operations on the 'metadata' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.metadata
 * @subpackage model
 */
class MetadataPeer extends BaseMetadataPeer implements IRelatedObjectPeer
{
	public static function setDefaultCriteriaFilter()
	{
		if(self::$s_criteria_filter == null)
			self::$s_criteria_filter = new criteriaFilter();
		
		$c = new Criteria();
		$c->addAnd(MetadataPeer::STATUS, Metadata::STATUS_VALID);
		self::$s_criteria_filter->setFilter($c);
	}

	/* (non-PHPdoc)
	 * @see BaseCuePointPeer::doSelect()
	 */
	public static function doSelect(Criteria $criteria, PropelPDO $con = null)
	{
		$c = clone $criteria;

		if($c instanceof KalturaCriteria)
		{
			$c->applyFilters();
			$criteria->setRecordsCount($c->getRecordsCount());
		}

		return parent::doSelect($c, $con);
	}

	/**
	 * Retrieve a single metadata object by object id and type.
	 *
	 * @param      int $metadataProfileId
	 * @param      int $objectType
	 * @param      string $objectId
	 * @param      PropelPDO $con the connection to use
	 * @return     Metadata
	 */
	public static function retrieveByObject($metadataProfileId, $objectType, $objectId, PropelPDO $con = null)
	{
		$metadataProfile = MetadataProfilePeer::retrieveByPK($metadataProfileId, $con);
		if(!$metadataProfile)
			return null;
		
		$criteria = new Criteria();
		$criteria->add(MetadataPeer::METADATA_PROFILE_ID, $metadataProfileId);
		$criteria->add(MetadataPeer::OBJECT_TYPE, $objectType);
		$criteria->add(MetadataPeer::OBJECT_ID, $objectId);

		return MetadataPeer::doSelectOne($criteria, $con);
	}

	/**
	 * Retrieve metadata objects by object ids and type.
	 *
	 * @param      int $metadataProfileId
	 * @param      int $objectType
	 * @param      string $objectId
	 * @param      PropelPDO $con the connection to use
	 * @return     Metadata
	 */
	public static function retrieveByObjects($metadataProfileId, $objectType, array $objectIds, PropelPDO $con = null)
	{
		$metadataProfile = MetadataProfilePeer::retrieveByPK($metadataProfileId, $con);
		if(!$metadataProfile)
			return null;

		$criteria = new Criteria();
		$criteria->add(MetadataPeer::METADATA_PROFILE_ID, $metadataProfileId);
		$criteria->add(MetadataPeer::OBJECT_TYPE, $objectType);
		$criteria->add(MetadataPeer::OBJECT_ID, $objectIds, Criteria::IN);

		return MetadataPeer::doSelect($criteria, $con);
	}
	
	/**
	 * Retrieve all metadta object by profile.
	 *
	 * @param      int $metadataProfileId
	 * @param      int $metadataProfileVersion
	 * @param      PropelPDO $con the connection to use
	 * @return     array<Metadata>
	 */
	public static function retrieveByProfile($metadataProfileId, $metadataProfileVersion = null, PropelPDO $con = null)
	{
		if(is_null($metadataProfileVersion))
		{
			$metadataProfile = MetadataProfilePeer::retrieveByPK($metadataProfileId, $con);
			if(!$metadataProfile)
				return null;
				
			$metadataProfileVersion = $metadataProfile->getVersion();
		}
		
		$criteria = new Criteria();
		$criteria->add(MetadataPeer::METADATA_PROFILE_ID, $metadataProfileId);
		$criteria->add(MetadataPeer::METADATA_PROFILE_VERSION, $metadataProfileVersion);

		return MetadataPeer::doSelect($criteria, $con);
	}
	
	/**
	 * Retrieve a single metadta object by object id and type.
	 *
	 * @param      int $objectType
	 * @param      string $objectId
	 * @param      PropelPDO $con the connection to use
	 * @return     array<Metadata>
	 */
	public static function retrieveAllByObject($objectType, $objectId, PropelPDO $con = null)
	{
		$criteria = new Criteria();
		$criteria->add(MetadataPeer::OBJECT_TYPE, $objectType);
		$criteria->add(MetadataPeer::OBJECT_ID, $objectId);

		return MetadataPeer::doSelect($criteria, $con);
	}
	
	/**
	 * Retrieve all metadta objects of object ids and type.
	 *
	 * @param      int $objectType
	 * @param      array $objectIds
	 * @param      PropelPDO $con the connection to use
	 * @return     array<Metadata>
	 */
	public static function retrieveAllByObjectIds($objectType, array $objectIds, PropelPDO $con = null)
	{
		$criteria = new Criteria();
		$criteria->add(MetadataPeer::OBJECT_TYPE, $objectType);
		$criteria->add(MetadataPeer::OBJECT_ID, $objectIds, Criteria::IN);

		return MetadataPeer::doSelect($criteria, $con);
	}
	
	public static function getCacheInvalidationKeys()
	{
		return array(array("metadata:objectId=%s", self::OBJECT_ID));		
	}

	/* (non-PHPdoc)
	 * @see IRelatedObjectPeer::getRootObjects()
	 */
	public function getRootObjects(IBaseObject $object)
	{
		$parentObject = kMetadataManager::getObjectFromPeer($object);
		if($parentObject)
			return array($parentObject);

		return array();
	}

	/* (non-PHPdoc)
	 * @see IRelatedObjectPeer::isReferenced()
	 */
	public function isReferenced(IBaseObject $object)
	{
		/* @var $object Metadata */
		if($object->getObjectType() == MetadataObjectType::DYNAMIC_OBJECT)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
} // MetadataPeer

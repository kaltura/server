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
 * @package    lib.model
 */
class MetadataPeer extends BaseMetadataPeer {


	public static function setDefaultCriteriaFilter()
	{
		if(self::$s_criteria_filter == null)
			self::$s_criteria_filter = new criteriaFilter();
		
		$c = new Criteria();
		$c->addAnd(MetadataPeer::STATUS, Metadata::STATUS_VALID);
		self::$s_criteria_filter->setFilter($c);
	}
	
	/**
	 * Retrieve a single metadta object by object id and type.
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
		$criteria->add(MetadataPeer::METADATA_PROFILE_VERSION, $metadataProfile->getVersion());
		$criteria->add(MetadataPeer::OBJECT_TYPE, $objectType);
		$criteria->add(MetadataPeer::OBJECT_ID, $objectId);

		return MetadataPeer::doSelectOne($criteria, $con);
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
	
} // MetadataPeer

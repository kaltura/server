<?php

/**
 * Skeleton subclass for performing query and update operations on the 'metadata_profile' table.
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
class MetadataProfilePeer extends BaseMetadataProfilePeer
{
	
	public static function setDefaultCriteriaFilter()
	{
		if(self::$s_criteria_filter == null)
			self::$s_criteria_filter = new criteriaFilter();
		
		$c = new Criteria();
		$c->addAnd(MetadataProfilePeer::STATUS, MetadataProfile::STATUS_DEPRECATED, Criteria::NOT_EQUAL);
		self::$s_criteria_filter->setFilter($c);
	}
	
	/**
	 * Retrieve a single object by system name (object is retrieved for the current partner).
	 *
	 * @param      int $systemName the system name
	 * @param      int $partnerId the partner Id 
	 * @param      PropelPDO $con the connection to use
	 * @return     MetadataProfile
	 */
	public static function retrieveBySystemName($systemName, $partnerIds = null, PropelPDO $con = null)
	{
		$criteria = new Criteria();
		$criteria->add(MetadataProfilePeer::SYSTEM_NAME, $systemName);
		if($partnerIds)
		{
			$partnerIds = !is_array($partnerIds) ? array($partnerIds) : $partnerIds;
			$partnerIds = array_map ('intval', $partnerIds);
			$criteria->add(MetadataProfilePeer::PARTNER_ID, $partnerIds, Criteria::IN);
		
		}
		return MetadataProfilePeer::doSelectOne($criteria, $con);
	}
	
	/**
	 * @param      int $partnerId the partner id
	 * @param      PropelPDO $con the connection to use
	 * @return     array<MetadataProfile>
	 */
	public static function retrieveByPartnerId($partnerId, PropelPDO $con = null)
	{
		$criteria = new Criteria();
		$criteria->add(MetadataProfilePeer::PARTNER_ID, $partnerId);
		
		self::setUseCriteriaFilter(false);
		$ret = MetadataProfilePeer::doSelectOne($criteria, $con);
		self::setUseCriteriaFilter(true);
		
		return $ret;
	}
	public static function getCacheInvalidationKeys()
	{
		return array(array("metadataProfile:id=%s", self::ID), array("metadataProfile:partnerId=%s", self::PARTNER_ID));		
	}
	public static function retrieveAllActiveByPartnerId($partnerId, $object_type ,PropelPDO $con = null)
	{
		$criteria = new Criteria();
		$criteria->add(MetadataProfilePeer::PARTNER_ID, $partnerId);
		if ($object_type)
			$criteria->add(MetadataProfilePeer::OBJECT_TYPE, $object_type);

		$profiles = MetadataProfilePeer::doSelect($criteria, $con);

		return $profiles;
	}
} // MetadataProfilePeer

<?php


/**
 * Skeleton subclass for performing query and update operations on the 'metadata_profile_field' table.
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
class MetadataProfileFieldPeer extends BaseMetadataProfileFieldPeer {


	/**
	 * @param      int $metadataProfileId
	 * @param      PropelPDO $con the connection to use
	 * @return     array<MetadataProfileField>
	 */
	public static function retrieveByMetadataProfileId($metadataProfileId, PropelPDO $con = null)
	{
		$criteria = new Criteria();
		$criteria->add(MetadataProfileFieldPeer::METADATA_PROFILE_ID, $metadataProfileId);

		return MetadataProfileFieldPeer::doSelect($criteria, $con);
	}
	
	/**
	 * Retrieves by STATUS_ACTIVE, will not include STATUS_NONE_SEARCHABLE
	 *
	 * @param      int $metadataProfileId
	 * @param      PropelPDO $con the connection to use
	 * @return     array<MetadataProfileField>
	 */
	public static function retrieveActiveByMetadataProfileId($metadataProfileId, PropelPDO $con = null)
	{
		$criteria = new Criteria();
		$criteria->add(MetadataProfileFieldPeer::METADATA_PROFILE_ID, $metadataProfileId);
		$criteria->add(MetadataProfileFieldPeer::STATUS, MetadataProfileField::STATUS_ACTIVE);

		return MetadataProfileFieldPeer::doSelect($criteria, $con);
	}

	/**
	 * Retrieves by STATUS_ACTIVE and STATUS_NONE_SEARCHABLE
	 *
	 * @param      int $metadataProfileId
	 * @param      PropelPDO $con the connection to use
	 * @return     array<MetadataProfileField>
	 */
	public static function retrieveAllActiveByMetadataProfileId($metadataProfileId, PropelPDO $con = null)
	{
		$criteria = new Criteria();
		$criteria->add(MetadataProfileFieldPeer::METADATA_PROFILE_ID, $metadataProfileId);
		$criteria->add(MetadataProfileFieldPeer::STATUS, array(MetadataProfileField::STATUS_ACTIVE, MetadataProfileField::STATUS_NONE_SEARCHABLE), Criteria::IN);

		return MetadataProfileFieldPeer::doSelect($criteria, $con);
	}
	
	/**
	 * @param      int $metadataProfileId
	 * @param      string $key
	 * @param      PropelPDO $con the connection to use
	 * @return     MetadataProfileField
	 */
	public static function retrieveByMetadataProfileAndKey($metadataProfileId, $key, PropelPDO $con = null)
	{
		$criteria = new Criteria();
		$criteria->add(MetadataProfileFieldPeer::METADATA_PROFILE_ID, $metadataProfileId);
		$criteria->add(MetadataProfileFieldPeer::KEY, $key);

		return MetadataProfileFieldPeer::doSelectOne($criteria, $con);
	}

	/**
	 * @param      int $partnerId
	 * @param      string $key
	 * @param      PropelPDO $con the connection to use
	 * @return     array<MetadataProfileField>
	 */
	public static function retrieveByPartnerAndKey($partnerId, $key, PropelPDO $con = null)
	{
		$criteria = new Criteria();
		$criteria->add(MetadataProfileFieldPeer::PARTNER_ID, $partnerId);
		$criteria->add(MetadataProfileFieldPeer::KEY, $key);

		return MetadataProfileFieldPeer::doSelect($criteria, $con);
	}
	
	
	/**
	 * @param      int $partnerId
	 * @param      string $key
	 * @param      PropelPDO $con the connection to use
	 * @return     array<MetadataProfileField>
	 */
	public static function retrieveByPartner($partnerId, PropelPDO $con = null)
	{
		$criteria = new Criteria();
		$criteria->add(MetadataProfileFieldPeer::PARTNER_ID, $partnerId);

		return MetadataProfileFieldPeer::doSelect($criteria, $con);
	}


	/**
	 * @param $partnerId
	 * @param $relatedMetadataProfileId
	 * @param PropelPDO $con
	 * @return array<MetadataProfileField>
	 */
	public static function retrieveByPartnerAndRelatedMetadataProfileId($partnerId, $relatedMetadataProfileId, PropelPDO $con = null)
	{
		$criteria = new Criteria();
		$criteria->add(MetadataProfileFieldPeer::PARTNER_ID, $partnerId);
		$criteria->add(MetadataProfileFieldPeer::RELATED_METADATA_PROFILE_ID, $relatedMetadataProfileId);
		$criteria->add(MetadataProfileFieldPeer::STATUS, array(MetadataProfileField::STATUS_ACTIVE, MetadataProfileField::STATUS_NONE_SEARCHABLE), Criteria::IN);

		return MetadataProfileFieldPeer::doSelect($criteria, $con);
	}
	
	/**
	 * @param      int $partnerId
	 * @param      int $objectType
	 * @return     array<MetadataProfileField>
	 */
	public static function retrieveIndexableByPartnerAndType($partnerId, $objectType)
	{
		$criteria = new Criteria();
		$criteria->addSelectColumn(MetadataProfilePeer::ID);
		$criteria->add(MetadataProfilePeer::PARTNER_ID, $partnerId);
		$criteria->add(MetadataProfilePeer::OBJECT_TYPE, $objectType);

		$stmt = MetadataProfilePeer::doSelectStmt($criteria);
		$metadataProfileIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
		
		$criteria = new Criteria();
		$criteria->add(MetadataProfileFieldPeer::PARTNER_ID, $partnerId);
		$criteria->add(MetadataProfileFieldPeer::METADATA_PROFILE_ID, $metadataProfileIds, Criteria::IN);
		$criteria->add(MetadataProfileFieldPeer::STATUS, MetadataProfileField::STATUS_ACTIVE, Criteria::EQUAL);
		$criteria->add(MetadataProfileFieldPeer::SEARCH_INDEX, null, Criteria::ISNOTNULL);

		return MetadataProfileFieldPeer::doSelect($criteria);
	}
	
	/**
	 * @param      int $partnerId
	 * @param      PropelPDO $con the connection to use
	 * @return     array<string> keys
	 */
	public static function retrievePartnerKeys($partnerId, PropelPDO $con = null)
	{
		$criteria = new Criteria();
		$criteria->addSelectColumn(MetadataProfileFieldPeer::KEY);
		$criteria->add(MetadataProfileFieldPeer::PARTNER_ID, $partnerId);
		$criteria->add(MetadataProfileFieldPeer::STATUS, MetadataProfileField::STATUS_ACTIVE);

		$stmt = MetadataProfileFieldPeer::doSelectStmt($criteria, $con);
		return $stmt->fetchAll(PDO::FETCH_COLUMN);
	}
	
	public static function getCacheInvalidationKeys()
	{
		return array(array("metadataProfileField:metadataProfileId=%s", self::METADATA_PROFILE_ID));		
	}
} // MetadataProfileFieldPeer

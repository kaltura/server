<?php


/**
 * Skeleton subclass for performing query and update operations on the 'distribution_profile' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.model
 */
class DistributionProfilePeer extends BaseDistributionProfilePeer 
{
	// cache classes by their type
	protected static $class_types_cache = array(
		DistributionProviderType::GENERIC => GenericDistributionProfilePeer::OM_CLASS,
	);
	
	/**
	 * Retrieve all partner profiles.
	 *
	 * @param      int $partnerId
	 * @param      PropelPDO $con the connection to use
	 * @return     array<DistributionProfile>
	 */
	public static function retrieveByPartnerId($partnerId, PropelPDO $con = null)
	{
		$criteria = new Criteria();
		$criteria->add(DistributionProfilePeer::PARTNER_ID, $partnerId);

		return DistributionProfilePeer::doSelect($criteria, $con);
	}

	/**
	 * The returned Class will contain objects of the default type or
	 * objects that inherit from the default.
	 *
	 * @param      array $row PropelPDO result row.
	 * @param      int $colnum Column to examine for OM class information (first is 0).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getOMClass($row, $colnum)
	{
		if($row)
		{
			$assetType = $row[$colnum + 4]; // provider type column
			if(isset(self::$class_types_cache[$assetType]))
				return self::$class_types_cache[$assetType];
				
			$extendedCls = KalturaPluginManager::getObjectClass(parent::OM_CLASS, $assetType);
			if($extendedCls)
			{
				self::$class_types_cache[$assetType] = $extendedCls;
				return $extendedCls;
			}
			self::$class_types_cache[$assetType] = parent::OM_CLASS;
		}
			
		return GenericDistributionProfilePeer::OM_CLASS;
	}

	/**
	 * @return DistributionProfile
	 */
	public static function createDistributionProfile($providerType)
	{
		if($providerType == DistributionProviderType::GENERIC)
			return new GenericDistributionProfile();
			
		$distributionProfile = KalturaPluginManager::loadObject(parent::OM_CLASS, $providerType);
		if($distributionProfile)
			return $distributionProfile;
		
		return null;
	}
	
} // DistributionProfilePeer

<?php
/**
 * @package plugins.contentDistribution
 * @subpackage model
 */
class GenericDistributionProfilePeer extends DistributionProfilePeer
{
	const OM_CLASS = 'GenericDistributionProfile';

	/**
	 * Creates default criteria filter
	 */
	public static function setDefaultCriteriaFilter()
	{
		if ( self::$s_criteria_filter == null )
			self::$s_criteria_filter = new criteriaFilter ();
		
		$c = new myCriteria(); 
		$c->addAnd ( DistributionProfilePeer::PROVIDER_TYPE, DistributionProviderType::GENERIC); 
		$c->addAnd ( DistributionProfilePeer::STATUS, DistributionProfileStatus::DELETED, Criteria::NOT_EQUAL);
		self::$s_criteria_filter->setFilter ( $c );
	}
	
	/**
	 * Retrieve all profiles of the provider.
	 *
	 * @param      int $providerId
	 * @param      PropelPDO $con the connection to use
	 * @return     array<DistributionProfile>
	 */
	public static function retrieveByProviderId($providerId, PropelPDO $con = null)
	{
		$criteria = new Criteria();
		$criteria->add(GenericDistributionProfilePeer::PROVIDER_TYPE, DistributionProviderType::GENERIC);

		$distributionProfiles = DistributionProfilePeer::doSelect($criteria, $con);
		foreach($distributionProfiles as $key => $distributionProfile)
			if($distributionProfile->getGenericProviderId() != $providerId)
				unset($distributionProfiles[$key]);
				
		return $distributionProfiles;
	}
}
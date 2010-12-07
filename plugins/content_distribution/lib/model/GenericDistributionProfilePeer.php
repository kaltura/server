<?php
class GenericDistributionProfilePeer extends DistributionProfilePeer
{
	const OM_CLASS = 'GenericDistributionProfile';

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
			if($distributionProfiles->getGenericProviderId() != $providerId)
				unset($distributionProfiles[$key]);
				
		return $distributionProfiles;
	}
}
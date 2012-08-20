<?php


/**
 * Skeleton subclass for performing query and update operations on the 'generic_distribution_provider_action' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.contentDistribution
 * @subpackage model
 */
class GenericDistributionProviderActionPeer extends BaseGenericDistributionProviderActionPeer 
{
	/**
	 * Creates default criteria filter
	 */
	public static function setDefaultCriteriaFilter()
	{
		if ( self::$s_criteria_filter == null )
			self::$s_criteria_filter = new criteriaFilter ();
		
		$c = new myCriteria(); 
		$c->addAnd ( GenericDistributionProviderActionPeer::STATUS, GenericDistributionProviderStatus::DELETED, Criteria::NOT_EQUAL);
		self::$s_criteria_filter->setFilter ( $c );
	}
	
	/**
	 * @param      int $providerId
	 * @param      PropelPDO $con the connection to use
	 * @return     array<GenericDistributionProviderAction>
	 */
	public static function retrieveByProviderId($providerId, PropelPDO $con = null)
	{
		$criteria = new Criteria();
		$criteria->add(GenericDistributionProviderActionPeer::GENERIC_DISTRIBUTION_PROVIDER_ID, $providerId);

		return GenericDistributionProviderActionPeer::doSelect($criteria, $con);
	}
	
	/**
	 * @param      int $providerId
	 * @param      int $action
	 * @param      PropelPDO $con the connection to use
	 * @return     GenericDistributionProviderAction
	 */
	public static function retrieveByProviderAndAction($providerId, $action, PropelPDO $con = null)
	{
		$criteria = new Criteria();
		$criteria->add(GenericDistributionProviderActionPeer::GENERIC_DISTRIBUTION_PROVIDER_ID, $providerId);
		$criteria->add(GenericDistributionProviderActionPeer::ACTION, $action);

		return GenericDistributionProviderActionPeer::doSelectOne($criteria, $con);
	}
}

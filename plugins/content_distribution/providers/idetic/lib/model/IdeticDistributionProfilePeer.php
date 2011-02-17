<?php
/**
 * @package plugins.ideticDistribution
 * @subpackage model
 */
class IdeticDistributionProfilePeer extends DistributionProfilePeer
{
	const OM_CLASS = 'IdeticDistributionProfile';

	/**
	 * Creates default criteria filter
	 */
	public static function setDefaultCriteriaFilter()
	{
		if ( self::$s_criteria_filter == null )
			self::$s_criteria_filter = new criteriaFilter ();
		
		$c = new myCriteria(); 
		$c->addAnd ( IdeticDistributionProfilePeer::PROVIDER_TYPE, IdeticDistributionProviderType::get()->coreValue(IdeticDistributionProviderType::IDETIC)); 
		$c->addAnd ( IdeticDistributionProfilePeer::STATUS, DistributionProfileStatus::DELETED, Criteria::NOT_EQUAL);
		self::$s_criteria_filter->setFilter ( $c );
	}
}
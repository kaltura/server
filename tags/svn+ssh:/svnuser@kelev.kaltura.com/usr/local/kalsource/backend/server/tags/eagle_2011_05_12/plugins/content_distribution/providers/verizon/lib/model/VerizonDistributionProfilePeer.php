<?php
/**
 * @package plugins.verizonDistribution
 * @subpackage model
 */
class VerizonDistributionProfilePeer extends DistributionProfilePeer
{
	const OM_CLASS = 'VerizonDistributionProfile';

	/**
	 * Creates default criteria filter
	 */
	public static function setDefaultCriteriaFilter()
	{
		if ( self::$s_criteria_filter == null )
			self::$s_criteria_filter = new criteriaFilter ();
		
		$c = new myCriteria(); 
		$c->addAnd ( VerizonDistributionProfilePeer::PROVIDER_TYPE, VerizonDistributionProviderType::get()->coreValue(VerizonDistributionProviderType::VERIZON)); 
		$c->addAnd ( VerizonDistributionProfilePeer::STATUS, DistributionProfileStatus::DELETED, Criteria::NOT_EQUAL);
		self::$s_criteria_filter->setFilter ( $c );
	}
}
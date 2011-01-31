<?php
class MyspaceDistributionProfilePeer extends DistributionProfilePeer
{
	const OM_CLASS = 'MyspaceDistributionProfile';

	/**
	 * Creates default criteria filter
	 */
	public static function setDefaultCriteriaFilter()
	{
		if ( self::$s_criteria_filter == null )
			self::$s_criteria_filter = new criteriaFilter ();
		
		$c = new myCriteria(); 
		$c->addAnd ( MyspaceDistributionProfilePeer::PROVIDER_TYPE, MyspaceDistributionProviderType::get()->coreValue(MyspaceDistributionProviderType::MYSPACE)); 
		$c->addAnd ( MyspaceDistributionProfilePeer::STATUS, DistributionProfileStatus::DELETED, Criteria::NOT_EQUAL);
		self::$s_criteria_filter->setFilter ( $c );
	}
}
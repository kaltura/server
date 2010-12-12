<?php


/**
 * Skeleton subclass for performing query and update operations on the 'generic_distribution_provider' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.model
 */
class GenericDistributionProviderPeer extends BaseGenericDistributionProviderPeer 
{
	public function setInstanceCriteriaFilter ()
	{
		if ( self::$s_criteria_filter == null )
			self::$s_criteria_filter = new criteriaFilter ();
		
		$c = new myCriteria(); 
		$c->addAnd ( GenericDistributionProviderPeer::STATUS, GenericDistributionProviderStatus::DELETED, Criteria::NOT_EQUAL);
		self::$s_criteria_filter->setFilter ( $c );
	}
}

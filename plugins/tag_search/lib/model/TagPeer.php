<?php


/**
 * Skeleton subclass for performing query and update operations on the 'tag' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.tagSearch
 * @subpackage model
 */
class TagPeer extends BaseTagPeer
{
    
	/**
	 * Creates default criteria filter
	 */
	public static function setDefaultCriteriaFilter()
	{
		if(self::$s_criteria_filter == null)
			self::$s_criteria_filter = new criteriaFilter();
		
		$c = KalturaCriteria::create(self::OM_CLASS);
		
		if (kEntitlementUtils::getEntitlementEnforcement())
		{
			$privacyContexts = kEntitlementUtils::getKsPrivacyContextArray();
			$c->addAnd(self::PRIVACY_CONTEXT, $privacyContexts, Criteria::IN);
		}
		$c->addAnd(self::INSTANCE_COUNT, 0, Criteria::GREATER_THAN);
		
		self::$s_criteria_filter->setFilter($c);
	}
	
	/**
	 * @param Criteria $criteria
	 * @param PropelPDO $con
	 */
	public static function doSelect(Criteria $criteria, PropelPDO $con = null)
	{
		$c = clone $criteria;
		
		if($c instanceof KalturaCriteria)
		{
			$c->applyFilters();
			$criteria->setRecordsCount($c->getRecordsCount());
		}
			
		return parent::doSelect($c, $con);
	}
} // TagPeer

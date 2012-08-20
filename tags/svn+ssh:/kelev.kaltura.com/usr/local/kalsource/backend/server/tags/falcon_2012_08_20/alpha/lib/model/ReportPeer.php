<?php


/**
 * Skeleton subclass for performing query and update operations on the 'report' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package Core
 * @subpackage model
 */
class ReportPeer extends BaseReportPeer 
{
	public static function setDefaultCriteriaFilter ()
	{
		if(is_null(self::$s_criteria_filter))
			self::$s_criteria_filter = new criteriaFilter();
		
		$c = new Criteria(); 
		$c->add(self::DELETED_AT, null, Criteria::ISNULL);
		self::$s_criteria_filter->setFilter($c);
	}
} // ReportPeer

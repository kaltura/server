<?php


/**
 * Skeleton subclass for performing query and update operations on the 'edge_server' table.
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
class EdgeServerPeer extends BaseEdgeServerPeer {
	
	public static function setDefaultCriteriaFilter ()
	{
		if ( self::$s_criteria_filter == null )
			self::$s_criteria_filter = new criteriaFilter ();
		
		$c = KalturaCriteria::create(EdgeServerPeer::OM_CLASS);
		$c->addAnd ( EdgeServerPeer::STATUS, EdgeServerStatus::DELETED, Criteria::NOT_EQUAL);
		
		self::$s_criteria_filter->setFilter($c);
	}

} // EdgeServerPeer

<?php

/**
 * Subclass for performing query and update operations on the 'syndication_feed' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class syndicationFeedPeer extends BasesyndicationFeedPeer
{
	public static function setDefaultCriteriaFilter ()
	{
		if ( self::$s_criteria_filter == null )
		{
			self::$s_criteria_filter = new criteriaFilter ();
		}

		$c = new Criteria();
		$c->add ( self::STATUS, KalturaSyndicationFeedStatus::ACTIVE , Criteria::EQUAL );
		self::$s_criteria_filter->setFilter ( $c );
	}
	
	public static function retrieveByPKNoFilter ($pk, $con = null)
	{
		self::setUseCriteriaFilter ( false );
		$res = parent::retrieveByPK( $pk , $con );
		self::setUseCriteriaFilter ( true );
		return $res;
	}

	public static function retrieveByPKsNoFilter ($pks, $con = null)
	{
		self::setUseCriteriaFilter ( false );
		$res = parent::retrieveByPKs( $pks , $con );
		self::setUseCriteriaFilter ( true );
		return $res;
	}    
}

<?php

/**
 * Subclass for performing query and update operations on the 'flavor_params' table.
 *
 * 
 *
 * @package lib.model
 */ 
class flavorParamsPeer extends assetParamsPeer
{
	/** the related Propel class for this table */
	const OM_CLASS = 'flavorParams';
		
	public static function setDefaultCriteriaFilter ()
	{
		if ( self::$s_criteria_filter == null )
		{
			self::$s_criteria_filter = new criteriaFilter ();
		}

		$c = new Criteria();
		$c->add ( self::DELETED_AT, null, Criteria::EQUAL );
		$c->add ( self::TYPE, assetType::FLAVOR );
		self::$s_criteria_filter->setFilter ( $c );
	}
}

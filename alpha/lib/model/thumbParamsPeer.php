<?php

/**
 * Subclass for performing query and update operations on the 'flavor_params' table.
 *
 * 
 *
 * @package lib.model
 */ 
class thumbParamsPeer extends BaseflavorParamsPeer
{
	/** the related Propel class for this table */
	const OM_CLASS = 'thumbParams';
		
	public static function setDefaultCriteriaFilter ()
	{
		if ( self::$s_criteria_filter == null )
		{
			self::$s_criteria_filter = new criteriaFilter ();
		}

		$c = new Criteria();
		$c->add ( self::DELETED_AT, null, Criteria::EQUAL );
		$c->add ( self::TYPE, assetType::THUMBNAIL );
		self::$s_criteria_filter->setFilter ( $c );
	}
}

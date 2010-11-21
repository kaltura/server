<?php

/**
 * Subclass for performing query and update operations on the 'flavor_params' table.
 *
 * 
 *
 * @package lib.model
 */ 
class thumbParamsPeer extends assetParamsPeer
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

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     thumbParams
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{
		return parent::retrieveByPK($pk, $con);
	}
}

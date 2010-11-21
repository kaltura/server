<?php

/**
 * Subclass for performing query and update operations on the 'flavor_asset' table.
 *
 * 
 *
 * @package lib.model
 */ 
class thumbAssetPeer extends assetPeer
{
	/** the related Propel class for this table */
	const OM_CLASS = 'thumbAsset';

	public static function setDefaultCriteriaFilter ()
	{
		if ( self::$s_criteria_filter == null )
		{
			self::$s_criteria_filter = new criteriaFilter ();
		}

		$c = new Criteria();
		$c->add ( self::STATUS, flavorAsset::FLAVOR_ASSET_STATUS_DELETED, Criteria::NOT_EQUAL );
		$c->add ( self::TYPE, assetType::THUMBNAIL );
		self::$s_criteria_filter->setFilter ( $c );
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     thumbAsset
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{
		return parent::retrieveByPK($pk, $con);
	}
}
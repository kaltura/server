<?php

/**
 * Subclass for performing query and update operations on the 'conversion_profile_2' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class conversionProfile2Peer extends BaseconversionProfile2Peer
{
	public static function setDefaultCriteriaFilter ()
	{
		if ( self::$s_criteria_filter == null )
		{
			self::$s_criteria_filter = new criteriaFilter ();
		}

		$c = new Criteria();
		$c->add ( self::DELETED_AT, null, Criteria::EQUAL );
		$c->add ( self::STATUS, ConversionProfileStatus::DELETED, Criteria::NOT_EQUAL );
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

	public static function getIds(Criteria $criteria, $con = null)
	{
		$result = array();
		$profiles = conversionProfile2Peer::doSelect($criteria, $con);
		foreach ($profiles as $profile)
		{
			$result[] = $profile->getId();
		}
		
		return $result;
	}
	public static function getCacheInvalidationKeys()
	{
		return array(array("conversionProfile2:id=%s", self::ID), array("conversionProfile2:partnerId=%s", self::PARTNER_ID));		
	}
	
	public static function retrieveByPartnerIdAndSystemName ($partnerId, $systemName, $type)
	{
		$c = new Criteria();
		$c->addAnd(conversionProfile2Peer::PARTNER_ID, $partnerId);
		$c->addAnd(conversionProfile2Peer::SYSTEM_NAME, $systemName);
		$c->addAnd(conversionProfile2Peer::STATUS, ConversionProfileStatus::ENABLED);
		$c->addAnd(conversionProfile2Peer::TYPE, $type);
		
		return conversionProfile2Peer::doSelectOne($c);
	}
	
}

<?php

/**
 * Subclass for performing query and update operations on the 'access_control' table.
 *
 * 
 *
 * @package lib.model
 */ 
class accessControlPeer extends BaseaccessControlPeer
{
	public static function setDefaultCriteriaFilter ()
	{
		if ( self::$s_criteria_filter == null )
		{
			self::$s_criteria_filter = new criteriaFilter ();
		}

		$c = new Criteria();
		$c->add ( self::DELETED_AT, null, Criteria::EQUAL );
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
	
	public static function getIdsValidForScope(accessControlScope $scope)
	{
		$profiles = self::getValidForScope($scope);
		$ids = array();
		foreach($profiles as $profile)
		{
			$ids[] = $profile->getId();
		}
		return $ids;
	}
	
	public static function getValidForScope(accessControlScope $scope)
	{
		$c = new Criteria();
		$c->setLimit(Partner::MAX_ACCESS_CONTROLS);
		$profiles = self::doSelect($c);
		$curretProfiles = array();
		foreach($profiles as $profile)
		{
			$profile->setScope($scope);
			if ($profile->isValid())
				$curretProfiles[] = $profile;
		}
		
		return $curretProfiles;
	}
}
<?php

/**
 * Subclass for performing query and update operations on the 'category' table.
 *
 * 
 *
 * @package lib.model
 */ 
class categoryPeer extends BasecategoryPeer
{
	const CATEGORY_SEPARATOR = ">";
	
	const MAX_CATEGORY_NAME = 60;
	
	
	private static $invalid_characters = array('>','<',',');
	
	private static $replace_character = "_";
	
	
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

	public static function getParsedName($v)
	{
		$v = substr($v, 0, self::MAX_CATEGORY_NAME);
		$v = str_replace(self::$invalid_characters, self::$replace_character, $v);
		
		return $v;
	}

	public static function getParsedFullName($v)
	{
		$names = explode(self::CATEGORY_SEPARATOR, $v);
		$finalNames = array();
		foreach($names as $name)
			$finalNames[] = self::getParsedName($name);
		
		return implode(self::CATEGORY_SEPARATOR, $finalNames);
	}
	
	/**
	 * Get category by full name using exact match (returns null or category object)
	 *  
	 * @param $partnerId
	 * @param $fullName
	 * @param $con
	 * @return category
	 */
	public static function getByFullNameExactMatch($fullName, $con = null)
	{
		$fullName = self::getParsedFullName($fullName);
		
		$c = new Criteria();
		$c->add(categoryPeer::FULL_NAME, $fullName);
		return categoryPeer::doSelectOne($c, $con);
	}
	
	/**
	 * Get categories by full name using full name wildcard match (returns an array)
	 *  
	 * @param $partnerId
	 * @param $fullName
	 * @param $con
	 * @return array
	 */
	public static function getByFullNameWildcardMatch($fullName, $con = null)
	{
		$c = new Criteria();
		$c->add(categoryPeer::FULL_NAME, $fullName."%", Criteria::LIKE);
		
		return categoryPeer::doSelect($c, $con);
	}
}

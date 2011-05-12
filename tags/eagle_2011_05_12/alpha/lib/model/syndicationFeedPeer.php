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
	// cache classes by their type
	private static $class_types_cache = array(
		syndicationFeedType::GOOGLE_VIDEO => parent::OM_CLASS,
		syndicationFeedType::ITUNES => parent::OM_CLASS,
		syndicationFeedType::TUBE_MOGUL => parent::OM_CLASS,
		syndicationFeedType::YAHOO => parent::OM_CLASS,
		syndicationFeedType::KALTURA => 'genericSyndicationFeed',
		syndicationFeedType::KALTURA_XSLT => 'genericSyndicationFeed',		
	);
	
	public static function setDefaultCriteriaFilter ()
	{
		if ( self::$s_criteria_filter == null )
		{
			self::$s_criteria_filter = new criteriaFilter ();
		}

		$c = new Criteria();
		$c->add ( self::STATUS, syndicationFeed::SYNDICATION_ACTIVE , Criteria::EQUAL );
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

	/**
	 * The returned Class will contain objects of the default type or
	 * objects that inherit from the default.
	 *
	 * @param      array $row PropelPDO result row.
	 * @param      int $colnum Column to examine for OM class information (first is 0).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getOMClass($row, $colnum)
	{
		if($row)
		{
			$syndicationFeedType = $row[$colnum + 6]; // type
			if(isset(self::$class_types_cache[$syndicationFeedType]))
				return self::$class_types_cache[$syndicationFeedType];
				
			self::$class_types_cache[$syndicationFeedType] = parent::OM_CLASS;
		}
			
		return parent::OM_CLASS;
	}
}

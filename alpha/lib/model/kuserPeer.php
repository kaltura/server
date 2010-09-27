<?php
/**
 * Subclass for performing query and update operations on the 'kuser' table.
 *
 * 
 *
 * @package lib.model
 */ 
class kuserPeer extends BasekuserPeer 
{
	private static $s_default_count_limit = 301;
	
	public static function getKuserByScreenName( $screen_name  )
	{
		$c = new Criteria();
		$c->add ( kuserPeer::SCREEN_NAME , $screen_name );
		return self::doSelectOne( $c ); 
	}
	
	/**
	 * @param int $partner_id
	 * @param string $puser_id
	 * @param bool $ignore_puser_kuser
	 * @return kuser
	 */
	public static function getKuserByPartnerAndUid($partner_id , $puser_id, $ignore_puser_kuser = false)
	{
		if (defined("KALTURA_API_V3") || $ignore_puser_kuser)
		{
			$c = new Criteria();
			$c->add(self::PARTNER_ID, $partner_id);
			$c->add(self::PUSER_ID, $puser_id);
			return self::doSelectOne($c);			
		}
		
		// just grab the kuser, we dont mind which subp we get
		$puser_kuser = PuserKuserPeer::retrieveByPartnerAndUid( $partner_id , 0, $puser_id , true );
		if ( !$puser_kuser ) return false;
		return $puser_kuser->getKuser();
	}
	
	public static function getActiveKuserByPartnerAndUid($partner_id , $puser_id)
	{
		$c = new Criteria();
		$c->add(self::STATUS, KalturaUserStatus::ACTIVE);
		$c->add(self::PARTNER_ID, $partner_id);
		$c->add(self::PUSER_ID, $puser_id);
		return self::doSelectOne($c);			
	}
	
	public static function createKuserForPartner($partner_id, $puser_id)
	{
		$kuser = self::getKuserByPartnerAndUid($partner_id, $puser_id);
		
		if (!$kuser)
		{
			$kuser = new kuser();
			$kuser->setPuserId($puser_id);
			$kuser->setScreenName($puser_id);
			$kuser->setFullName($puser_id);
			$kuser->setPartnerId($partner_id);
			$kuser->setStatus(1); // KalturaUserStatus::ACTIVE
			$kuser->save();
		}
		
		return $kuser;
	}
	
	/**
	 * This function returns a pager object holding the given user's favorite users
	 *
	 * @param int $kuserId = the requested user
	 * @param int $privacy = the privacy filter
	 * @param int $pageSize = number of kshows in each page
	 * @param int $page = the requested page
	 * @return the pager object
	 */
	public static function getUserFavorites($kuserId, $privacy, $pageSize, $page)
	{
		$c = new Criteria();
		$c->addJoin(kuserPeer::ID, favoritePeer::SUBJECT_ID, Criteria::INNER_JOIN);
		$c->add(favoritePeer::KUSER_ID, $kuserId);
		$c->add(favoritePeer::SUBJECT_TYPE, favorite::SUBJECT_TYPE_USER);
		$c->add(favoritePeer::PRIVACY, $privacy);
		$c->setDistinct();
		
		// our assumption is that a request for private favorites should include public ones too 
		if( $privacy == favorite::PRIVACY_TYPE_USER ) 
		{
			$c->addOr( favoritePeer::PRIVACY, favorite::PRIVACY_TYPE_WORLD );
		}
			
		$c->addAscendingOrderByColumn(kuserPeer::SCREEN_NAME);
		
	    $pager = new sfPropelPager('kuser', $pageSize);
	    $pager->setCriteria($c);
	    $pager->setPage($page);
	    $pager->init();
			    
	    return $pager;
	}

	/**
	 * This function returns a pager object holding the given user's favorite entries
	 * each entry holds the kuser object of its host.
	 *
	 * @param int $kuserId = the requested user
	 * @param int $privacy = the privacy filter
	 * @param int $pageSize = number of kshows in each page
	 * @param int $page = the requested page
	 * @return the pager object
	 */
	public static function getUserFans($kuserId, $privacy, $pageSize, $page)
	{
		$c = new Criteria();
		$c->addJoin(kuserPeer::ID, favoritePeer::KUSER_ID, Criteria::INNER_JOIN);
		$c->add(favoritePeer::SUBJECT_ID, $kuserId);
		$c->add(favoritePeer::SUBJECT_TYPE, favorite::SUBJECT_TYPE_USER);
		$c->add(favoritePeer::PRIVACY, $privacy);
		
		$c->setDistinct();
		
		// our assumption is that a request for private favorites should include public ones too 
		if( $privacy == favorite::PRIVACY_TYPE_USER ) 
		{
			$c->addOr( favoritePeer::PRIVACY, favorite::PRIVACY_TYPE_WORLD );
		}
			
		$c->addAscendingOrderByColumn(kuserPeer::SCREEN_NAME);
		
	    $pager = new sfPropelPager('kuser', $pageSize);
	    $pager->setCriteria($c);
	    $pager->setPage($page);
	    $pager->init();
			    
	    return $pager;
	}
	
	/**
	 * This function returns a pager object holding the specified list of user favorites, 
	 * sorted by a given sort order.
	 * the $mine_flag param decides if to return favorite people or fans
	 */
	public static function getUserFavoritesOrderedPager( $order, $pageSize, $page, $kuserId, $mine_flag )
	{
		$c = new Criteria();
		
		if ( $mine_flag ) 
		{
			$c->addJoin(kuserPeer::ID, favoritePeer::SUBJECT_ID, Criteria::INNER_JOIN);
			$c->add(favoritePeer::KUSER_ID, $kuserId); 
		}
		else 
		{
			$c->addJoin(kuserPeer::ID, favoritePeer::KUSER_ID, Criteria::INNER_JOIN);
			$c->add(favoritePeer::SUBJECT_ID, $kuserId); 
		}
			
		$c->add(favoritePeer::SUBJECT_TYPE, favorite::SUBJECT_TYPE_USER);
		
		// TODO: take privacy into account
		$privacy = favorite::PRIVACY_TYPE_USER;
		$c->add(favoritePeer::PRIVACY, $privacy);
		// our assumption is that a request for private favorites should include public ones too 
		if( $privacy == favorite::PRIVACY_TYPE_USER ) 
		{
			$c->addOr( favoritePeer::PRIVACY, favorite::PRIVACY_TYPE_WORLD );
		}
			
		switch( $order )
		{
			
			case kuser::KUSER_SORT_MOST_VIEWED: $c->addDescendingOrderByColumn(kuserPeer::VIEWS);  break;
			case kuser::KUSER_SORT_MOST_RECENT: $c->addAscendingOrderByColumn(kuserPeer::CREATED_AT);  break;
			case kuser::KUSER_SORT_NAME: $c->addAscendingOrderByColumn(kuserPeer::SCREEN_NAME); break;
			case kuser::KUSER_SORT_AGE: $c->addAscendingOrderByColumn(kuserPeer::DATE_OF_BIRTH); break;
			case kuser::KUSER_SORT_COUNTRY: $c->addAscendingOrderByColumn(kuserPeer::COUNTRY); break;
			case kuser::KUSER_SORT_CITY: $c->addAscendingOrderByColumn(kuserPeer::CITY); break;
			case kuser::KUSER_SORT_GENDER: $c->addAscendingOrderByColumn(kuserPeer::GENDER); break;		
			case kuser::KUSER_SORT_PRODUCED_KSHOWS: $c->addDescendingOrderByColumn(kuserPeer::PRODUCED_KSHOWS); break;
			
			default: $c->addAscendingOrderByColumn(kuserPeer::SCREEN_NAME);
		}
		
		$c->setDistinct();
		
		
	    $pager = new sfPropelPager('kuser', $pageSize);
	    $pager->setCriteria($c);
	    $pager->setPage($page);
	    $pager->init();
			    
	    return $pager;
	}
	
	
	/**
	 * This function returns a pager object holding all the users
	 */
	public static function getAllUsersOrderedPager( $order, $pageSize, $page )
	{
		$c = new Criteria();
		
		switch( $order )
		{
			
			case kuser::KUSER_SORT_MOST_VIEWED: $c->addDescendingOrderByColumn(kuserPeer::VIEWS);  break;
			case kuser::KUSER_SORT_MOST_RECENT: $c->addAscendingOrderByColumn(kuserPeer::CREATED_AT);  break;
			case kuser::KUSER_SORT_NAME: $c->addAscendingOrderByColumn(kuserPeer::SCREEN_NAME); break;
			case kuser::KUSER_SORT_AGE: $c->addAscendingOrderByColumn(kuserPeer::DATE_OF_BIRTH); break;
			case kuser::KUSER_SORT_COUNTRY: $c->addAscendingOrderByColumn(kuserPeer::COUNTRY); break;
			case kuser::KUSER_SORT_CITY: $c->addAscendingOrderByColumn(kuserPeer::CITY); break;
			case kuser::KUSER_SORT_GENDER: $c->addAscendingOrderByColumn(kuserPeer::GENDER); break;		
			case kuser::KUSER_SORT_MOST_ENTRIES: $c->addDescendingOrderByColumn(kuserPeer::ENTRIES); break;		
			case kuser::KUSER_SORT_MOST_FANS: $c->addDescendingOrderByColumn(kuserPeer::FANS); break;		
			
			default: $c->addAscendingOrderByColumn(kuserPeer::SCREEN_NAME);
		}
		
		$pager = new sfPropelPager('kuser', $pageSize);
	    $pager->setCriteria($c);
	    $pager->setPage($page);
	    $pager->init();
			    
	    return $pager;
	}
	

	public static function selectIdsForCriteria ( Criteria $c )
	{
		$c->addSelectColumn(self::ID);
		$rs = self::doSelectStmt($c);
		$id_list = Array();
		
		while($rs->next())
		{
			$id_list[] = $rs->getInt(1);
		}
		
		$rs->close();
		
		return $id_list;
	}

	public static function doCountWithLimit (Criteria $criteria, $distinct = false, $con = null)
	{
		$criteria = clone $criteria;
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn("DISTINCT ".self::ID);
		} else {
			$criteria->addSelectColumn(self::ID);
		}

		$criteria->setLimit( self::$s_default_count_limit );
		
		$rs = self::doSelectStmt($criteria, $con);
		$count = 0;
		while($rs->next())
			$count++;
	
		return $count;
	}
	
	
	public static function doStubCount (Criteria $criteria, $distinct = false, $con = null)
	{
		return 0;
	}	
	
}

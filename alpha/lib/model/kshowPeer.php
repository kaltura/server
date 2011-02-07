<?php
/**
 * Subclass for performing query and update operations on the 'kshow' table.
 *
 *
 *
 * @package Core
 * @subpackage model
 */
class kshowPeer extends BasekshowPeer 
{
	private static $s_default_count_limit = 301;

	/**
	 * This function sets the requested order of kshows to the given criteria object.
	 * we can use an associative array to hold the ordering fields instead of the
	 * switch statement being used now
	 *
	 * @param $c = given criteria object
	 * @param int = $order the requested sort order
	 */
	private static function setOrder($c, $order)
	{
		switch ($order) {
		case kshow::KSHOW_SORT_MOST_VIEWED:
			//$c->hints = array(kshowPeer::TABLE_NAME => "views_index");
			$c->addDescendingOrderByColumn(self::VIEWS);

			break;

		case kshow::KSHOW_SORT_MOST_RECENT:
			//$c->hints = array(kshowPeer::TABLE_NAME => "created_at_index");
			$c->addDescendingOrderByColumn(self::CREATED_AT);
			break;

		case kshow::KSHOW_SORT_MOST_COMMENTS:
			$c->addDescendingOrderByColumn(self::COMMENTS);
			break;

		case kshow::KSHOW_SORT_MOST_FAVORITES:
			$c->addDescendingOrderByColumn(self::FAVORITES);
			break;

		case kshow::KSHOW_SORT_END_DATE:
			$c->addDescendingOrderByColumn(self::END_DATE);
			break;

		case kshow::KSHOW_SORT_MOST_ENTRIES:
			$c->addDescendingOrderByColumn(self::ENTRIES);
			break;

		case kshow::KSHOW_SORT_NAME:
			$c->addAscendingOrderByColumn(self::NAME);
			break;

		case kshow::KSHOW_SORT_RANK:
			$c->addDescendingOrderByColumn(self::RANK);
			break;
		case kshow::KSHOW_SORT_MOST_UPDATED:
			$c->addDescendingOrderByColumn(self::UPDATED_AT);
			break;
		case kshow::KSHOW_SORT_MOST_CONTRIBUTORS:
			$c->addDescendingOrderByColumn(self::CONTRIBUTORS);
			break;

		}
	}

	/**
	 * This function returns a pager object holding kshows sorted by a given sort order.
	 * each kshow holds the kuser object of its host.
	 *
	 * @param int $order = the requested sort order
	 * @param int $pageSize = number of kshows in each page
	 * @param int $page = the requested page
	 * @return the pager object
	 */
	public static function getOrderedPager($order, $pageSize, $page, $producer_id = null, $kaltura_part_of_flag = null )
	{
		$c = new Criteria();
		self::setOrder($c, $order);

		$c->addJoin(self::PRODUCER_ID, kuserPeer::ID, Criteria::INNER_JOIN);

		if( $kaltura_part_of_flag )
		{
			// in this case we get the user-id in the $producer_id field
			$c->addJoin(self::ID, entryPeer::KSHOW_ID, Criteria::INNER_JOIN);
			$c->add(entryPeer::KUSER_ID, $producer_id);
			$c->add( self::PRODUCER_ID, $producer_id, Criteria::NOT_EQUAL );
			$c->setDistinct();
		}
		else if( $producer_id > 0 ) $c->add( self::PRODUCER_ID, $producer_id );

		$pager = new sfPropelPager('kshow', $pageSize);
	    $pager->setCriteria($c);
	    $pager->setPage($page);
	    $pager->setPeerMethod('doSelectJoinAll');
	    $pager->setPeerCountMethod('doCountJoinAll');
	    $pager->init();

	    return $pager;
	}

	public static function getKshowsByName( $name )
	{
		$c = new Criteria();
		$c->add ( kshowPeer::NAME , $name );
		return kshowPeer::doSelect( $c );
	}

	public static function getFirstKshowByName( $name )
	{
		$kshows = self::getKshowsByName ( $name );
		if( $kshows != null )
			return $kshows[0];
		return null;
	}

	public static function retrieveByIndexedCustomData3 ( $name )
	{
		$c = new Criteria();
		$c->add ( kshowPeer::INDEXED_CUSTOM_DATA_3 , $name );
		$kshows =  kshowPeer::doSelect( $c );
		if( $kshows != null )
			return $kshows[0];
		return null;
	}

	/**
	 * This function returns a pager object holding the given user's favorite entries
	 * each entry holds the kuser object of its host.
	 *
	 * @param int $kuserId = the requested user
	 * @param int $type = the favorite type (currently only SUBJECT_TYPE_ENTRY will match)
	 * @param int $privacy = the privacy filter
	 * @param int $pageSize = number of kshows in each page
	 * @param int $page = the requested page
	 * @return the pager object
	 */
	public static function getUserFavorites($kuserId, $type, $privacy, $pageSize, $page)
	{
		$c = new Criteria();
		$c->addJoin(self::PRODUCER_ID, kuserPeer::ID, Criteria::INNER_JOIN);
		$c->addJoin(self::ID, favoritePeer::SUBJECT_ID, Criteria::INNER_JOIN);
		$c->add(favoritePeer::KUSER_ID, $kuserId);
		$c->add(favoritePeer::SUBJECT_TYPE, $type);
		$c->add(favoritePeer::PRIVACY, $privacy);

		$c->setDistinct();

		// our assumption is that a request for private favorites should include public ones too
		if( $privacy == favorite::PRIVACY_TYPE_USER )
		{
			$c->addOr( favoritePeer::PRIVACY, favorite::PRIVACY_TYPE_WORLD );
		}


		$c->addAscendingOrderByColumn(self::NAME);

	    $pager = new sfPropelPager('kshow', $pageSize);
	    $pager->setCriteria($c);
	    $pager->setPage($page);
	    $pager->setPeerMethod('doSelectJoinkuser');
	    $pager->setPeerCountMethod('doCountJoinkuser');
	    $pager->init();

	    return $pager;
	}



	/**
	 * This function returns a pager object holding the given user's shows, for which he or she is the producer.
	 *
	 * @param int $kuserId = the requested user
	 * @param int $pageSize = number of kshows in each page
	 * @param int $page = the requested page
	 * @param int $order = the requested sort order
	 * @param int $currentKshowId = the current kshow id (e.g. in the browse page) not to be shown again in the other user shows
	 * @return the pager object
	 */
	public static function getUserShows($kuserId, $pageSize, $page, $order, $currentKshowId = 0)
	{

		$c = new Criteria();

		$c->addJoin(self::PRODUCER_ID, kuserPeer::ID, Criteria::INNER_JOIN);
		$c->add(self::PRODUCER_ID, $kuserId);
		if ($currentKshowId)
			$c->add(self::ID, $currentKshowId, criteria::NOT_EQUAL);

		self::setOrder($c, $order);

	    $pager = new sfPropelPager('kshow', $pageSize);
	    $pager->setCriteria($c);
	    $pager->setPage($page);
	    $pager->setPeerMethod('doSelectJoinkuser');
	    $pager->setPeerCountMethod('doCountJoinkuser');
	    $pager->init();

	    return $pager;
	}


		/**
	 * This function returns a pager object holding the set of shows for which given user contributed media.
	 *
	 * @param int $kuserId = the requested user
	 * @param int $pageSize = number of kshows in each page
	 * @param int $page = the requested page
	 * @return the pager object
	 */
	public static function getUserShowsPartOf($kuserId, $pageSize, $page, $order)
	{

		$c = new Criteria();

		$c->addJoin(self::ID, entryPeer::KSHOW_ID, Criteria::INNER_JOIN);
		$c->add(entryPeer::KUSER_ID, $kuserId);
		$c->add( self::PRODUCER_ID, $kuserId, Criteria::NOT_EQUAL );
		self::setOrder($c, $order);
		$c->setDistinct();

	    $pager = new sfPropelPager('kshow', $pageSize);
	    $pager->setCriteria($c);
	    $pager->setPage($page);
	    $pager->setPeerMethod('doSelectJoinkuser');
	    $pager->setPeerCountMethod('doCountJoinkuser');
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

	public static function getKshowsByEntryIds($entry_ids)
	{
		$c = new Criteria();
		//$c->addSelectColumn(kshowPeer::ID);
		//$c->addSelectColumn(kshowPeer::NAME);
		kshowPeer::addSelectColumns($c);
		$c->addJoin(kshowPeer::ID, roughcutEntryPeer::ROUGHCUT_KSHOW_ID);
		$c->add(roughcutEntryPeer::ENTRY_ID, $entry_ids, Criteria::IN);
		return kshowPeer::populateObjects(self::doSelectStmt($c));
	}

	// this function deletes a KSHOW
	// users can only delete their own entries
	public static function deleteKShow( $kshow_id, $kuser_id  )
	{
		$kshow = self::retrieveByPK( $kshow_id );
		if( $kshow == null ) return false;
		if( $kshow->getProducerId() != $kuser_id ) return false;
		else
		{
			$kshow->delete();

			// now delete the subscriptions
			$c = new Criteria();
			$c->add(KshowKuserPeer::KSHOW_ID, $kshow_id ); // the current user knows they just favorited
			$c->add(KshowKuserPeer::SUBSCRIPTION_TYPE, KshowKuser::KSHOW_SUBSCRIPTION_NORMAL); // this table stores other relations too
			$subscriptions = KshowKuserPeer::doSelect( $c );
			foreach ( $subscriptions as $subscription )
			{
					$subscription->delete();
			}

			return true;
		}

	}

	public static function doCountWithLimit (Criteria $criteria, $distinct = false, $con = null)
	{
		$criteria = clone $criteria;
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn("DISTINCT ".kshowPeer::ID);
		} else {
			$criteria->addSelectColumn(kshowPeer::ID);
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

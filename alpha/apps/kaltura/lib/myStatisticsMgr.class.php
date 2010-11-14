<?php
class myStatisticsMgr
{
	private static $s_dirty_objects = array();

	public static function incKuserViews ( kuser $kuser , $delta = 1 )
	{
		$v = $kuser->getViews();
		if ( self::shouldModify ( $kuser , kuserPeer::VIEWS ) );
		{
			self::inc ( $v , $delta);
			$kuser->setViews( $v );
		}
		return $v;
	}

	// will increment either fans or favorites for kshow or entry according to favorite.subject_type
	/**
	const SUBJECT_TYPE_KSHOW = '1';
	const SUBJECT_TYPE_ENTRY = '2';
	const SUBJECT_TYPE_USER = '3';

	*/
	public static function addFavorite ( favorite $favorite )
	{
		self::add ( $favorite );

		$type = $favorite->getSubjectType();
		$id = $favorite->getSubjectId();
		if ( $type == favorite::SUBJECT_TYPE_ENTRY )
		{
			$obj = entryPeer::retrieveByPK( $id );
			if ( $obj ) 
			{
				$v = $obj->getFavorites () 	;
				self::inc ( $v );
				$obj->setFavorites ( $v );
			}
		}
		elseif ( $type == favorite::SUBJECT_TYPE_KSHOW )
		{
			$obj = kshowPeer::retrieveByPK( $id );
			if ( $obj )
			{			
				$v = $obj->getFavorites () 	;
				self::inc ( $v );
				$obj->setFavorites ( $v );
			}
		}
		elseif (  $type == favorite::SUBJECT_TYPE_USER )
		{
			$obj = kuserPeer::retrieveByPK( $id );
			if ( $obj )
			{
				$v = $obj->getFans () 	;
				self::inc ( $v );
				$obj->setFans ( $v );
			}
		}
		// don't forget to save the modified object
		self::add ( $obj );
	}

	//- will increment kuser.entries, kshow.entries & kshow.contributors
	public static function addEntry ( entry $entry )
	{
		$kshow = $entry->getkshow();
		if ( $kshow )
		{
			$v = $kshow->getEntries();
			self::inc ( $v );
			$kshow->setEntries ( $v );
		}

		$c = new Criteria();
		myCriteria::addComment( $c , __METHOD__  );
		$c->add ( entryPeer::KSHOW_ID , $entry->getKshowId() );
		$c->add ( entryPeer::KUSER_ID , $entry->getKuserId() );
		$c->setLimit ( 2 );
		$res = entryPeer::doCount( $c );
		if ( $res < 1 && $kshow != null )
		{
			// kuser didn't contribute to this kshow - increment
			$v = $kshow->getContributors();
			self::inc ( $v );
			$kshow->setContributors( $v );
		}

		$kuser = $entry->getkuser();
		if ( $kuser )
		{
			$v = $kuser->getEntries();
			self::inc ( $v );
			$kuser->setEntries ( $v );
		}

		self::add ( $kshow );
		self::add ( $kuser );
	}

	//- will increment kuser.entries, kshow.entries & kshow.contributors
	public static function deleteEntry ( entry $entry )
	{
		$kshow = $entry->getkshow();
		if ($kshow)
		{
			$v = $kshow->getEntries();
			self::dec ( $v );
			$kshow->setEntries ( $v );
	
			$c = new Criteria();
			myCriteria::addComment( $c , __METHOD__  );
			$c->add ( entryPeer::KSHOW_ID , $entry->getKshowId() );
			$c->add ( entryPeer::KUSER_ID , $entry->getKuserId() );
			$c->setLimit ( 2 );
			$res = entryPeer::doCount( $c );
			if ( $res == 1 )
			{
				// if $res > 1 -  this kuser contributed more than one entry, deleting this one should still leave him a contributor 
				// if $res < 1 -  this kuser never contributed - strange! but no need to dec the contributors
				// kuser did contribute to this kshow - decrement
				$v = $kshow->getContributors();
				self::dec ( $v );
				$kshow->setContributors( $v );
			}
	
			$kuser = $entry->getkuser();
			if ( $kuser )
			{
				$v = $kuser->getEntries();
				self::dec ( $v );
				$kuser->setEntries ( $v );
			}
	
			self::add ( $kshow );
			self::add ( $kuser );
		}
	}
	
	//- will increment kuser.produced_kshows
	public static function addKshow ( kshow $kshow )
	{
		$kuser = $kshow->getKuser();
		// this might happen when creating a temp kshow without setting its producer 
		if ( $kuser == NULL ) return;
		
		$v = $kuser->getProducedKshows ();
		self::inc ( $v );
		$kuser->setProducedKshows ( $v );
		self::add ( $kuser );
	}

	//- will decrement kuser.produced_kshows
	public static function deleteKshow ( kshow $kshow )
	{
		$kuser = $kshow->getKuser();
		// this might happen when creating a temp kshow without setting its producer 
		if ( $kuser == NULL ) return;
		
		$v = $kuser->getProducedKshows ();
		self::dec( $v );
		$kuser->setProducedKshows ( $v );
		self::add ( $kuser );
	}
		
	public static function incKshowViews ( kshow $kshow , $delta = 1 )
	{
		$v = $kshow->getViews();
		if ( self::shouldModify ( $kshow , kshowPeer::VIEWS ) );
		{
			self::inc ( $v , $delta);
			$kshow->setViews( $v );
		}
		return $v;
	}

	public static function incKshowPlays ( kshow $kshow , $delta = 1 )
	{
		$v = $kshow->getPlays();
		
KalturaLog::log ( __METHOD__ . ": " . $kshow->getId() . " plays: $v");
 
		if ( self::shouldModify ( $kshow , kshowPeer::PLAYS ) );
		{
			self::inc ( $v , $delta);
			$kshow->setPlays( $v );
		}
		
KalturaLog::log ( __METHOD__ . ": " . $kshow->getId() . " plays: $v");		
		return $v;
	}
	
/*	
	// - do we vote for kshows ??? - this should be derived from the roughcut
	public static function incKshowVotes ( kshow $kshow )
	{
	}
*/

	// - will increment kshow.comments or entry.comments according to comment_type
	/**
	* 	const COMMENT_TYPE_KSHOW = 1;
	const COMMENT_TYPE_DISCUSSION = 2;
	const COMMENT_TYPE_USER = 3;
	const COMMENT_TYPE_SHOUTOUT = 4;
	*
	*/
	public static function addComment ( comment $comment )
	{
		$obj = NULL;
		$type = $comment->getCommentType();
		$id = $comment->getSubjectId();
		if ( $type == comment::COMMENT_TYPE_KSHOW || 
			$type == comment::COMMENT_TYPE_SHOUTOUT ||
			$type == comment::COMMENT_TYPE_DISCUSSION )
		{
			$obj = kshowPeer::retrieveByPK( $id );
			if ( $obj )
			{
				$v = $obj->getComments () 	;
				self::inc ( $v );
				$obj->setComments ( $v );
			}
		}
		elseif ( $type == comment::COMMENT_TYPE_USER )
		{
/*			$obj = kuserPeer::retrieveByPK( $id );
			$v = $obj->getComments () 	;
			self::inc ( $v );
			$obj->setComments ( $v );
*/
		}

		// TODO - what about the other types ?
		if ( $obj != NULL )	self::add ( $obj );
	}

	public static function addSubscriber ( KshowKuser $kushow_kuser )
	{
		$type = $kushow_kuser->getAlertType();

		if ( $type == KshowKuser::KSHOW_SUBSCRIPTION_NORMAL )
		{
			$kshow = $kushow_kuser->getkshow();
			if ( $kshow )
			{
				$v = $kshow->getSubscribers() 	;
				self::inc ( $v );
				$kshow->setSubscribers ( $v );
			}

			self::add ( $kshow );
		}
	}

	// - will increment kshow.number_of_updates
	public static function incKshowUpdates ( kshow $kshow, $delta = 1 )
	{
		$v = $kshow->getNumberOfUpdates();
		if ( self::shouldModify( $kshow , kshowPeer::NUMBER_OF_UPDATES ) )
		{
			self::inc ( $v , $delta);
			$kshow->setNumberOfUpdates( $v );
		}
		return $v;
	}

	public static function incEntryViews ( entry $entry , $delta = 1 )
	{
		$v = $entry->getViews();
		if ( $delta == 0 ) return $v;
		if ( self::shouldModify ( $entry , entryPeer::VIEWS ) );
		{
			self::inc ( $v , $delta);
			$entry->setViews( $v );
		}
		
		if ( $entry->getType() == entryType::MIX )
		{
			$enclosing_kshow = $entry->getKshow();
			if ( $enclosing_kshow  )
			{
				$kshow_views = $enclosing_kshow->getViews() ;
				$enclosing_kshow->setViews ( ++$kshow_views );
				self::add( $enclosing_kshow );
			}
		}		
		return $v;
	}


	public static function incEntryPlays ( entry $entry , $delta = 1 )
	{
		$v = $entry->getPlays();
		if ( $delta == 0 ) return $v;
		if ( self::shouldModify ( $entry , entryPeer::PLAYS ) );
		{
			self::inc ( $v , $delta);
			$entry->setPlays( $v );
		}
		
		if ( $entry->getType() == entryType::MIX )
		{
			$enclosing_kshow = $entry->getKshow();
			if ( $enclosing_kshow  )
			{
				$kshow_views = $enclosing_kshow->getPlays() ;
				$enclosing_kshow->setPlays ( ++$kshow_views );
				self::add( $enclosing_kshow );
			}
		}		
		return $v;
	}
	
	
	public static function addKvote ( kvote $kvote , $delta_rank )
	{
		$entry = $kvote->getEntry();
		$res = self::incEntryVotes ( $entry , $delta_rank );
		return $res; 
	}

	// - will update votes , total_rank & rank
	// if the ebtry is of type roughcut -0 will update the kshow's rank too
	private static function incEntryVotes ( entry $entry , $delta_rank )
	{
		$res = array();
		
		$votes = $entry->getVotes();
		if ( self::shouldModify ( $entry , entryPeer::VOTES ) );
		{
			self::inc ( $votes );
			$entry->setVotes( $votes );
				
			$total_rank = $entry->getTotalRank();
			self::inc ( $total_rank , $delta_rank );
			$entry->setTotalRank( $total_rank );
				
			$res ["entry"] = $entry;
			// can assume $votes > 0
			$rank = $entry->setRank ( ( $total_rank / $votes ) * 1000 );
				
			// if rouhcut - update the kshow's rank too
			if ( $entry->getType() == entryType::MIX )
			{
				$enclosing_kshow = $entry->getKshow();
				if ( $enclosing_kshow  )
				{
					$kshow_votes = $enclosing_kshow->getVotes() ;
					$enclosing_kshow->setVotes ( ++$kshow_votes );
					if ( true ) //if ( $enclosing_kshow->getRank() <  $entry->getRank() ) // rank the show 
					{
						$enclosing_kshow->setRank ( $entry->getRank() );
						self::add( $enclosing_kshow );
						$res ["kshow"] = $enclosing_kshow;
					}
				}
			}
		}
		return $res;
	}


	// TODO - might be duplicates in the list- try to avoid redundant saves
	// (although won't commit to DB because there will be no internal dirty flags)
	public static function saveAllModified ()
	{
		foreach ( self::$s_dirty_objects as $id => $dirty_obj )
		{
			self::log ( "saving: [$id]" );
			$dirty_obj->save();
		}
		
		// free all the object - create a new empty array
		self::$s_dirty_objects = array();
	}

	private static function shouldModify ( BaseObject $baseObject , $col )
	{
		if ( ! $baseObject->isColumnModified($col ) )
		{
			self::add ( $baseObject );
			return true;
		}

		// this object should not be updated twice
		return false;
	}

	private static function add ( /*BaseObject*/ $baseObject )
	{
		if ( $baseObject != null )
		{
			$id = get_class ( $baseObject ) . $baseObject->getId();
			self::log ( "adding: [$id]" );
			self::$s_dirty_objects[$id] = $baseObject;
		}
	}

	private static function inc ( &$num , $delta = 1 )
	{
		if ( ! is_numeric ( $num )) $num = 0;
		$num += $delta;
	}
	
	private static function dec ( &$num , $delta = 1 )
	{
		if ( ! is_numeric ( $num )) $num = 0;
		$num -= $delta;
	}	
	
	
	private static function log ( $str )
	{
		//sfLogger::getInstance()->log ( "stats: " . $str , "err ");
	}
}
?>
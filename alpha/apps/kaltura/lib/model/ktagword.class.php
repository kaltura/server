<?php

require_once("myContentStorage.class.php");

class ktagword {
	const MINIMUN_TAGS = 10;
	const TOP_TAGS_TTL_SECONDS = 600; // top tags will be cached for 10 minutes
	const ADMIN_TAGS_TTL_SECONDS = 600; // admin tags will be cached for 10 minutes
	const TAG_SEPARATOR = ",";

	static protected $cache;

	static public function initCache()
	{
		if (self::$cache == null)
		self::$cache = new myCache("ktagword");
	}

	static public function getTagsArray ( $tags_str )
	{
		$dummy = "";
		return self::tagsListFromParagraph ( $tags_str  , $dummy );
	}

	/**
	 * if $fixed_paragraph is a string longer than 0 - it will hold the fixed string after clearing the invalid parameters
	 */
	static public function tagsListFromParagraph($paragraph , &$fixed_paragraph)
	{
		$tagwords = explode( self::TAG_SEPARATOR, $paragraph);

		$validTagwords = array();

		foreach($tagwords as $tagword)
		{
			// first trim white spaces from the tags
			$tagword = strtolower  ( trim ( $tagword ) );
			if (strlen($tagword) >= 2)  // check only words with 2 chars or more
			{
				$fixed_tag = substr ( $tagword , 0 , 30 ); // make sure not to let strings longer than the size in the DB

				// remove duplicates
				if ( ! in_array ( $fixed_tag , $validTagwords ))
				{
					$validTagwords[] = $fixed_tag;
				}
			}
		}

		// check if the caller expects us to return the fixed
		if ( strlen ( $fixed_paragraph ) > 0 )
		{
			// when gluing the tags together - use the separator with " "
			$fixed_paragraph = implode( self::TAG_SEPARATOR . " "  , $validTagwords);
		}
		return $validTagwords;
	}

	// build memcache from DB
	static private function initFromDbImpl()
	{
		self::initCache();

		$c = new Criteria();
		$c->setLimit( 1000 );
		$tagword_count_list = TagwordCountPeer::doSelect( $c );

		foreach ( $tagword_count_list as $tagword_count )
		{
			self::$cache->put ( $tagword_count->getTag() , $tagword_count->getTagCount() );
		}

		self::updateTagCount ( count ( $tagword_count_list ) );
	}

	/*
	 * will update memcache with the tag count in the DB.
	 * It will actually go to the DB only if the tag count is very small- meaning memcache is still "learning".
	 * Calling this method TOO often is bad because checking the size of the tag_count in memcache is in itself relatively heavy.
	 * It should be done at the system startup.
	 */
	static public function initFromDb()
	{
		if ( self::getTagCount() < self::MINIMUN_TAGS )
		{
			self::initFromDbImpl();
		}
	}

	/*
	 * This method should not be used from the application.
	 * Memcache should be synched with the DB pretty quickly after the applicaiton starts working, and even if memcache goes donw and up again,
	 * initFromDb will make sure it's up-to-date with DB.
	 * This should be called only if a massive update to the DB done externally to the application (manually on the DB or by script that does not
	 * update memcache).
	 */
	static public function forceInitFromDb()
	{
		self::initFromDbImpl();
	}

	static public function getTopTags ( $limit = 25 )
	{
		self::initFromDb();

		self::initCache();
		// add cache for these top tags - use memcache
		$tagword_count_list = self::$cache->get( "top_tags" );
		if ( $tagword_count_list == NULL )
		{
			$c = new Criteria();
			$c->setLimit( $limit );
			$c->addDescendingOrderByColumn( TagwordCountPeer::TAG_COUNT );
			$tagword_count_list = TagwordCountPeer::doSelect( $c );
			self::$cache->put( "top_tags" , $tagword_count_list , self::TOP_TAGS_TTL_SECONDS );
		}

		return $tagword_count_list;

	}

	static public function getWeight($tagword)
	{
		self::initCache();
		$occurrences = self::$cache->get($tagword);
		return $occurrences == null ? 0 : $occurrences;
	}


	/**
	 * updates the tags count in the DB and the cache and returns a fixed from $tags string that can be later displayed
	 */
	static public function updateTags($prevTags, $tags , $update_db = true  )
	{

		self::initCache();

		$dummy = "";
		$prevTagwords = self::tagsListFromParagraph($prevTags , $dummy );
		$fixed_tags = "bla";
		$tagwords = self::tagsListFromParagraph($tags , $fixed_tags );

		$removedTagwords = array_diff($prevTagwords, $tagwords);

		$s = "";

		foreach($removedTagwords as $tagword)
		{
			if ( empty ( $tagword ) ) continue;
			$s .= "-$tagword\n";
			$occurrences = self::$cache->decrement ( $tagword );

			self::updateDb(  $tagword , $occurrences , $update_db );
		}

		$newTagwords = array_diff($tagwords, $prevTagwords);

		foreach($newTagwords as $tagword)
		{
			if ( empty ( $tagword ) ) continue;
			$occurrences = self::$cache->increment ( $tagword );

			self::updateDb(  $tagword , $occurrences , $update_db );
		}

		return $fixed_tags;
	}

	private static function updateDb ( $tagword , $occurrences , $update_db )
	{
/*		
		if ( $update_db )
		{
			$tagword_count = TagwordCountPeer::retrieveByPK( $tagword );
			if ( $tagword_count == NULL )
			{
				$tagword_count = new TagwordCount();
				$tagword_count->setTag( $tagword );
			}

			$tagword_count->setTagCount( $occurrences );
			$tagword_count->save();
		}
*/
	}

	static public function updateTagCount ( $count )
	{
		self::initCache();

		self::$cache->increment ( "tagCount" , $count );
	}

	static public function getTagCount ( )
	{
		self::initCache();

		return self::$cache->get( "tagCount" );
	}

	/* ---------------- admin_tags ---------------- */
	public static function getAdminTags ( $partner_id , $limit = 10000 , $force_get_from_db = false , $entry_id_to_exclude = null )
	{
		// deprecated - very heavy query for getting the unique tagwords from the entry table 
		
		return "";
/*		
		if ( $limit === null ) $limit = 10000;
		$admin_tags_str = self::getAdminTagsFromCache( $partner_id );
		// if no tags from cahce, or $force_get_from_db - fetch from db
		if ( $force_get_from_db || ! $admin_tags_str )
		{
			$c = new Criteria();
			// the partner id is implicitly set - but can do it explicitly too
			$c->addAnd ( entryPeer::PARTNER_ID , $partner_id );
			// TODO - should add only ready entries ??
			$c->addAnd ( entryPeer::STATUS, entryStatus::READY ); 
			$c->addSelectColumn( entryPeer::ADMIN_TAGS );
			// TODO - should we group by ? - if so - need to add index in DB 
//			$c->addGroupByColumn( entryPeer::ADMIN_TAGS );
			if ( $entry_id_to_exclude )
			{
				// don't include this entry_id because it is about to change anyway
				$c->addAnd ( entryPeer::ID , $entry_id_to_exclude , Criteria::NOT_EQUAL );
			}
			$c->setLimit( $limit );
			$rs = entryPeer::doSelectStmt( $c );
			$admin_tags = array();
			$admin_tags_str = "";
		
			$res = $rs->fetchAll();
			foreach($res as $record) 
			{
				$current_admin_tags_str = trim($record[0]);
				$admin_tags_str .= "," . $current_admin_tags_str;
			}
			
//			// old code from doSelectRs
//			while($rs->next())
//			{
//				$current_admin_tags_str = trim($rs->getString(1));
//				$admin_tags_str .= "," . $current_admin_tags_str;
//			}	
	
			// after creating a long long string - create the array
			$admin_tags = self::mergeAdminTags ( $admin_tags , strtolower ( $admin_tags_str ) );
			$admin_tags_str = self::sortAndSetAdminTags ( $partner_id ,  $admin_tags  );
		}
		// TODO - store in cache
		return $admin_tags_str;
*/
	}
	
	public static function updateAdminTags ( entry $entry )
	{
		// be ready to update the admin_tags if the admin_tags OR the status was modified
		if ( $entry->isColumnModified( entryPeer::ADMIN_TAGS ) || $entry->isColumnModified( entryPeer::STATUS ) )
		{
			$partner_id = $entry->getPartnerId();
			// TODO - add the delta of the tags to the cache  
			$admin_tags_str = self::getAdminTags ( $partner_id , null , true , $entry->getId() );
//KalturaLog::log( "1 adminTags for partner [$partner_id]\n$admin_tags_str");			
			$admin_tags = explode ( self::TAG_SEPARATOR , $admin_tags_str ) ; 
			$admin_tags = self::mergeAdminTags( $admin_tags , $entry->getAdminTags() );
			$after_change = self::sortAndSetAdminTags ( $partner_id , $admin_tags );
//KalturaLog::log( "2 adminTags for partner [$partner_id]\n$after_change");			
		}
	}
	
	public static function fixAdminTags (  $tags )
	{
		$tag_arr = array();	
		$tag_arr = self::mergeAdminTags( $tag_arr , strtolower( $tags ));
		return implode ( self::TAG_SEPARATOR , $tag_arr );
	}
	
	private  static function mergeAdminTags ( $admin_tags , $new_admin_tags_str )
	{
		$tag_list = array ();
		
		$current_admin_tags = explode ( self::TAG_SEPARATOR , $new_admin_tags_str ) ;

		foreach ( $current_admin_tags as $tag )
		{ 
			$tag = strtolower(trim($tag)); // clear all white spaces from the beginning and end of each tag and move to lower case
			// add tags that are NOT empty - the fact that the key & value are set will prevent duplicates 
			if ( $tag ) $tag_list[$tag]=$tag; // make both the key & value poit to the tag
		}
		foreach ( $admin_tags as $tag )
		{ 
			$tag = strtolower(trim($tag)); // clear all white spaces from the beginning and end of each tag and move to lower case
			// add tags that are NOT empty - the fact that the key & value are set will prevent duplicates 
			if ( $tag ) $tag_list[$tag]=$tag; // make both the key & value poit to the tag
		}
		return $tag_list;
	}
	
	private static function sortAndSetAdminTags ( $partner_id , $admin_tags )
	{
		sort( $admin_tags , SORT_STRING );
		$admin_tags_str = implode ( self::TAG_SEPARATOR , $admin_tags );
		self::setAdminTagsInCache( $partner_id , $admin_tags_str );		
		return $admin_tags_str;
	}
	
	private static function getAdminTagsFromCache ( $partner_id )
	{
		// TODO - use DB cache
		self::initCache();
		
		return self::$cache->get( "admin_tags_$partner_id" );
	}

	private static function setAdminTagsInCache ( $partner_id , $admin_tags_str )
	{
		// TODO - use DB cache
		self::initCache();
		
		self::$cache->put( "admin_tags_$partner_id" , $admin_tags_str , self::ADMIN_TAGS_TTL_SECONDS );
	}
	
}

?>
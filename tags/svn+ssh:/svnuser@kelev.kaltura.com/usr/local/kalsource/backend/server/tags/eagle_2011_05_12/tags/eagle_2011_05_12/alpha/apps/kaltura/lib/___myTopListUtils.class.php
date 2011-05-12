<?php

interface ITopListProvider
{
	public static function getTopImpl ( $limit );
}

class myTopListUtils
{
	// TODO - PERFORMANCE - choose a longer period for expiry
	const TOP_LIST_EXPIRY_IN_SECONDS = 3600;
	const NUMBER_OF_TOP_TO_DISPLAY = 10;

	static protected $s_top_list_cache;
	
	/**
	 * $cls is a class that implements getTopImpl which actually fetchs the top list
	 * this method caches the 
	 */
	public static function getTopList ( /*ITopListProvider*/ $cls_name , $method_name = 'getTopImpl'  )
	{
		if ( self::$s_top_list_cache == NULL )
		{
			self::$s_top_list_cache = new myCache ( "topList" , self::TOP_LIST_EXPIRY_IN_SECONDS );
		}
		
		// get the list	according to the class -because the cache is static in the base class,
		// each derived class will have it's own list under the class_name 
		$top_list_name = $cls_name; //get_class( $cls );
		$top_list_full = self::$s_top_list_cache->get ( $top_list_name );
		
		if ( $top_list_full == NULL )
		{
			$top_list_full = call_user_func ( array ( $cls_name , $method_name ) ,  self::NUMBER_OF_TOP_TO_DISPLAY );
			self::$s_top_list_cache->put ( $top_list_name , $top_list_full );
		}
		
		$top_list = $top_list_full;
/*		
		$elements_from_original_list = self::NUMBER_OF_TOP_TO_DISPLAY /2 ;
		
		// in case the results are very poor at the beginning...
		if ( count ( $top_list_full ) < $elements_from_original_list )
		{
			$elements_from_original_list =  count ( $top_list_full );
		}
		
		$top_list = array();
		for ( $count = 0 ; $count < $elements_from_original_list ; +$count )
		{
			$top_list[]	= $top_list_full[$count];
		}
	*/	
		
		
		return $top_list;	
	}
	
	public static function forceTopList ( $cls_name )
	{
		if ( self::$s_top_list_cache == NULL )
		{
			self::$s_top_list_cache = new myCache ( "topList" );
		}
		
		// get the list	according to the class -because the cache is static in the base class,
		// each derived class will have it's own list under the class_name 
		$top_list_name = $cls_name; //get_class( $cls );
		$top_list_full = call_user_func ( array ( $cls_name , 'getTopImpl' ) ,  self::NUMBER_OF_TOP_TO_DISPLAY );
		self::$s_top_list_cache->put ( $top_list_name , $top_list_full );
		
	}	
	
	
	public static function getTopBands ( $number_of_results = self::NUMBER_OF_TOP_TO_DISPLAY , $rand=true  )
	{
		if ( self::$s_top_list_cache == NULL )
		{
			self::$s_top_list_cache = new myCache ( "topList" , self::TOP_LIST_EXPIRY_IN_SECONDS );
		}		
	
		$top_list_name = "kshowBands"; //get_class( $cls );
		$top_list_full = self::$s_top_list_cache->get ( $top_list_name );
		
		if ( $top_list_full == NULL )
		{
			$featuredIds = array( 26404, 42551, 31401, 31554, 12913, 18309, 13411, 11931, 15789, 13948, 13380, 13929, 21944, 23057 );
		
			//$top_list_full = kshowPeer::retrieveByPks($featuredIds);
			$c = new Criteria ();
			$c->add ( kshowPeer::ID ,$featuredIds , Criteria::IN );
			$top_list_full = kshowPeer::doSelectJoinkuser( $c );
			
			self::$s_top_list_cache->put ( $top_list_name , $top_list_full );
		}
		
		//if ( $number_of_results < 1 ) $number_of_results = self::NUMBER_OF_TOP_TO_DISPLAY;
		
		if ( $rand )
		{
			// shuffle the array and return the first  $number_of_results
			if ( is_array ( $top_list_full ) ) shuffle ( $top_list_full );
		}
		
		if ( count ( $top_list_full ) > $number_of_results )
		{
			$top_list_full = array_slice  ( $top_list_full , 0 , $number_of_results );
		}
		
		return $top_list_full;	
	}
}
?>
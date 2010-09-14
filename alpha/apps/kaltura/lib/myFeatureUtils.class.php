<?php
class myFeatureUtils
{

	static protected $s_config;
	static protected $s_cache;
	static protected $s_expiry;
	
	private static function init()
	{
		if ( self::$s_config == null )	
			self::$s_config = new myConfigWrapper( "app_" );
			
		self::$s_expiry = self::$s_config->get ( "feature_cache_expiry_sec") ;
		
		if ( self::$s_cache == NULL )
			self::$s_cache = new myCache ( "getFeaturedTeams" , self::$s_expiry );
			
	}
	
	/**
	 * Will store the featured teams in some cache and refresh on a daily basis.
	 * The ids of the featured kshows will be read from a config file at first, and at a later phase for DB.  
	 *
	 */
	public static function getFeaturedTeams ()
	{
		return self::getCachedKshowList ( "featured_team_list" );
	}

	public static function getFeaturedTeamsIdList ()
	{
		self::init();
		
		return self::$s_config->getList ( "featured_team_list" );
	}
	
	/**
	 * Will store the featured shows in some cache and refresh on a daily basis.
	 * The ids of the featured kshows will be read from a config file at first, and at a later phase for DB.  
	 *
	 */
	public static function getFeaturedShows ()
	{
		return self::getCachedKshowList ( "featured_show_list" );
	}

	public static function getFeaturedShowsIdList ()
	{
		self::init();
		
		return self::$s_config->getList ( "featured_show_list" );
	}

	/**
	 * Will store the featured intors in some cache and refresh on a daily basis.
	 * The ids of the featured kshows will be read from a config file at first, and at a later phase for DB.  
	 *
	 */
	public static function getFeaturedIntros ()
	{
		return self::getCachedKshowList ( "featured_intro_list" );
	}
	
	
	
	private  static function getCachedKshowList ( $config_param_name , $fallback = NULL) 
	{
		return array();
		self::init();

		// becuase the $config_param_name is unique in the config - use it in the cache too
		$cached_show_list = self::$s_cache->get ( $config_param_name );
		if ( $cached_show_list == NULL )
		{
			// read the ids from config
			
			$id_list = self::$s_config->getList ( $config_param_name );
			if ( $id_list == NULL && ! $fallback ) 
			{
				$id_list = self::$s_config->getList ( $fallback );
			}
			
			if ( $id_list == NULL )
			{
				throw new Exception ( "Cannot find featured list in app.yml (key=" . $config_param_name . ")" );
			}
			
			$c = new Criteria();
			$c->add ( kshowPeer::ID , $id_list , Criteria::IN );
			// fetch the real kshows from the DB
			$kshow_list = kshowPeer::doSelectJoinkuser( $c );

			// create stub objects with only the minimum data from the real kshow
			$cached_show_list = array();
			$temp_array = array();
			
			// the order is not the one we had in the $id_list - it's sorted by the DB
			foreach ( $kshow_list as $kshow )
			{
				$temp_array[$kshow->getId()] = new kshowStub ( $kshow ); 
			}
			
			$i = 0;
			foreach ( $id_list as $id )
			{
				if ( key_exists( $id , $temp_array) )	
				{
					$cached_show_list[$i] = $temp_array[$id];
					++$i;
				}
			}

			// store the stub list in the cache for next time
			self::$s_cache->put ( $config_param_name , $cached_show_list );
			
		}
		
		return $cached_show_list;	
	}
	

}
?>
<?php
/**
 * Uses the memcached extension,
 * This requires 2 things : 
 * 	1. in the php.ini - uncomment the extension=php_memcache.dll entry (windows)
 * 	2. run the daemon memcached
 * 
 * The memcahe object is sttic and will be used for all of the cache requests of a single request. 
 */

class myCache
{
	const STATS = "_stats_";
	const DEFAULT_EXPIRY_IN_SECONDS = 0	;

	
	private $m_namespace;
	private $m_stats ;
	private $m_expiry;
	private static $s_ready = false;
	private static $s_memcache;

	// if the cache will be used many times, a default expiry can be set to avoid setting evey time in the put method
	// this is good when most objects have the same expiry time
	public function myCache ( $namespace , $expiry = NULL )
	{
		$this->m_namespace = $namespace;
		
		if ( self::$s_memcache == NULL )
		{
			if (!function_exists('memcache_connect')) return;
						
			self::$s_memcache = new Memcache;
			//self::$s_memcache->pconnect(self::SERVER, self::PORT) // this will use a persistent connection 
			
			$res = @self::$s_memcache->connect( kConf::get ( "memcache_host") , kConf::get ( "memcache_port" ) );
			if ( !$res )
			{
				kLog::log( "ERROR: Error while trying to connect to memcache. Make sure it is properly running on " . 
					kConf::get ( "memcache_host") . ":" . kConf::get ( "memcache_port" ) );
				//throw new Exception ("Error while trying to connect to memcache. Make sure it is properly running on " . self::SERVER . ":" . self::PORT );
			}
			else
			{
				self::$s_ready = true;
			}
			
//				or die ("Error while trying to connect to memcache. Make sure it is properly running on " . self::SERVER . ":" . self::PORT );
		}

		
		if ( $expiry != null )
		{
			$this->m_expiry = $expiry;
		}
		
		if ( $this->calculateStats() )
		{
			$this->m_stats = $this->getStatsObj();
			if ( $this->m_stats == NULL )
			{
				$this->m_stats = new cacheStats ;
			}
		}
		else
		{
			$this->m_stats = new cacheStats ;
		}

	}
	
	static private function calculateStats()
	{
		return false;
	}
	
	private function getStatsObj ()
	{
//		self::$s_memcache->get ( $this->m_namespace . self::STATS ); 	
	}

	private function setStatsObj ()
	{
//		self::$s_memcache->set ( $this->m_namespace . self::STATS , $this->m_stats ); 	
	}
	
	
	public function put ( $obj_name , $obj , $expiry = NULL )
	{
		if ( ! self::$s_ready ) return;
		if ($expiry == NULL  )
		{
			$expiry = $this->m_expiry;
			
			// if it is still NULL
			if ($expiry == NULL  )
			{
				$expiry == self::DEFAULT_EXPIRY_IN_SECONDS;
			}
		}
		
		self::$s_memcache->set ( $this->m_namespace . $obj_name , $obj , false , $expiry );
		
		// TODO - maintain the linked_list
//		//$this->m_obj_container[$obj_name] = $obj;
		$this->m_stats->m_puts++;
	}
	
	public function get ( $obj_name )
	{
		if ( ! self::$s_ready ) return NULL ;
//		$this->m_stats->m_gets++;
		
		$value = self::$s_memcache->get ( $this->m_namespace . $obj_name );

		if ( !isset ( $value ) )
		{
			$this->m_stats->m_misses++;
			return NULL;
		}
		else
		{
			$this->m_stats->m_hits++;
			return $value;
		}
			
	}

	public function remove ( $obj_name )
	{
		if ( ! self::$s_ready ) return  NULL ;
		self::$s_memcache->delete ( $this->m_namespace . $obj_name );
	}

	public function increment ( $obj_name , $delta = 1 )
	{
		if ( ! self::$s_ready ) return NULL ;
		$new_value = self::$s_memcache->increment ( ( $this->m_namespace . $obj_name ) , $delta );
		
		if (  $new_value == NULL )
		{
			// this might happen if the value was never set before
			$new_value = $delta;
			$this->put ( $obj_name , $new_value );
		}
		
		return $new_value;
	}

	// if the value does not exist - zero will be used.
	// Note: New item's value will not be less than zero. 
	public function decrement ( $obj_name , $delta = 1 )
	{
		if ( ! self::$s_ready ) return NULL ;
		$new_value = self::$s_memcache->decrement ( ( $this->m_namespace . $obj_name ) , $delta );
		if ( $new_value == FALSE ) 
		{
			// this might happen if the value was never set before
			$this->put ( ( $this->m_namespace . $obj_name ) , 0 );
			$new_value = 0;
		}
		
		return $new_value;
	}
	
}

class cacheStats
{
	public $m_size = 0;
	public $m_puts = 0;
	public $m_gets = 0;
	public $m_hits = 0;
	public $m_misses = 0;
	
	function cacheStats (  )
	{
		//$this->m_gets = 0;
	}
	
	function toString()
	{
		var_dump( $this );
	}
	
	
}
?>
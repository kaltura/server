<?php
/**
 * Uses the memcached extension,
 * This requires 2 things : 
 * 	1. in the php.ini - uncomment the extension=php_memcache.dll entry (windows)
 * 	2. run the daemon memcached
 * 
 * The memcahe object is sttic and will be used for all of the cache requests of a single request.
 * 
 * @package server-infra
 * @subpackage cache
 */
class myCache
{
	const DEFAULT_EXPIRY_IN_SECONDS = 0	;
	
	private $m_namespace;
	private $m_expiry;
	private $m_cache;

	// if the cache will be used many times, a default expiry can be set to avoid setting evey time in the put method
	// this is good when most objects have the same expiry time
	public function __construct ( $namespace , $expiry = NULL )
	{
		$this->m_namespace = $namespace;
		$this->m_expiry = $expiry ? $expiry : self::DEFAULT_EXPIRY_IN_SECONDS;
		$this->m_cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_PS2);
	}
		
	public function put ( $obj_name , $obj , $expiry = NULL )
	{
		if ( ! $this->m_cache ) return;
		
		if ($expiry == NULL  )
		{
			$expiry = $this->m_expiry;
		}
		$this->m_cache->set ($this->m_namespace . $obj_name , $obj , $expiry);
	}
	
	public function get ( $obj_name )
	{
		if ( ! $this->m_cache ) return NULL;

		kApiCache::disableConditionalCache();
		
		$value = $this->m_cache->get ( $this->m_namespace . $obj_name );
		if ( !isset ( $value ) )
		{
			return NULL;
		}
		return $value;
	}

	public function remove ( $obj_name )
	{
		if ( ! $this->m_cache ) return  NULL ;
		
		$this->m_cache->delete ( $this->m_namespace . $obj_name );
	}

	public function increment ( $obj_name , $delta = 1 )
	{
		if ( ! $this->m_cache ) return  NULL ;
		
		$new_value = $this->m_cache->increment ( ( $this->m_namespace . $obj_name ) , $delta );
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
		if ( ! $this->m_cache ) return  NULL ;
		
		$new_value = $this->m_cache->decrement ( ( $this->m_namespace . $obj_name ) , $delta );
		if ( $new_value == FALSE ) 
		{
			// this might happen if the value was never set before
			$this->put ( ( $this->m_namespace . $obj_name ) , 0 );
			$new_value = 0;
		}
		
		return $new_value;
	}
}

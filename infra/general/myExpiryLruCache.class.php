<?php
/**
 * implementation of LRU cache where the cached objects can expire.
 * when using the 'get' function - if an object exists but expires, it will not appear (as if not in cache).
 * if you want to know if an object is expires but still is held in the cache - use 'exists ( $obj_name )'
 */

class myExpiryLruCache 
{
	private $m_cache ;
	private $m_expiry_period_milliseconds;
	private $m_touch_policy_on_get; // renew object on get or only on put ?
	
	public function myExpiryLruCache ( $name , $max_size , $expiry_period_milliseconds , $touch_policy_on_get )
	{
		$this->m_cache = new myLruCache( $name , $max_size );
		$this->m_expiry_period_milliseconds = $expiry_period_milliseconds;
		$this->m_touch_policy_on_get = $touch_policy_on_get;
	}
	
	public function put ( $obj_name , $obj , $expiry_period = -1 )
	{
		
		$this->m_cache->put ( $obj_name , new proxyValue ( $obj , 
			( $expiry_period > 0 ? $expiry_period : $this->m_expiry_period_milliseconds ) ));
	}
	
	public function get ( $obj_name , $expiry_period = -1 )
	{
		$proxy_value = $this->m_cache->get ( $obj_name );
		
		if ( $proxy_value == NULL )
		{
			return NULL;
		}
		else
		{
			// object exists - check if it has expired + maintain the touch_policy
			if ( $proxy_value->expired () )
			{
				return NULL;
			}
			else
			{
				// obj has not yet expired
				if ( $m_touch_policy_on_get )
				{
					$proxy_value->renew( ( $expiry_period > 0 ? $expiry_period : $this->m_expiry_period_milliseconds ) );
				}
				return $proxy_value->getValue();
			}
		}
			
	}
	
	public function exists ( $obj_name )
	{
		return ( $this->m_cache->get ( $obj_name ) != NULL );
	}
	
}

class proxyValue 
{
	private $m_obj ;
	private $m_expired_time; // will hold the time (in milliseconds) when the object expires
	function proxyValue ( $obj , $expiry_period )
	{
		$this->m_obj = $obj;
		$this->m_expired_time = time() + $expiry_period;
	}
	
	function getValue ( )
	{
		return $m_obj; 
	}
	
	function expired()
	{
		return time() > $this->m_expired_time;	
	}
	
	function renew ( $expiry_period ) 
	{
		$this->m_expired_time = time() + $expiry_period;
	}
}
?>
<?php
/**
 * @package infra
 * @subpackage cache
 */
class myProcessCache
{
	private $m_namespace;
	private $m_stats ;
	private $m_expiry;

	/**
	 * This cache is based on a local cache (wrapped by sfProcessCache) which stores data in the php process.
	 * Unlike memcache, it cannot be distributed.
	 */
	public function myCache ( $namespace , $expiry = NULL )
	{
		$this->m_namespace = $namespace;
		
		if ( $expiry != null )
		{
			$this->m_expiry = $expiry;
		}
		else
		{
			$this->m_expiry =  DEFAULT_EXPIRY_IN_SECONDS ;
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
		return true;
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
		if ( $expiry == NULL )
		{
			$expiry = $this->m_expiry;
		}
		sfProcessCache::set( $this->m_namespace . $obj_name, $obj, $expiry);

		// TODO - maintain the linked_list
//		//$this->m_obj_container[$obj_name] = $obj;
		$this->m_stats->m_puts++;
	}
	
	public function get ( $obj_name )
	{
		$this->m_stats->m_gets++;

		$value = sfProcessCache::get($this->m_namespace . $obj_name);

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
	
	public function clear	 ( )
	{
		sfProcessCache::clear();
	}
}

?>
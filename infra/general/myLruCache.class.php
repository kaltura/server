<?php
/**
 * implementation of a cache that uses the LRU algorithm when number of cached objects exceed the 
 * allowed size.
 */

// TODO - implement the LRU algorithm !!
class myLruCache
{
	private $m_max_size = 100; // SOME defaulat value
	private $m_obj_container = array ();
	private $m_linked_list;
	private $m_name ;
	private $m_stats ;
	
	private static $s_cache_list = array();
	
	public function myLruCache ( $name , $max_size )
	{
		$this->m_max_size = $max_size;
		$this->m_name = $name;
		
		self::$s_cache_list[$name] = & $this;
		$this->m_stats = new myLruCache_cacheStats( $max_size );
		
	
		self::getCacheList();
	}
	
	public function put ( $obj_name , $obj )
	{
		// TODO - maintain the linked_list
//		//$this->m_obj_container[$obj_name] = $obj;
		$this->m_stats->m_puts++;
	}
	
	public function get ( $obj_name )
	{
		$this->m_stats->m_gets++;
		// TODO - maintain the linked_list
		@($value = $this->m_obj_container[$obj_name]); // suppress error !
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
		unset ( $this->m_obj_container[$obj_name] );
	}
	
	public function clearAll()
	{
		// TODO - is this the corerct way to release the reference to the cotainer and start a new one ?
		$this->m_obj_container = array();
	}
	
	
	public static function getCacheList()
	{
		return array_keys( self::$s_cache_list );
	}
	
	public static function getCacheStats( $name )
	{
		return self::$s_cache_list[$name]->m_stats;
	}

	
}

class myLruCache_cacheStats
{
	public $m_size = 0;
	public $m_puts = 0;
	public $m_gets = 0;
	public $m_hits = 0;
	public $m_misses = 0;
	
	function myLruCache_cacheStats ( $size )
	{
		$this->m_size = $size ;
		//$this->m_gets = 0;
	}
	
	function toString()
	{
		var_dump( $this );
	}
	
	
}
?>
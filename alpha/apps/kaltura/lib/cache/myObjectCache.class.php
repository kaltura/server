<?php
/**
 * @package server-infra
 * @subpackage cache
 */
class myObjectCache
{
	private static $s_cache ;
	private static $s_memory_cache;  // even faster cache - local in memeory
	
	private $m_expiry_in_seconds;
	
	public function __construct( $expiry_in_seconds = 1000 )
	{
		if ( self::$s_cache == null )
		{
			 self::$s_cache = new myCache ( "objCache" , $expiry_in_seconds );
			 self::$s_memory_cache = new memoryCache ( );
		}
		
		$this->m_expiry_in_seconds = $expiry_in_seconds;
	}

	// in the case of the array - do 2 things : 
	// 1. store each object in the cache
	// 2. store the array of id's in the cache associated    
	public function putArray ($parent , $field_name , $arr )
	{
		if ( $arr == null ) return;
		
		if ( !is_array ( $arr ) ) return ;
		
		// empty arrays are sometimes worth caching !!
//		if ( count ( $arr ) == 0 ) return ;

		$id_list = array();
		$obj_clazz = null;
		$i = 0 ;
		// in the first place in the array - set the class of the ineer objects 
		foreach  ( $arr as $obj )
		{
			if ( $obj_clazz == null ) $obj_clazz = get_class ( $obj );
			self::put ( $obj );
			if ( $i == 0 )	$id_list[] =  $obj->getId() . "," . $obj_clazz ;
			else $id_list[] = $obj->getId() ;
			$i++;
		}

		$parnet_clazz = get_class ( $parent );
		
		$id = $parent->getId();


		$key = $parnet_clazz . "_" . $id . "_arr_$field_name";
		
		KalturaLog::info (  __CLASS__ . ":putArray: $key" );
//		echo "putArray:" . $key . "(" . count ( $arr ) . ")\n" ;
		
		self::$s_memory_cache->put ( $key , $id_list , $this->m_expiry_in_seconds );
		self::$s_cache->put ( $key , $id_list , $this->m_expiry_in_seconds);
	}
	
	public function getArray ($parent , $field_name )
	{
		$obj_clazz = get_class ( $parent );
		$id = $parent->getId();
		$key = $obj_clazz . "_" . $id . "_arr_$field_name";
		$res = @ self::$s_memory_cache->get ( $key );
		if ( $res == null ) $res = self::$s_cache->get ( $key );
		
		if ( $res == null ) return null;

		$obj_clazz = null;
		// now attempt to re-create the array:
		$obj_array = array();
		foreach  ( $res as $id )
		{
			if ( $obj_clazz == null ) 
			{
				$params = explode ( "," , $id );
				$obj_clazz = $params[1];
				$id = $params[0];
			}
			
			$obj_for_id = self::get ( $obj_clazz , $id );
			
			// if even one obj is missing - the array is worthless - there is no generic way to retrive the missing objects from the DB 
			if ( $obj_for_id == null ) return null;
			$obj_array[] = $obj_for_id;
		}
		
		return $obj_array;
	}
	
	public function removeArray ( $parnet , $field_name , $id=null )
	{
		
		if ( is_object( $parnet ))
		{
			$obj_clazz = get_class ( $parnet );
			$id = $parnet->getId();
		}
		else
		{
			$obj_clazz = $parnet;
			// in this case the caller must supply an id
		}

		$key = $obj_clazz . "_" . $id . "_arr_$field_name";
		KalturaLog::info (  __CLASS__ . ":removeArray: $key" );
		
		 
		self::$s_memory_cache->remove ( $key );
		self::$s_cache->remove ( $key );		
	}
	
		
	// ASSUMES the object has a field called $id
	public function put ( $obj , $cache_key_field = null )
	{
		if ( $obj == null || is_array ( $obj ) ) return;
		
		$obj_clazz = get_class ( $obj );
		
		if ( $cache_key_field == null )
		{
			$id = $obj->getId();
		}
		else
		{
			$getter_func_name = "get{$cache_key_field}";
			$id = "{$cache_key_field}_" . $obj->$getter_func_name();
		}

		$key = $obj_clazz . "_" . $id;
		
		// make the memory cache much shorter - for whn using it in batch processes. the memcahce can be cleared easily
		self::$s_memory_cache->put ( $key , $obj , (int)($this->m_expiry_in_seconds /10) );  
		self::$s_cache->put ( $key , $obj , $this->m_expiry_in_seconds);
	}

	public function putValue ( $obj_clazz , $id , $cache_key_field , $value )
	{
		if ( $cache_key_field != null )
		{
			$id = "{$cache_key_field}_" . $id;
		}
				
		$key = $obj_clazz . "_" . $id;
		
		self::$s_memory_cache->put ( $key , $value , $this->m_expiry_in_seconds );
		self::$s_cache->put ( $key , $value , $this->m_expiry_in_seconds );
	}
	
	public function get ( $obj_clazz , $id , $cache_key_field = null )
	{
		if ( $cache_key_field == null )
		{
			$key = $obj_clazz . "_" . $id;
		}
		else
		{
			$key = $obj_clazz . "_" . "{$cache_key_field}_" . $id;
		}

		$res = self::$s_memory_cache->get ( $key );
		if ( $res != null ) 
		{
			return $res;
		}
		
		$res = self::$s_cache->get ( $key );
		if ( $res != null ) 
		{
			self::$s_memory_cache->put ( $key , $res , $this->m_expiry_in_seconds );
			return $res;
		}

		return null;
	}
	
	public function remove ( $obj_clazz , $id )
	{
		$key = $obj_clazz . "_" . $id;
		self::$s_memory_cache->remove ( $key );
		self::$s_cache->remove ( $key );		
	}

}

class memoryCache
{
	private $m_expiry; // in seconds
	private static $s_map = array();
	function __construct ( $expiry = null )
	{
		$this->m_expiry = $expiry; 
	}
	
	function put ( $name , $value , $expiry  )
	{
		$expiry = $expiry-3;
		
		$expired_time = time() + $expiry ;
		self::$s_map[$name] = array ( $expired_time , $value );
	}
	
	function get ( $name )
	{
		
		if ( !isset (self::$s_map[$name])) return null;
		$value_arr = @self::$s_map[$name];
		if ( $value_arr )
		{
			$expired_time = $value_arr[0];
			
			$now = time();
			if ( $expired_time >= $now  )
				return $value_arr[1];
		}
		return null;
	}
	
	function remove ( $name )
	{
		unset ( self::$s_map[$name] );
	}
}



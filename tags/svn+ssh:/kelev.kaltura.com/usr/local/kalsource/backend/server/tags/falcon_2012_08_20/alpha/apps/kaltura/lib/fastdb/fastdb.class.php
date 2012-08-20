<?php
// fast DB functions
class fdb
{
	private static $last_class_name;
	private static $last_criteria_str;
	private static $last_join_with;
	private static $last_query_time ;
	
	private static $timer_start;
	
	// dupliace a row in the DB and give it the desired new ID.
	// If the ID already exists - give error
	public static function dup ( $class_name , $old_id , $new_id )
	{
		$obj = self::select ( $class_name , "id={$old_id}" );
		if ( ! $obj )
		{
			throw new Exception ( __CLASS__ . ": error! did not find [{$class_name}] with {$old_id} to duplicate" ); 
		}
		
		$temp_obj = self::select ( $class_name , "id={$new_id}" );
		if ( $temp_obj )
		{
			throw new Exception ( __CLASS__ . ": error! [{$class_name}] with {$new_id} already exists in db. cannot duplicate" ); 
		}
		
		$new_obj = $obj->copy();
		$new_obj->save();
		$stored_id = $new_obj->getId();
		
//		echo "stored_id: $stored_id\n";
		
		$db_connection = Propel::getConnection();
		$db_connection->begin();
		$db_connection->executeUpdate ( "UPDATE {$class_name} set id={$new_id} WHERE id={$stored_id};" );
		$db_connection->commit();
		//$db_connection->close();
		
		Propel::close();

	}

	// print the objects in a table manner displaying the selected columns
	public static function dump ( $objs , $columns )
	{
		 if ( $objs == null ) return "";
		 if ( is_array ( $objs ) )
		 {
		 	foreach ( $objs as $obj )
		 	{
		 		self::dumpImpl ( $obj , $columns );
		 	}
		 }
		 else
		 {
		 	self::dumpImpl ( $objs , $columns );
		 }
	}
	
	
	private static function dumpImpl ( $obj , $columns )
	{

	}
	
	public static function getLastQueryTime( )
	{
		return self::$last_query_time;
	}
	
	public static function select ( $class_name = null , $criteria_str = null )
	{
		return self::impl ( $class_name , $criteria_str , "doSelect" );	
	}

	public static function selectjoin ( $class_name = null , $criteria_str = null  , $join_with = null )
	{
		if ( $join_with == null ) $join_with = self::$last_join_with ;
		else self::$last_join_with = $join_with;
		
		return self::impl ( $class_name , $criteria_str , "doSelectJoin" . $join_with );	
	}
	
	public static function count ( $class_name = null , $criteria_str = null)
	{
		$res =  self::impl ( $class_name , $criteria_str , "doCount" );
		if ( empty ( $res ) )
			return '0';
		return $res;	
	}

	public static function countjoin ( $class_name = null , $criteria_str = null , $join_with = null )
	{
		if ( $join_with == null ) $join_with = self::$last_join_with ;
		else self::$last_join_with = $join_with;
		
		return self::impl ( $class_name , $criteria_str , "doCountJoin" . $join_with);	
	}
	
	public static function selectcount ( $class_name = null, $criteria_str = null)
	{
		$sel = self::select ( $class_name , $criteria_str );
		$cnt = self::count ( $class_name , $criteria_str );
		return array ( "select" => $sel , "count" => $cnt );	
	}

	public static function delete ( $class_name , $criteria_str )
	{
		return self::impl ( $class_name , $criteria_str , "doDelete" );	
	}
	
	protected  static function impl ( $class_name = null , $criteria_str = null , $func_name )
	{
		$timer_start = microtime ( true );
		
		if ( $class_name == null ) $class_name = self::$last_class_name ;
		if ( $criteria_str == null ) $criteria_str = self::$last_criteria_str ;

		if ( empty ( $class_name ) ) //|| empty ( $criteria_str ) )
		{
			throw new Exception ( __CLASS__ . ": error! cannot execute query for empty class [{$class_name}] or empty criteria [{$criteria_str}]");	
		}
		else
		{
			self::$last_class_name = $class_name ;
			self::$last_criteria_str = $criteria_str ;
		}
		
		
		$peer_clazz_name = $class_name . "Peer";
		$timer_start = microtime ( true );
		$peer_clazz = new $peer_clazz_name();

		$criteria = self::createCriteria ( $peer_clazz , $criteria_str );

$timer_start = microtime ( true );
		$res = $peer_clazz->$func_name ( $criteria );
$timer_end = microtime ( true );		
		if ( $res == null ) {}
		elseif ( is_array ( $res ) && count ( $res ) == 1 )
		{
			// return the only element in the array
			$res = $res[0];
		}
			
//		$timer_end = microtime ( true );
		
		self::$last_query_time = ( $timer_end - $timer_start );
		
		return $res;
	}


	protected static function createCriteria ( $peer_clazz , $criteria_str )
	{
		$param_name_format =  BasePeer::TYPE_FIELDNAME;
		$criteria_arr = explode ( "," , $criteria_str );
/*		if ( count ( $criteria_arr ) <= 0 )
			throw new Exception ( __CLASS__ . ": error while creating criteria for {$peer_clazz}:{$criteria_str}");
*/
		$db_param_name = $peer_clazz->translateFieldName( "id" , $param_name_format , BasePeer::TYPE_COLNAME );
		$criteria = new Criteria();
		$criteria->add ( $db_param_name , $criteria_str , Criteria::CUSTOM );
		
		return $criteria;
/*		
		foreach ( $criteria_arr as $criterion )
		{
			$criterion = trim ( $criterion );
			if ( empty ( $criterion ) ) continue;
			// TODO - allow operators = < > <= => <> != 
			list ( $param , $value ) = explode ( "=" , $criterion );
			
			$db_param_name = $peer_clazz->translateFieldName( $param , $param_name_format , BasePeer::TYPE_COLNAME );
			if ( $value[0] == '%' || $value[strlen($value)-1] == '%' )
			{
				$criteria->addAnd ( $db_param_name , $value , Criteria::LIKE  );	
			}
			else
			{
				$criteria->addAnd ( $db_param_name , $value );	
			}
			
		}
			
		return $criteria;
*/
	}
	
	
	/**
	 * This helper function populates fields in the container_objects in a single query rather than retriving objects one-by-one 
	 * implicitly due to getters of the container
	 */
	public static function populateObjects ( &$container_objects , $retrieve_peer , $retrieve_id_method , $retrieve_obj_method , 
		$query_before_fetch = false  , $obj_id_method = null )
	{
		$ids_to_retrieve = array();
		foreach ( $container_objects as $container_obj )
		{
			$add_id = true;
			if ( $query_before_fetch )
			{
				$getter_method = "get$retrieve_obj_method";
				// see if container object already has the target object
				$target_obj = call_user_func(  array ( $container_obj , $getter_method ) );
				$add_id = ( $target_obj === null );
			}
			
			if ( $add_id )
			{
				// add the id to the required list
				$getter_method = "get$retrieve_id_method";
				$ids_to_retrieve[] = call_user_func(  array ( $container_obj , $getter_method ) );
			}
		}

		if ( count ( $ids_to_retrieve ) > 0 )
		{
			$target_objects = $retrieve_peer->retrieveByPks ( $ids_to_retrieve );
			foreach ( $container_objects as $container_obj )
			{
				$getter_method = "get$retrieve_id_method";
				$id = call_user_func(  array ( $container_obj , $getter_method ) );
				
				foreach ( $target_objects as $target_obj )
				{
					if ( $obj_id_method == null )
					{
						$equal = ( $id == $target_obj->getId() ); 
					}
					else
					{
						$target_getter_method = "get$obj_id_method";
						$equal = ( $id == call_user_func(  array ( $target_obj , $target_getter_method ) ) );
					}
					if (  $equal )
					{
						// set this target_object in the container 
						$setter_method = "set$retrieve_obj_method";
						
						$id = call_user_func(  array ( $container_obj , $setter_method ) , $target_obj );
						break;						
					}
				}
			}			
		}
	}
	
	
}
?>
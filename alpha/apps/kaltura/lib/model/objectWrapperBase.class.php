<?php

abstract class objectWrapperBase implements Iterator
{
	const DETAIL_LEVEL_BASIC = 1;
	const DETAIL_LEVEL_REGULAR = 4;
	const DETAIL_LEVEL_DETAILED = 7;

	const DETAIL_VELOCITY_DEFAULT = -3;

	private static $use_cache = true;

	protected $detail_level = self::DETAIL_LEVEL_REGULAR ;
	protected $recursion_depth = 0 ;
	protected $detail_policy_velocity = -1;

	private $m_obj;
	private $m_is_array;
	private $m_recursive_wrapping ;

	protected $basic_fields = array ( "id" );
	protected $regular_fields_ext = null;
	protected $detailed_fields_ext = null;
	protected $detailed_objs_ext = null;
	protected $objs_cache = null;
	protected $read_only_fields = null;

	protected static $accumulated_regular_fields = null;
	protected static $accumulated_detailed_fields = null;
	protected static $accumulated_updateable_fields = null;

	private static $s_cache;

	public $fields;

	private static $s_should_wrap = true;

	public static function shouldWrap ( $v )
	{
		self::$s_should_wrap = $v;
	}

	public static function classForName ( $clazz_name )
	{
		echo __METHOD__ . " $clazz_name";
		require_once  ( "lib/model/" . $clazz_name . ".php" );
		$clazz = get_class( $clazz_name );
		return  new $clazz();
	}

	// will return the proper object wrapper
	public static function getWrapperClass ( $obj , $detail_level = self::DETAIL_LEVEL_REGULAR ,
		$detail_policy_velocity = -3 , $recursion_depth = 0 , $extra_fields = null)
	{
		if ( self::$s_should_wrap == false ) return $obj;

		if ( $obj == null )
		{
			//			echo "NULL OBJECT!<br>";
			return null;
		}

		$class = is_array ( $obj ) ? "Array (" . count ( $obj ) . ")" : get_class($obj) ;

		if ( is_array ( $obj ) )
		{
			$obj_arr = array();
			foreach ( $obj as $id => $element_in_arr )
			{
				if ( ! is_object( $element_in_arr ) )
				{
					$obj_arr[$id] = $element_in_arr;
					continue;
				}
				// stay with the same $recursion_depth
				$obj_arr[] = self::getWrapperClass ( $element_in_arr , $detail_level , $detail_policy_velocity , $recursion_depth , $extra_fields );
			}

			// TRICKY !!
			// if this is a call of depth=0 - thre return value should be $obj_arr ONLY.
			if ( $recursion_depth == 0 )
			{
				return $obj_arr;
			}

			// the reason we return this array of 2 is for internal implementation caching
			return array ( $obj_arr , $obj );
		}

		$wrapper_clazz = get_class($obj). "Wrapper";

//				echo $wrapper_clazz . "[$detail_level]\n";

		try
		{
			//echo $wrapper_clazz . " [$detail_level/$detail_policy_velocity]<br>";

			// for all wrappers - class file is local
			// for plugin backward-compatibility support in PS2 actions - file can be elsewhere
			// if file does not exist - simply try to instantiate object (through autoloader)
			// this is fully backward compatible - if file didn't exist before plugin - FATAL would happen
			// if we try to instantiate a non-existing class - FATAL would happen
			if(file_exists($wrapper_clazz . ".class.php"))
				require_once  ( $wrapper_clazz . ".class.php" );

			// try envoking the ctor of the wrapper class
			$wrapper = new $wrapper_clazz( $obj ,
				true , /*$recursive_wrapping ,*/
				$detail_level + $detail_policy_velocity ,
				++$recursion_depth ,
				$detail_policy_velocity ) ;

			$wrapper->fields = null;

			if ( self::DETAIL_LEVEL_DETAILED <= $detail_level )			$wrapper->fields = $wrapper->getDetailedFields();
			else if ( self::DETAIL_LEVEL_REGULAR <= $detail_level )		$wrapper->fields = $wrapper->getRegularFields();
			else if ( self::DETAIL_LEVEL_BASIC <= $detail_level )		$wrapper->fields = $wrapper->getBasicFields();

			if ( $extra_fields != null )
			{
				// allow the fields to be the standard-set (depending on the LEVEL) and the extra_fields 
				$wrapper->fields = self::combineFields ( $wrapper , $extra_fields );
			}

			return $wrapper;
		}
		catch ( Exception $ex )
		{
			echo ( "BAD! " . $ex->getMessage() );
		}
	}

	// TODO - warn or throw an exception in case of attempting to access invalid fields ??
	private static function combineFields ( $wrapper , $extra_fields )
	{
		// make sure all the extra_fields are a subset of the biggest field group (getAllPresentableFields)
		$all_fields = $wrapper->getAllPresentableFields ();
		
		$allowed_extra_fields = array_intersect( $all_fields , $extra_fields );
		
		return array_merge( $wrapper->fields , $allowed_extra_fields );
	}
	
	public static function useCache ( $use )
	{
		self::$use_cache = $use;
	}

	public function getWrappedObj ()
	{
		return $this->m_obj;
	}

	public function addFields ( $fields )
	{
		$basic = $this->getBasicFields();
		array_merge( $basic , $fields );
	}

	public function & getBasicFields()
	{
		return $this->basic_fields;
	}


	public function getRegularFields()
	{
	//	return kArray::append ( $this->basic_fields , $this->regular_fields_ext );
		return array_merge ( $this->basic_fields , $this->regular_fields_ext );
/*
		if ( self::$accumulated_regular_fields == null )
		{
			self::$accumulated_regular_fields = kArray::append ( $this->basic_fields , $this->regular_fields_ext );
		}

		return self::$accumulated_regular_fields;
*/
	}

	public function getDetailedFields()
	{
		return kArray::append ( $this->basic_fields , $this->regular_fields_ext , $this->detailed_fields_ext , $this->detailed_objs_ext );
/*		if ( self::$accumulated_detailed_fields == null )
		{
			self::$accumulated_detailed_fields = kArray::append ( $this->basic_fields , $this->regular_fields_ext , $this->detailed_fields_ext , $this->detailed_objs_ext );
		}
		return self::$accumulated_detailed_fields;
*/
	}


	public function getAllPresentableFields ()
	{
		return $this->getDetailedFields();	
	}
	
	public function getUpdateableFields()
	{
		return kArray::append ( $this->basic_fields , $this->regular_fields_ext , $this->detailed_fields_ext  ); // leave out the objects !
/*		if ( self::$accumulated_updateable_fields == null )
		{
			self::$accumulated_updateable_fields = kArray::append ( $this->basic_fields , $this->regular_fields_ext , $this->detailed_fields_ext  ); // leave out the objects !
			// TODO - remove all the read only fields
		}
		return self::$accumulated_updateable_fields;
*/
	}

	public function getObjectTypes ( )
	{
		return $this->objs_cache;
	}

	protected  function objectWrapperBase ( $object , $recursive_wrapping = false ,
		$detail_level = self::DETAIL_LEVEL_REGULAR , $recursion_depth = 0 , $detail_policy_velocity = -1 )
	{
		if ( $object == null || $detail_level <= 0 ) return $this;

		if ( self::$s_cache == null ) self::$s_cache = new myObjectCache();

		$this->m_obj = $object;
		$this->m_is_array = is_array( $object );
		$this->m_recursive_wrapping = $recursive_wrapping;

		$this->detail_level = $detail_level ;
		$this->recursion_depth = $recursion_depth ;
		$this->detail_policy_velocity = $detail_policy_velocity ;

		/*		if ( $this->m_is_array )
		 {
			reset ( $this->m_obj );
			}*/
	}

	public function getFieldNames()
	{
		return $this->fields;
	}

	public function toArray ( )
	{
		return 	self::toArrayImpl ( $this );
	}

	public static function toArrayImpl ( $obj )
	{
		if ( $obj instanceof objectWrapperBase )
		{
			$fields = $obj->getFieldNames();

			if ( $fields == null )
				return null;
			$arr_fields = array();
			$i=0;
			foreach( $fields as $key ) // subnode
			{

				if ( empty ( $key ) ) continue;
				$val = $obj->get ( $key );

				if ( empty ( $val ) ) continue;
				$arr_fields[$key] = self::toArrayImpl ( $val )	;
			}

			return $arr_fields;
		}
		elseif ( is_array ( $obj ) )
		{
			$arr_fields = array();

			// assume this array is a list NOT a map
			foreach( $obj as $key=>$val ) // subnode
			{
//				echo  "cls:[" . get_class ( $key ) . "]";
				// create a list - not a map
				if ( empty ( $val ) ) continue;

//				$key = (is_numeric($key)? "_num"  . $key : $key);
				//$val = $obj[$key];
				$arr_fields[$key] = self::toArrayImpl ( $val )	;
			}

			return $arr_fields;
		}
		else
		{
			return $obj;
		}
	}


	public function getEnumMap ( $enum_name )
	{
		if ( $this->m_obj == null ) return null;
		return $this->m_obj->getEnumMap( $enum_name );
	}

		public function get ($field_name )
		{
			return $this->__get ( $field_name );
		}

		public function __get ( $field_name )
		{
			//		sfLogger::getInstance()->log ( "__get [" . get_class ( $this ) . "/$field_name]"  );

			//		echo ("__get:$field_name<br>");
			// before envoking the method - something that can cause a hit to the DB -
			// check if object is in object cache
			$res = ( self::$use_cache ? $this->fetchFromCache( $field_name ) : null );

			$from_cache = false;
			if ( $res == null )
			{
				try
				{
					$res = myBaseObject::envokeMethod ( $this->m_obj , $field_name );
				}
				catch ( Exception $ex )
				{
					$res = "Error{$field_name}";
				}
			}
			else
			{
				$from_cache = true;
			}

			if ( $this->m_recursive_wrapping )
			{
				// wrap non-primative properties
				if ( is_object($res ) )
				{
					$val = self::getWrapperClass ( $res , $this->detail_level ,  $this->detail_policy_velocity , $this->recursion_depth );

					if ( self::$use_cache && !$from_cache )
					{
						//					echo "putting object in cache $field_name, [" . get_class ( $val ) . "]\n";
						if ( $val ) $this->putInCache( $field_name , $val->getWrappedObj() );
					}
					return $val;
				}
				else if ( is_array( $res ) )
				{
					list ( $val , $original_arr ) = self::getWrapperClass ( $res , $this->detail_level ,  $this->detail_policy_velocity , $this->recursion_depth );

					if ( self::$use_cache && !$from_cache )
					{
						//					echo "putting in array cache $field_name, [" . get_class ( $val ) . "]\n";
						if ( $val ) $this->putInCache( $field_name , $original_arr );
					}
					return $val;

				}

				return $res;
			}
			else
			{
				return $res;
			}
		}

		private function fetchFromCache ( $field_name )
		{
			if ( $this->objs_cache == null ) return null;
			if ( array_key_exists( $field_name ,  $this->objs_cache ) )
			{
				//			echo get_class ( $this );

				$cache_params = 	 $this->objs_cache[$field_name];
				// will be in the format of <object_class>,<field_id>
				$arr = explode ( "," ,  $cache_params );
				$obj_clazz = $arr[0];
				$obj_id_field = $arr[1];
				$id = $this->get ( $obj_id_field );

				// this indicates the field is an array of objects
				if ( kString::beginsWith( $obj_clazz , "*" ) )
				{
					//				echo "\nfetchFromCache [$field_name] $obj_clazz\n";
					$obj_clazz = substr ( $obj_clazz , 1 );
					$obj_from_cache = self::$s_cache->getArray ( $this->m_obj , $field_name );
				}
				else
				{
					$obj_from_cache = self::$s_cache->get ($obj_clazz,$id);
				}

				/*
				 if ( $obj_from_cache )
				 {
				 echo ( "\nfound $cache_params [$id]\n");
				 }
				 else
				 {
				 echo ( "\n" . $cache_params . " [$id] not found\n");
				 }
				 */
				return $obj_from_cache;
			}
			else
			{
				//			sfLogger::getInstance()->log ( "Not supposed to search in cache [" . get_class ( $this ) . "/$field_name]"  );

				return null;
			}
		}

		private function putInCache ( $field_name , $val )
		{
			if ( $this->objs_cache == null ) return null;

			if ( array_key_exists( $field_name ,  $this->objs_cache ) )
			{
				//			$cache_params = 	 $this->objs_cache[$field_name];
				// will be in the format of <object_class>,<field_id>
				//			$arr = explode ( "," ,  $cache_params );

				//			echo "putInCache: $field_name: " . print_r ( $arr , true );

				/*			$obj_clazz = $arr[0];
				 $obj_id_field = $arr[1];
				 $id = $this->get ( $obj_id_field );
				 */
				if ( is_array ( $val ) )
				{
					//				echo "putInCache: $field_name (" . count ($val) . ")";
					self::$s_cache->putArray ( $this->m_obj , $field_name ,  $val );
				}
				else
				{
					self::$s_cache->put ( $val );
				}
			}
		}

		public function removeFromCache ( $field_name , $id=null , $child_field_name=null )
		{
			if ( $this->objs_cache == null ) return ;
			if ( $child_field_name )
			{
				// this means that we are setting an array within an object
				 self::$s_cache->removeArray ( $field_name , $child_field_name , $id);
			}
			else
			{
				 self::$s_cache->remove ( $field_name , $id );
			}
		}

		public function isArray()
		{
			return $this->m_is_array;
		}

		public function rewind()
		{
			if ( $this->m_obj == null ) return;
			//		echo "rewind\n";
			$this->verifyIterator();
			reset($this->m_obj);
		}

		public function current()
		{
			//echo "current\n";
			$this->verifyIterator();
			return self::getWrapperClass ( current($this->m_obj) , $this->detail_level , $this->recursion_depth , $this->detail_policy_velocity );
		}

		public function key()
		{
			//		echo "key\n";
			$this->verifyIterator();
			return  key($this->m_obj);
		}

		public function next()
		{
			//	echo "next\n";
			$this->verifyIterator();
			return self::getWrapperClass ( next($this->m_obj) , $this->detail_level , $this->recursion_depth , $this->detail_policy_velocity );
		}

		public function valid()
		{
			if ( $this->m_obj == null ) return false;
			//		echo "valid\n
			$this->verifyIterator();
			return current($this->m_obj) !== false;
		}

		protected function verifyIterator()
		{
			if ( $this->m_obj != null &&  ! $this->m_is_array ) throw new Exception ( "Cannot iterate an objec that is not an array" );

		}
		
		public static function parseString ( $str , $obj )
		{
			$pattern = "|{([a-zA-Z0-9_\-]*)}|";
			
			preg_match_all( $pattern , $str , $matches );
			
	//		print_r ( $matches );
			 
		}
	}
	?>
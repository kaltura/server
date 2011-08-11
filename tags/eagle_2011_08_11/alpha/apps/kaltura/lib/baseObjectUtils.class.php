<?php

class baseObjectUtils
{
	const CLONE_POLICY_PREFER_EXISTING = 0;
	const CLONE_POLICY_PREFER_NEW = 1;

 
	/**
	 * Fills obj of type BaseObject with values from the request.
	 * It only uses parameters with that match the given prefix.
	 * Use instanceof to verify that indeed the object is of the correct type (poor OOP,  but we'll have to live with it!)
	 */
	/*
  * TODO - should move to lib
  * TODO - go over the code and see if the is a better way to do things -
  * I think there is plent of bad practices with how I manipulated strings and fethed stuff from the request !
  */
	static public function fillObjectFromRequest ( array $request_params , BaseObject &$obj , $prefix , $exclude_params, &$debug_str=NULL)
	{
		$prefix_len = strlen( $prefix);
		foreach ( $request_params as $param => $name )
		{
			// instanceof - very important !!
			if ( ! $obj instanceof BaseObject )
			{
				throw new Exception ( "fillObjectFromRequest should have the second parameter an object of type BaseObject" );
			}

			if ( strlen ( $request_params[$param]) == 0 ) continue;

			if ( isset ( $debug_str ) )
			{
				$debug_str = $debug_str .$param . "==" . $request_params[$param] . "[" . strpos ( $param , $prefix ) . "]<br>";
			}
			// the prefix is matched...
			$pos = strpos ( $param , $prefix );
			if ( $pos === 0 )
			{
				// the field name is the rest of the string coming after the prefix
				$param_name = substr ( $param , $prefix_len );

				// TODO - IMPORTANT: verify that the include params are indeed fields of this object.
				// not doing so will allow typos of field names folloowed by  security breachs !!!
				if ( $exclude_params != NULL && in_array ( $param_name  , $exclude_params ) )
				{
					// this param should be ignored !
//					debugUtils::DEBUG ( $debug_str , "Ignoring parameter [" . $param_name . "]");
					continue;
				}
//				debugUtils::DEBUG ( $debug_str ,  "[" . $param . "==" . $request_params[$param] . "]<br>" );
				$obj->setByName ( $param_name , $request_params[$param] , BasePeer::TYPE_FIELDNAME );
			}
		}
	}


	/**
	 * This function will take any map of name-values and set the values in obj as long as the field atarts with the given prefix & is in the fields list.
	 * Setting the value will be done using the naming_type which can be one of the 3:
	 * 	BasePeer::TYPE_FIELDNAME  (xxx_yyy) 
	 * 	BasePeer::TYPE_PHPNAME 	  (xxxYyy)	
	 * 	BasePeer::TYPE_COLNAME	  (table-name.xxx_yyy)
	 * 
	 * !! the default type is TYPE_PHPNAME NOT TYPE_FIELDNAME !!
	 * 
	 */
	// TODO - this funciton is deprecated by fillObjectFromMapOrderedByFields which does the same in a more efficient way
	static public function fillObjectFromMap ( 	array $request_params , 
												BaseObject &$obj , 
												$prefix , 
												$fields, 
												$naming_type = BasePeer::TYPE_PHPNAME , $allow_empty = false )
	{
		$fields_modified = array();
		$prefix_len = strlen( $prefix);
		foreach ( $request_params as $param => $name )
		{
			// instanceof - very important !!
			if ( ! $obj instanceof BaseObject )
			{
				throw new Exception ( "fillObjectFromMap should have the second parameter an object of type BaseObject" );
			}
			if ( strlen ( @$request_params[$param]) == 0 && (! $allow_empty ) ) continue;

			// the prefix is matched...
			$pos = strpos ( $param , $prefix );
			if ( $pos === 0 )
			{
				// the field name is the rest of the string coming after the prefix
				$param_name = substr ( $param , $prefix_len );

				$value_to_set = @$request_params[$param];
				
				// TODO - IMPORTANT: verify that the include params are indeed fields of this object.
				// not doing so will allow typos of field names folloowed by  security breachs !!!
				if ( ! in_array ( $param_name  , $fields ) )
				{
					// this param should be ignored !
					// mark this value with ^ to indicate it was not really set in the container 
					$fields_modified[$param_name] = "^$value_to_set";//"Invalid field name";
					continue;
				}

				if ( $naming_type = BasePeer::TYPE_PHPNAME  )
				{
					$func_name = "set" . $param_name;
				// 	because the name can indicate a getter that does not wrap a field of the base class, we need to envoke the method directly
					call_user_func ( array ( $obj , $func_name ) ,$value_to_set  );
				}
				else
				{
					$obj->setByName ( $param_name , $value_to_set , $naming_type );
				}
				$fields_modified[$param_name] = $value_to_set;
			}
		}
		
		return $fields_modified;
	}
	
	/**
	 * This function will take any map of name-values and set the values in obj as long as the field atarts with the given prefix & is in the fields list.
	 * Setting the value will be done using the naming_type which can be one of the 3:
	 * 	BasePeer::TYPE_FIELDNAME  (xxx_yyy) 
	 * 	BasePeer::TYPE_PHPNAME 	  (xxxYyy)	
	 * 	BasePeer::TYPE_COLNAME	  (table-name.xxx_yyy)
	 * 
	 * !! the default type is TYPE_PHPNAME NOT TYPE_FIELDNAME !!
	 * 
	 */
	static public function fillObjectFromMapOrderedByFields  ( 	array $request_params , 
												BaseObject &$obj , 
												$prefix , 
												$fields, 
												$naming_type = BasePeer::TYPE_PHPNAME , $allow_empty = false )
	{
		// instanceof - very important !!
		if ( ! $obj instanceof BaseObject )
		{
			throw new Exception ( "fillObjectFromMap should have the second parameter an object of type BaseObject" );
		}
	
		$fields_modified = array();
		$prefix_len = strlen( $prefix);
		
		$all_field_names_by_field_order = self::sortParamsByOrderInFields ( $request_params , $fields , false )	;
		

		foreach ( $all_field_names_by_field_order as $field_name  )
		{
			if ( isset( $request_params[$prefix.$field_name]))
			{
				// this field is both in the fields list and the $request_params
				$param = $prefix.$field_name; //$field_name;
				$param_name = $field_name;
			}
			else
			{
				continue; // the field is in the potencial files list but not in the request_params
			}
		
			if ( strlen ( @$request_params[$param]) == 0 && (! $allow_empty ) ) continue;

			$value_to_set = @$request_params[$param];
			
			// TODO - IMPORTANT: verify that the include params are indeed fields of this object.
			// not doing so will allow typos of field names followed by  security breachs !!!
			// this is actually redundant because the $param_name is originally from the $fields list
			if ( ! in_array ( $param_name  , $fields ) )
			{
				// this param should be ignored !
				// mark this value with ^ to indicate it was not really set in the container 
				$fields_modified[$param_name] = "^$value_to_set";//"Invalid field name";
				continue;
			}

			if ( $naming_type = BasePeer::TYPE_PHPNAME  )
			{
				$func_name = "set" . $param_name;
			// 	because the name can indicate a getter that does not wrap a field of the base class, we need to envoke the method directly
				call_user_func ( array ( $obj , $func_name ) ,$value_to_set  );
			}
			else
			{
				$obj->setByName ( $param_name , $value_to_set , $naming_type );
			}
			$fields_modified[$param_name] = $value_to_set;
			
		}
		
		return $fields_modified;
	}	
	
	// this new function will use good defaults for the fillObjectFromObject assuming the source & target objects are of the same class
	public static function autoFillObjectFromObject ( BaseObject $source_obj  , BaseObject &$target_obj , $allow_empty = false )
	{
		$obj_wrapper = objectWrapperBase::getWrapperClass( $source_obj , 0 );
		$updateable_fields = $obj_wrapper->getUpdateableFields() ;
		baseObjectUtils::fillObjectFromObject( $updateable_fields  , $source_obj , $target_obj , baseObjectUtils::CLONE_POLICY_PREFER_NEW , 
				null , BasePeer::TYPE_PHPNAME ,$allow_empty );
	}
	/**
	 * Copies fields from the source_obj to the target_obj
	 *
	 * @param array $source_field_names - the field names of the source_obj to copy into the target_obj
	 * @param BaseObject $source_obj - of type BaseObject
	 * @param BaseObject $target_obj - of type BaseObject
	 * @param unknown_type $clone_policy - can be either CLONE_POLICY_PREFER_EXISTING or CLONE_POLICY_PREFER_NEW
	 * 	If set to be CLONE_POLICY_PREFER_EXISTING, values already set in the target_obj will not be overridden by the new values of the source_obj
	 * 	even if explicity appear in the $source_field_names
	 * @param unknown_type $exclude_field_names - field names that appear here will be skipped not set in the target_obj even if appear in source_field_names.
	 * 	It would be the same as to remove these field names from source_field_names in the first place and set exclude_field_names to be NULL.
	 * @param unknown_type $debug_str
	 */
	static public function fillObjectFromObject ( array $source_field_names , BaseObject $source_obj  , BaseObject &$target_obj ,
	$clone_policy ,	$exclude_field_names,  $naming_type = BasePeer::TYPE_FIELDNAME , $allow_empty = false )
	{
		foreach ( $source_field_names as $field )
		{

			// TODO - IMPORTANT: verify that the include params are indeed fields of this object.
			// not doing so will allow typos of field names folloowed by  security breachs !!!
			if ( $exclude_field_names != NULL && in_array ( $field  , $exclude_field_names ) )
			{
				// this param should be ignored !
				continue;
			}

			$field_value =  self::getByName ( $source_obj , $field ,  $naming_type);

			if ( $clone_policy == baseObjectUtils::CLONE_POLICY_PREFER_EXISTING )
			{
				$current_value =  self::getByName ( $target_obj , $field ,  $naming_type);
				if ( $current_value != NULL )
				{
					// prefer existing - don't force the new value
					continue;
				}
			}
			else
			{
				//			echo "setting field [" . $field . "] with value [" . $field_value . "]<br>\n";
			}
			
		
			if ( $naming_type == BasePeer::TYPE_PHPNAME  )
			{
//	echo "[1]$field=$field_value<br>";
				if ( $field_value === null ) continue; // if the field was null (as opposed to an empty string) - continue
//	echo "[2]$field=$field_value<br>";				
				// now deal with empty strings - allow "0" or 0 (string or int) as real values
				if ( empty ( $field_value ) && ( $allow_empty == false ) && $field_value !== 0 && $field_value !== "0" ) continue;
				$func_name = "set" . $field;
//	echo "[3]$field=$field_value<br>";				
			// 	because the name can indicate a getter that does not wrap a field of the base class, we need to envoke the method directly
				$value = call_user_func ( array ( $target_obj , $func_name ) , $field_value );
			}
			else
			{					
				$target_obj->setByName ( $field , $field_value ,  $naming_type );
			}
		}

	}

	
	public static function getByName ( $obj , $field_name , $naming_type = BasePeer::TYPE_FIELDNAME )
	{
		if ( $naming_type == BasePeer::TYPE_PHPNAME )
		{
			$func_name = "get" . $field_name;
		// 	because the name can indicate a getter that does not wrap a field of the base class, we need to envoke the method directly
			$value = call_user_func ( array ( $obj , $func_name ) );
		}
		else
		{
			$value =  $obj->getByName ( $field_name ,  $naming_type);
		}
		
		return $value;
	}
	
	public static function setParamInObject ( BaseObject $obj , $param_name , $param_value )
	{
		if ( $obj == NULL )
		{
			return ;
		}
		if ( ! $obj instanceof BaseObject )
		{
			throw new Exception ( "getParamFromObject should have the first parameter an object of type BaseObject" );
		}
		return $obj->setByName( $param_name , $param_value , BasePeer::TYPE_FIELDNAME );
	}


	/**
	 * @param BaseObject $obj - the obj to extract the values from 
	 * @param array param_names the list of fields to add as attributes to the xml node.
	 * @param array $map_to_add - by default NULL - added to the output array as name=>value pairs  
	 * each element in the array can either be a field name or a pair of name=>alias.
	 * if name - the name will be used as the name in the output.
	 * if name=>alias - the 'alias' will be used as the name in the output instead. 
	 * @return array - associative array with all the requested names and their values from the object 
	 */
	public static function getByNames (  BaseObject $obj , array $param_names , $map_to_add = NULL , $invoke_method = true , $param_names_camelback = false )
	{
		if ( $obj == NULL )
		{
			return NULL;
		}
		if ( ! $obj instanceof BaseObject )
		{
			throw new Exception ( "getByNames should have the first parameter an object of type BaseObject" );
		}
		
		$res = array ();
		
		foreach ( $param_names as $param_name => $param_alias )
		{
			// this is to be compatible both for flat arrays & associative ones 
			// it ASSUMEs there are no fields that are numbers in a BaseObject
			$name = ( is_numeric( $param_name ) ? $param_alias : $param_name  );
			
			if ( $invoke_method )
			{
				throw new Exception ( "Method not yet implemented" );
				
				$func_name = "get" . $name;
			// 	because the name can indicate a getter that does not wrap a field of the base class, we need to envoke the method directly
				$value = call_user_func ( array ( $obj , $func_name ) );
			}
			else
			{
				// access the field by name 
				$value = $obj->getByName( $name , BasePeer::TYPE_FIELDNAME ) ;
			}
			$res[$param_alias] = $value;
		}
		
		if ( $map_to_add != NULL )
		{
			foreach ( $map_to_add as $attr => $val )
			{
				$res[$attr] = $val;
			}
		}
		return $res;		
	}
	
	/**
	 * Returns the value of the paramter from the object.
	 * Assume $obj is of type BaseObject
	 *
	 * @param unknown_type $obj
	 * @param unknown_type $param_name
	 */
	public static function getParamFromObject ( BaseObject $obj , $param_name )
	{
		if ( $obj == NULL )
		{
			return NULL;
		}
		if ( ! $obj instanceof BaseObject )
		{
			throw new Exception ( "getParamFromObject should have the first parameter an object of type BaseObject" );
		}
		return $obj->getByName( $param_name , BasePeer::TYPE_FIELDNAME );
	}

	public static function getParamListFromObject ( BaseObject $obj , $param_names , $separator=',')
	{
		if ( $obj == NULL )
		{
			return NULL;
		}
		if ( ! $obj instanceof BaseObject )
		{
			throw new Exception ( "getParamFromObject should have the first parameter an object of type BaseObject" );
		}
		
		$res = "";
		
		if ( is_string($param_names))
		{
			return 	$obj->getByName( $param_name , BasePeer::TYPE_FIELDNAME ) ;
		}
		
		$first_time = true;
		foreach ( $param_names as $param_name )
		{
			if ( !$first_time )
			{
				$res .= $separator;
			}
			$res .= $obj->getByName( $param_name , BasePeer::TYPE_FIELDNAME ) ;
			$first_time= false;
		}
		
		return $res;
	}

	public static function getParamListFromObjectAsArray ( BaseObject $obj , $param_names  )
	{
		if ( $obj == NULL )
		{
			return NULL;
		}
		if ( ! $obj instanceof BaseObject )
		{
			throw new Exception ( "getParamFromObject should have the first parameter an object of type BaseObject" );
		}
		
		$res = array();
		foreach ( $param_names as $param_name )
		{
			$res[$param_name] = $obj->getByName( $param_name , BasePeer::TYPE_FIELDNAME ) ;
		}
		
		return $res;
	}
	
	/**
	 * param_names is the list of fields to add as attributes to the xml node.
	 * each element in the array can either be a field name or a pair of name=>alias.
	 * if name - the name will be used as the attribute name.
	 * if name=>alias - the 'alias' will be used as the attribute name instead. 
	 */
	public static function objToXml (  	BaseObject $obj , 
										array $param_names , 
										$xml_element_name = NULL , 
										$close_xml_element = true , 
										$map_to_add = NULL , 
										$envoke_method = false )
	{
		if ( $obj == NULL )
		{
			return NULL;
		}
		if ( ! $obj instanceof BaseObject )
		{
			throw new Exception ( "objToXml should have the first parameter an object of type BaseObject" );
		}
		
		$res = $xml_element_name == NULL ? "" : "<" . $xml_element_name . " " ;
		
		foreach ( $param_names as $param_name => $param_alias )
		{
			// this is to be compatible both for flat arrays & associative ones 
			// it ASSUMEs there are no fields that are numbers in a BaseObject
			$name = ( is_numeric( $param_name ) ? $param_alias : $param_name  );
			if ( $envoke_method )
			{
				// 
				$raw_value = myBaseObject::envokeMethod ( $obj , $name );
			}
			else
			{
				$raw_value = $obj->getByName( $name , BasePeer::TYPE_FIELDNAME ) ;
			}
			// escape the value of the attribute
			$value = kString::xmlEncode( $raw_value );
			$res .= ' ' . $param_alias . '="' . $value . '"';
		}
		
		if ( $map_to_add != NULL )
		{
			foreach ( $map_to_add as $attr => $val )
			{
				$res .= ' ' . $attr . '="' . kString::xmlEncode($val) . '"';
			}
		}
		$res .= $xml_element_name == NULL ? "" :
			$close_xml_element ? "/>\n" : ">\n" ;
		return $res;		
	}
	
	
	public static function objToString ( $obj , $field_list )
	{
		$str = ""; 
	
		foreach ( $field_list as $field )
		{
			if ( $str != "" ) $str .= "&";
			
			// this is to be compatible both for flat arrays & associative ones 
			// it ASSUMEs there are no fields that are numbers in a BaseObject
			$name = $field;

			$func_name = "get" . $name;
			// 	because the name can indicate a getter that does not wrap a field of the base class, we need to envoke the method directly
			$raw_value = call_user_func ( array ( $obj , $func_name )   );
			$value = str_replace ( array ( "=" , "&") , array ( "__EQ__" , "__AMP__" ), $raw_value );
			
			$str .= "$name=$value";
		}
		
		return $str;
	}
	
	public static function objFromString ( $obj , $str )
	{
		$str = ""; 
	
		$name_value_list = explode("&" , $str);
		
		foreach ( $name_value_list as $name_value  )
		{
			list ( $name , $raw_value ) = explode( "=" , $name_value );
			$value = str_replace (  array ( "__EQ__" , "__AMP__" ), array ( "=" , "&") , $raw_value );
			$func_name = "set" . $name;
			// 	because the name can indicate a getter that does not wrap a field of the base class, we need to envoke the method directly
			call_user_func ( array ( $obj , $func_name ) ,$value  );
			
		}
		
		return $obj;
	}	
	
	public static function arrayFromString ( $str )
	{
		$name_value_list = explode("&" , $str);
		
		$result = array() ;
		
		foreach ( $name_value_list as $name_value  )
		{
			list ( $name , $raw_value ) = explode( "=" , $name_value );
			$value = str_replace (  array ( "__EQ__" , "__AMP__" ), array ( "=" , "&") , $raw_value );
			
			$result[$name]=$value;
			
		}
		
		return $result;
	}	

	
	public static function sortParamsByOrderInFields ( $request_params , $fields , $add_fields_from_request_params = true )
	{
		$all_field_names = array();
		foreach ( $fields as $field_name )
		{
			$all_field_names[] = $field_name;
		}
		
		if ( $add_fields_from_request_params )
		{
			foreach ( $request_params as $name => $value )
			{
				if ( in_array ( $name , $all_field_names ) ) continue;
				$all_field_names[] = $name;
			}
		}
		return $all_field_names;
	}
}
?>
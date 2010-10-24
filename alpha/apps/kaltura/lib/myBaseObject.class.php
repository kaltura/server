<?php
abstract class myBaseObject implements Iterator 
{
	const CLONE_POLICY_PREFER_EXISTING = 0;
	const CLONE_POLICY_PREFER_NEW = 1;

	const CLONE_FIELD_POLICY_THIS = 0;
	const CLONE_FIELD_POLICY_OTHER= 1;
  
	const FIELD_EQUAL = "=";
	const ENCODE_FIELD_EQUAL = "_@EQ@_";
	const FIELD_SEPARATOR  = "&";
	const ENCODE_FIELD_SEPARATOR  = "_@AMP@_";

	protected $fields;

	abstract protected function init();
	public function __construct ()
	{
		$this->init();
	}

	public function __get ( $field_name )
	{
		return $this->getByName ( $field_name );
	}
	
	/**
	 * ASSUME - the $method_alias can have a separator - "." between methods - 
	 * this will require a recusion until there are no such separators
	 */
	public static function envokeMethod ( $obj , $method_alias )
	{
//		echo "now envoking: " . get_class ( $obj ) . "::" . 	$method_alias . "<br>";
		// method_alias does not include . - a direct method hit
		if ( strpos( $method_alias , "." ) === FALSE  )
		{
			//			echo  "strpos in $method_alias :" . strpos( $method_alias , ".") . "<br>";
			$method_name = "get".  $method_alias ; // ucfirst( $method_alias); // - no need to uppser case the methodname

			try
			{
				if ( ! method_exists ( $obj, $method_name ) )
				{
					throw new Exception ( "[" . ( $obj ? "1" : "0" ) . "] Error while envoking " . get_class ( $obj ) . "::" . 	$method_name . "!!" );
				}
				
				return 	call_user_func ( array ( $obj , $method_name ) );
			}
			catch ( Exception $ex )
			{
//				echo "[" . ( $obj ? "1" : "0" ) . "] Error while envoking " . get_class ( $obj ) . "::" . 	$method_name . "!!";
				echo "[" . get_class ( $obj ) . "] methodName [$method_name]:" . $ex->getMessage() . "<br>";
//				echo "Error while envoking " . get_class ( $obj ) . "::" . 	$method_name . "!!" ;
			}
			
			return null;
		}

//		echo "Now envoking $method_alias<br>";
		
		$method_list = explode ( "." , $method_alias);

		$current_obj = $obj;
		$current_method_name = "get".  ucfirst( $method_list[0] );

		reset ( $method_list );
		//loop
		while ( $current_method_alias =  current ( $method_list) )
		{
			$current_method_name = "get".  ucfirst( $current_method_alias );
			if ( $current_obj )
			{
//				echo "$method_alias: [" . get_class ( $current_obj ) . "] $current_method_name<br>";
				$arr = array ( $current_obj , $current_method_name);
				//echo "envoke:" . print_r ( $arr , true );
				$current_obj = call_user_func ( $arr  );
			}
			next ( $method_list );
		}

		return $current_obj;
	}


	/**
	 * returns the number of field set
	 */
	public function fillObjectFromRequest ( $request_params , $prefix , $exclude_params, &$debug_str=NULL)
	{

		$set_field_count = 0;

		$prefix_len = strlen( $prefix);

		// iterate over all the paramters of the request
		foreach ( $request_params as $param => $name )
		{
			// ignore empty strings in the filter !
			if ( $request_params[$param] ==NULL || strlen ( $request_params[$param]) == 0 ) continue;

			//debugUtils::DEBUG( $debug_str , $param . "==" . $request_params[$param] . "[" . strpos ( $param , $prefix ) . "]<br>" );

			// the prefix is matched...
			$pos = strpos ( $param , $prefix );
			if ( $pos === 0 )
			{
				// the field name is the rest of the string coming after the prefix
				$param_name = substr ( $param , $prefix_len );

				if ( $exclude_params != NULL && in_array ( $param_name  , $exclude_params ) )
				{
					// this param should be ignored !
					debugUtils::DEBUG ( $debug_str , "Ignoring parameter [" . $param_name . "]");
					continue;
				}

				// TODO - should add a reg-exp array rather than this hard-coded logic in the base class !!
				// dont' fill the properties ending with backgroundImage (the avlues are WRONG!)
				if ( kString::endsWith( $param_name , "Image" ) )
				{
					continue;
				}

				//				debugUtils::DEBUG ( $debug_str ,  "[" . $param . "==" . $request_params[$param] . "]<br>" );
//								echo "[" . $param . "==" . $request_params[$param] . "]<br>" ;

				$this->setByName ( $param_name , $request_params[$param] );
				$set_field_count++;
			}
		}

		return $set_field_count;
		//echo "fillObjectFromRequest - done!!";
	}

	// ASSUME - the other_obj is the same type of this
	// other object can be either of myBaseObject or BaseObject
	// we use only 1 methods on this object: getByName
	public function fillObjectFromObject ( $other_obj  , $clone_field_policy ,
		$clone_policy ,	$exclude_field_names,  $envoke_getter_method_list = NULL , $allow_empty = true  )
	{
		$field_added = false;
		if ( $clone_field_policy == self::CLONE_FIELD_POLICY_THIS )
		{
			$field_list = 	 $this->fields;
		}
		else
		{
			// in this case - use the local fields anyway
			if ( $other_obj instanceof  BaseObject ) 
			{
				$field_list = 	 $this->fields;
			}
			else
			{
				$field_list = 	 $other_obj->getFields();
			}
		}


		foreach ( $field_list as $field => $name )
		{
			// TODO - IMPORTANT: verify that the include params are indeed fields of this object.
			// not doing so will allow typos of field names followed by  security breachs !!!
			if ( $exclude_field_names != NULL && in_array ( $field  , $exclude_field_names ) )
			{
				continue;
			}

			if ( $clone_policy == self::CLONE_POLICY_PREFER_EXISTING )
			{
				$current_value = $this->getByName ( $field , BasePeer::TYPE_FIELDNAME);
				if ( $current_value != NULL )
				{
					// prefer existing - don't force the new value
					continue;
				}
			}
				
			// third parameter - only for when using
			$field_value =  $other_obj->getByName ( $field , BasePeer::TYPE_FIELDNAME);
			if ( empty ( $field_value ) && ( $allow_empty == false ) && $field_value !== 0 ) continue;				
			$this->setByName ( $field , $field_value , BasePeer::TYPE_FIELDNAME );
		}

		// ASSUME - the getters don't get any paramter
		if ( $envoke_getter_method_list == NULL ) return;

		foreach ( $envoke_getter_method_list as $field => $method_alias )
		{

			if ( $clone_policy == self::CLONE_POLICY_PREFER_EXISTING )
			{
				$current_value = $this->getByName ( $field , BasePeer::TYPE_FIELDNAME);
				if ( $current_value != NULL )
				{
					// prefer existing - don't force the new value
					continue;
				}
			}
				
			$field_value =  self::envokeMethod($other_obj , $method_alias )  ;
				
			$this->setByName ( $field , $field_value , BasePeer::TYPE_FIELDNAME );
		}
	}


	/**
	 * returns the number of field set
	 * WARNING - this method's params are different from fillObjectFromRequest due to the structure of the xml 
	 * The second parameter $prefix_to_add represents the string to append as prefix to each of the elements names.
	 */
	public function fillObjectFromXml ( SimpleXMLElement $simple_xml_node , $prefix_to_add , $exclude_params=null )
	{

		$set_field_count = 0;

		// iterate over all the paramters of the request
		foreach ( $simple_xml_node as $param => $value )
		{
			// ignore empty strings in the filter !
			if ( $value ==NULL || strlen ($value) == 0 ) continue;

			if ( $exclude_params != NULL && in_array ( $param  , $exclude_params ) )
			{
				continue;
			}
		
			// the field name is the rest of the string coming after the prefix
			$param_name = $prefix_to_add . $param;

			// TODO - should add a reg-exp array rather than this hard-coded logic in the base class !!
			// dont' fill the properties ending with backgroundImage (the avlues are WRONG!)
			if ( kString::endsWith( $param_name , "Image" ) )
			{
				continue;
			}

			$this->setByName ( $param_name , (string)html_entity_decode($value) );  // cast the SimpleXMLElement to string !!
			$set_field_count++;
		}

		return $set_field_count;
	}
		
	public function getFields()
	{
		return $this->fields;
	}

	public function getFieldNames ()
	{
		return array_keys( $this->fields );
	}

	public function get ( $param_name )
	{
		return 	$this->getByName( $param_name );
	}

	public function getParamFromObject ( $param_name )
	{
		return $this->getByName( $param_name );
	}

	/**
	 * @param array $field_names - list of names to return their values
	 * @return array - an associative array of name-value according to the requested $field_names list
	 */
	public function getByNames ( array $field_names  )
	{
		$res = array();

		foreach ( $field_names as $name )
		{
			$res[$name] = $this->getByName ( $name );
		}

		return $res;
	}

	protected function getByName ( $field_name  )
	{

		if ( !array_key_exists( $field_name , $this->fields ) )
		{
			debugUtils::DEBUG( "" , "Cannot set field [" . $field_name . "] in object of type - TODO - how to display current object's class ??" );
			return;
		}
		return $this->fields[$field_name] ;
	}

	public function set ( $field_name , $field_value )
	{
		if(is_array($field_value))
			$field_value = implode(',', $field_value);
			
		$this->setByName( $field_name , $field_value );
	}

	public function setByName ( $field_name , $field_value )
	{
		if(is_array($field_value))
			$field_value = implode(',', $field_value);
			
		if ( $this->isFieldValid ( $field_name , $field_value) )
		{
			$this->fields[$field_name] = $field_value;
		}
	}

	public function unsetByName ( $field_name )
	{
		$this->fields[$field_name] = null;
	}

	/**
	 * Derived classes can override this logic and have different constrainst on the field names and values.
	 * Here we can also add complex validaitons as long as the order of thefields doesn't matter at this point -
	 * only the name and the value of a single filed.
	 * Relationships between other fileds ought to be verified after the whole object is full
	 * because no assumption can be made on the order of the field filling.
	 * The basic implementation verifies that the field_name already exists in the fields array (meaning - part of the object's schema).
	 *
	 * @param string $field_name
	 * @param any $field_value
	 */
	protected function isFieldValid ( $field_name , $field_value )
	{
//		echo "isFieldValid: $field_name , $field_value , <br>" . print_r ( $this->fields  , false );
		
		if ( !array_key_exists( $field_name , $this->fields ) )
		{
			debugUtils::DEBUG( "" , "Cannot set field [" . $field_name . "] in object of type - TODO - how to display current object's class ??" );
			return false;
		}

		return true;
	}

	// TODO - consider using PHP's serialize
	/**
	 * Serializes all NON NULL fields to a string.
	 * This string has 2 importatn purposes:
	 * 1. such an object can be persisted in a single field in the DB (assuming no queries will be done on such an object)
	 * 2. will go back and forth to the client and will be deserialized using JavaScript.
	 * There will be 2 similar methods on the JavaScript side too.
	 */
	public function serializeToString ()
	{
		$str = "";
		$field_added = false;
		foreach ( $this->fields as $field => $name )
		{
			//			echo $field . "=" . $this->fields[$field] . "<br>";

			if ( $field_added )
			{
				// for the second time onwards ...
				$str .= myBaseObject::FIELD_SEPARATOR;
			}
			$str .= $field . myBaseObject::FIELD_EQUAL . myBaseObject::encode ( $this->fields[$field] );
			$field_added = true;
		}

		return $str;
	}

	// TODO - consider using PHP's deserialize and making this method static
	/**
	 * Populates all fields according to the string values.
	 */
	public function deserializeFromString ( $obj_str )
	{
		if ( !$obj_str or strlen( $obj_str ) == 0 )
		{
			// no string to deserialize fomr
			return;
		}

		$name_value_pairs = explode( myBaseObject::FIELD_SEPARATOR , $obj_str );

		foreach ( $name_value_pairs as $pair )
		{
			$tokens = explode ( myBaseObject::FIELD_EQUAL , $pair );
			$tok_count = count ( $tokens ) ;
			if ( $tok_count == 0 ) continue;
			elseif ( $tok_count == 1 )
			{
				$name = $tokens[0];
				$value = NULL;
			}
			elseif ( $tok_count == 2 )
			{
				$name = $tokens[0];
				$value = $tokens[1];
			}
			else
			{
				throw Exception ( "encode/decode didn't work well - there are '=' characters within the name or the value of a serialized field" );
			}

			//		echo $name . "=" . $value ."<br>";
			// first token is the name of the field, second token is the value
			$this->setByName( $name , myBaseObject::decode ( $value ) );
		}
	}

	public function toString ()
	{
		$str = "";
		$field_added = false;
		foreach ( $this->fields as $field => $name )
		{
			//			echo $field . "=" . $this->fields[$field] . "<br>";

			if ( $field_added )
			{
				// for the second time onwards ...
				$str .= "<br>"; // for nice display
			}
			$str .= $field . myBaseObject::FIELD_EQUAL . myBaseObject::encode ( $this->fields[$field] );
			$field_added = true;
		}

		return $str;
	}

	// This method assumes that the encoded values are never used in the text
	// TODO - if the encoded fields do appear in the tex, we can implement a more comlex encoding algorithm
	// that declares the separator at the beginning of the string (as in multipart email)
	static protected function encode ( $str )
	{
		return str_replace ( self::FIELD_SEPARATOR , self::ENCODE_FIELD_SEPARATOR ,
		str_replace ( self::FIELD_EQUAL , self::ENCODE_FIELD_EQUAL , $str ) );
	}

	// for opposite function for encode
	static protected function decode ( $str )
	{
		return str_replace ( self::ENCODE_FIELD_SEPARATOR , self::FIELD_SEPARATOR ,
		str_replace ( self::ENCODE_FIELD_EQUAL , self::FIELD_EQUAL ,  $str ) );
	}

	
	
	public function rewind()
	{
		if ( $this->m_obj == null ) return;
//		echo "rewind\n";
		$this->verifyIterator();
		reset($this->fields);
	}

	public function current()
	{
		//echo "current\n";
		$this->verifyIterator();
		return  ( current($this->fields) );
	}

	public function key() 
	{
//		echo "key\n";
		$this->verifyIterator();
		return  key($this->fields);
	}

	public function next() 
	{
	//	echo "next\n";
		$this->verifyIterator();
		return ( next($this->fields) );
	}

	public function valid() 
	{
		if ( $this->m_obj == null ) return false;
//		echo "valid\n
		$this->verifyIterator();
		return current($this->fields) !== false;
	}

	protected function verifyIterator()
	{
		if ( $this->fields != null ) throw new Exception ( "Cannot iterate an objec that is not an array" );
		
	}

}
?>
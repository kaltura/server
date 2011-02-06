<?php
/**
 * A bundle of helpful functions for manipulating arrays
 * 
 * @package infra
 * @subpackage utils
 */
class kArray
{
	/**
	 * Assums that the input array ($arr) is a flat non-associative array.
	 * Returns a new array where the name is equal to the value for every elemen in the original array.
	 *
	 * @param array $arr
	 * @return array
	 */
	public static function makeAssociative ( array $arr )
	{
		$new_arr = array();
		foreach ( $arr as $elem )
		{
			$new_arr[$elem] = $elem;
		}
		return $new_arr;
	}

	public static function makeAssociativeDefaultValue ( array $arr , $default_value)
	{
		$new_arr = array();
		foreach ( $arr as $elem )
		{
			$new_arr[$elem] = $default_value;
		}
		return $new_arr;
	}

	/**
	 * Assumes $array_to_add_from and $array_to_add_to are both associative.
	 * Adds every element from $array_to_add_from to $array_to_add_to
	 * Returns no value !
	 *
	 * @param array $array_to_add_to
	 * @param array $array_to_add_from (can be NULL)
	 */
	public static function associativePush( array &$array_to_add_to , $array_to_add_from)
	{
		if (is_array($array_to_add_from))
		{
			foreach ($array_to_add_from as $key => $value)
			{
				$array_to_add_to[$key] = $value;
			}
		}
	}

	public static function append ( $arr1 , $arr2 /* , more arrays to append */)
	{

		if ( $arr2 == NULL ) return $arr1;

		if ( $arr1 == NULL ) $arr1 = array();
		foreach ( $arr2 as $obj )
		{
			$arr1[] = $obj;
		}

		$c = func_num_args();
		if ( $c > 2 )
		{
			for ( $i=2 ; $i < $c ; ++$i )
			{
				$arr2 = func_get_arg ( $i );
				foreach ( $arr2 as $obj )
				{
					$arr1[] = $obj;
				}
			}
		}
		return $arr1;
	}

	public static function getRandFromArray ( $arr , $start_index = 0 )
	{
		if ( $arr == NULL ) return NULL;

		return $arr [ rand( $start_index , count ( $arr ) -1 ) ];
	}

	// $envoke of format : array ( $obj , $method_name )
	public static function foreachElem ( $arr , $envoke , $return_empty_array_if_null = false )
	{
		if ( $arr == NULL )
		{
			if ( $return_empty_array_if_null ) return array();
			else return NULL;
		}
		if ( ! is_array ( $arr ))
		{
			// pass the obj as the first and only argument
			return call_user_func ( $envoke , $arr );
		}

		$res = array();
		foreach ( $arr as $arg1 )
		{
			$res[] = call_user_func ( $envoke , $arg1 );
		}

		return $res;
	}

	public static function array_is_associative ($array)
	{
		if ( is_array($array) && ! empty($array) )
		{
			for ( $iterator = count($array) - 1; $iterator; $iterator-- )
			{
				if ( ! array_key_exists($iterator, $array) ) { return true; }
			}
			return ! array_key_exists(0, $array);
		}
		return false;
	}

	public static function addToArray ( &$arr , $value , $ignore_if_exists = false )
	{
		// either not in array or we don't care if it is in the array...
		if ( !$ignore_if_exists || ! in_array ( $value , $arr ) )
		{
			$arr[] = $value;
		}
	}

	public static function removeFromArray ( &$arr , $values )
	{
		if ( is_array ( $values ))
		{
			foreach ( $values as $value )
			{
				self::removeFromArray ( $arr , $value );
			}
		}
		else
		{
			foreach ( $arr as $index => $v )
			{
				if ( $v == $values )
				{
					array_splice($arr, $index, 1);
				}
			}
		}
	}

	public static function array_merge_keys($arr1, $arr2) 
	{
	    foreach($arr2 as $k=>$v) {
	        if (!array_key_exists($k, $arr1)) { //K DOESN'T EXISTS //
	            $arr1[$k]=$v;
	        }
/*	        else { // K EXISTS //
	            if (is_array($v)) { // K IS AN ARRAY //
	                $arr1[$k]=array_unisci_chiavi($arr1[$k], $arr2[$k]);
	            }
	        }
*/
	    }
	    return $arr1;
	}

	public static function trim(&$arr)
	{
		foreach($arr as &$item)
			$item = trim($item);
	}
	
}
?>
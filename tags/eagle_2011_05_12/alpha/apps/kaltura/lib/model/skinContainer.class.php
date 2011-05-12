<?php
include_once ( "myBaseObject.class.php" );
include_once ( "allUtils.class.php" );

// TODO -move to lib !!
class skinContainer extends myBaseObject
{
	// TODO - explain why the .cls prefix in the UI turns to _cls ?!!??!?!
	const FIELD_NAME_PREFIX = "_cls";
	const MAX_FIELD_NAME_LENGTH = 50;
	const MAX_FIELD_VALUE_LENGTH = 50;
	const MAX_NUMBER_OF_FIELDS = 80;

	const RULE_PROPERTY_SEPARATOR = "__";

	// TODO - think if should explicitly write all the allowed fields of the
	public function init ()
	{
		$this->fields = array ( );
	}

	/**
	 * will return a 2 dimention array or rules x properties
	 * rule[0]
	 * 	prop[0]
	 * 	prop[1]
	 * rule[1]
	 * 	prop[0]
	 * ...
	 */
	function getCssRules ()
	{
		$rule_entries = $this->getFields();
		
		$rules = array();
		// create a 2 dimention array that represents all the properties per css-rule
		foreach ( $rule_entries as $entry => $name )
		{
			//echo $entry . "=" . $name . "<br>";
			skinContainer::splitRuleProperty( $entry , $rule , $prop_name );

			$rules[$rule][$prop_name] = $rule_entries[$entry];
		}
		
		return $rules;
	}


	// Assuming the str hold the serialized string, will set the $rule with the first toke
	// which is the css-rule name and $prop will hold the name of the property
	static function splitRuleProperty ( $str , &$rule , &$prop_name )
	{
		$tokens = explode( self::RULE_PROPERTY_SEPARATOR , $str );
		$rule = $tokens[0];
		$prop_name = $tokens[1];
	}

	static public function getSkinFieldName ( $cls_name , $property )
	{
		//return "skin__" . $cls_name . self::RULE_PROPERTY_SEPARATOR . $property;
		return "skin_" . $cls_name . self::RULE_PROPERTY_SEPARATOR . $property;
	}

	// the values of the cls_name & property will be populated after the tokening
	// this is the reverse function of getSkinFieldName.
	// it assume neither $cls_name nore $propert included '__' and that there is a prefix to the string ( most probably skin__ )
	static public function getSkinClassAndPropertyFromHtmlFields ( $field_name , &$cls_name , &$property )
	{
		$tokens = explode( self::RULE_PROPERTY_SEPARATOR , $field_name );
		$cls_name = $str_arr[1];
		$property = $str_arr[2];
	}

	static public function getFieldNameFromHtmlFieldName ( $prefix , $html_field_name )
	{
		if ( kString::beginsWith( $html_field_name , $prefix ) )
		{
			$field_name = substr( $html_field_name , strlen ( $prefix ) );
			return str_replace ( ".cls" , "_cls" , $field_name );
		}
		
		return NULL;
	}
	
	static public function fixClassName ( $cls_name )
	{
		return str_replace ( ".cls" , "_cls" , $cls_name );
	}
	
	
	public function getValueFromHtmlFieldName ( $prefix , $html_field_name )
	{
		return $this->getParamFromObject ( getFieldNameFromHtmlFieldName ( $prefix , $html_field_name) );
	}
	
	
	public function setValueFromHtmlFieldName ( $prefix , $html_field_name , $value )
	{
		if ( kString::beginsWith( $html_field_name , $prefix ) )
		{
			$field_name = substr( $html_field_name , strlen ( $prefix ) );
			$this->setByName( $field_name , $value );
		}
	}
	
	public function getValue ( $cls_name , $property )
	{
		return $this->getParamFromObject ( str_replace ( ".cls" , "_cls" , $cls_name ) . "__" . $property );
	}
	
	
	/**
	 * Override the basic implementation of the field validation so there will be no need to define each and every field name in the fields schema.
	 * IMPORTANT - the restriction will be based on several parameters mainly to protect againt injection of arbitrary data in large quantities.
	 * 1. the prefix of the field_name is FIELD_NAME_PREFIX 
	 * 2. the length of the field_name is less than or equal MAX_FIELD_NAME_LENGTH 
	 * 3. the length of the field_value  is less than or equal MAX_FIELD_VALUE_LENGTH
	 * 4. the total number of fields is less tham or equal MAX_NUMBER_OF_FIELDS
	 *
	 * @param stirng  $field_name
	 * @param any $field_value
	 * @return true if the field (name & value) fit the constraints, false otherwise
	 */
	protected function isFieldValid ( $field_name , $field_value )
	{
/*		if ( ! kString::beginsWith( $field_name , skinContainer::FIELD_NAME_PREFIX ) )
		{
			debugUtils::DEBUG( "Field [" . $field_name . "] invalid. Every field shouild start with the prefix "  , skinContainer::FIELD_NAME_PREFIX );
			return false;
		}
*/
		if ( strlen ( $field_name ) > skinContainer::MAX_FIELD_NAME_LENGTH)
		{
			debugUtils::DEBUG( "Field [" . $field_name . "] invalid. Field name should not be longer than ", skinContainer::MAX_FIELD_NAME_LENGTH );
			return false;
		}
		if ( strlen ( $field_value ) > skinContainer::MAX_FIELD_VALUE_LENGTH)
		{
			debugUtils::DEBUG( "Field [" . $field_name . "] invalid. Field value is [" . $field_value . "] and should not be longer than ", skinContainer::MAX_FIELD_VALUE_LENGTH );
			return false;
		}
		if ( count ( $this->fields ) > skinContainer::MAX_NUMBER_OF_FIELDS)
		{
			debugUtils::DEBUG( "Too many fields. Number should not exceed [" . skinContainer::MAX_NUMBER_OF_FIELDS . "]" , "" );
			return false;
		}
		return true;
	}
}
?>
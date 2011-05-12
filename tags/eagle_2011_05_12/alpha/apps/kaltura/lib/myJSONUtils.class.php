<?php
if (!function_exists('json_encode')) 
{
	require('Services_JSON.class.php');
}


class myJSONUtils
{
	static public function encodeObject ( BaseObject $obj , array $field_names ) 	
	{
		return json_encode(baseObjectUtils::getParamListFromObjectAsArray ( $obj , $field_names ) ); 
	}
}
?>
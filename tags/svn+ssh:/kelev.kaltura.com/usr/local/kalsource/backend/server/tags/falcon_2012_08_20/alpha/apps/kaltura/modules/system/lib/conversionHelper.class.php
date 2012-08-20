<?php
class conversionHelper
{

function createSelect ( $id , $name , $default_value , $list_name )
{
	$prefix = "convprofile_";
	
	//global $arrays;
	$arrays = array ( 
		"boolean_type" => array ( "" => "" , "true" => "true" , "false" => "false"  ) ,
		"boolean_int_type" => array ( "" => "" , "1" => "true" , "0" => "false"  ) ,
		"aspect_ratio" => array ( ""  => "leave empty" , 1=> "original ratio" , 2 => "orginal size" , 3 => "4:3" , 4 => "16:9" ) ,  
	);


	$list = $arrays[$list_name];

	//echo "createSelect: list_name:[$list_name] count:[" . count ( $list ) . "]<br>";

	$str = "<select id='$prefix{$id}' style='font-family:arial; font-size:12px;' name='$prefix{$name}' onkeyup='updateSelect( this )' onchange='updateSelect( this )'>";

	$default_value_selected = "";
	foreach ( $list as $value => $option  )
	{
		// not always the default value is found 
		if ( $value == $default_value ) $default_value_selected = $default_value;
		$selected = ($value == $default_value ) ? "selected='selected'" : "" ;
		$str .= "<option value='$value' $selected >$option</option>\n";
	}
	$str .= "</select> <span style='color:blue;' id='$prefix{$id}_current_value'>$default_value_selected</span>\n";

	return $str;
}

	private $element_id = 0;
	function createInput ( $name , $type="text" , $size=7 , $default_value=null , $list_name=null , $comment=null )
	{
		$prefix = "convprofile_";
		
		global $element_id;
		$element_id++;
		$id = $name . "_" . $element_id;
	
		if ( !$type ) $type="text";
		if ( !$size ) $size = 20;
		if ( !$default_value) $default_vlaue="";
	
		$str = "<td>";
	
		if ( $type == "select" )
		{
			$str .= createSelect ( $id, $name , $default_value , $list_name );
		}
		elseif ( $type == "textarea" )
		{
			@list ( $rows,$cols ) = explode ( "," , $size ) ;
			$str .= "<textarea id='{$prefix}{$id}' name='$prefix{$name}' rows='$rows' cols='$cols' >{$default_value}</textarea>" ;
		}
		else
		{
			$str .= "<input id='$prefix{$id}' name='$prefix{$name}'  type='$type'' size='$size' value='$default_value'>";
		}
	
		$str .= "</td><td style='color:gray; font-size:11px; font-family:arial;'>" . ( $comment ?  "* " . $comment : "&nbsp;" ) . "</td>";
		return $str;
	}
	
	function createInputs ( $arr )
	{
		$str = "";
		foreach ( $arr as $input )
		{
			$str .= createInput ( $input[0] , @$input[1] , @$input[2] ,@$input[3] , @$input[4] , @$input[5] );
		}
	
		return $str;
	}
	
	function prop ( $obj , $getter_name , $type="text" , $size=7 , $default_value=null , $list_name=null , $comment=null)
	{
		$method_name = "get{$getter_name}";
		if ( $obj ) $value = call_user_func ( array ( $obj , $method_name ) );
		else $value = "";
		if ( $value === null ) $value = $default_value;
		return "<tr class='prop'><td>$getter_name</td>" . createInput ( $getter_name , $type  , $size  , $value  , $list_name  , $comment ) . "</tr>";
	}
	
	static function propList ( $obj , array $prop_names )
	{
		$str = "";
	
		foreach ( $prop_names as $prop_name )
		{
			$method_name = "get{$prop_name}";
			if ( $obj ) $value = call_user_func ( array ( $obj , $method_name ) );
			else $value = "";
			$str .= "<td>{$value}</td>";
		}
		return $str;
	}
}
?>
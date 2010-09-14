<?php


function createSelect ( $id , $name , $default_value , $list_name )
{
	$prefix = "convparams_";
	
	//global $arrays;
	$arrays = array ( 
		"boolean_type" => array ( "" => "" , "true" => "true" , "false" => "false"  ) ,
		"boolean_int_type" => array ( "" => "" , "1" => "true" , "0" => "false"  ) ,
		"aspect_ratio" => array ( ""  => "leave empty" , 1=> "original ratio" , 2 => "orginal size" , 3 => "4:3" , 4 => "16:9" ) ,  
		"audio_channels_type" => array ( ""  => "leave empty" , 1=> "Mono" , 2 => "Stereo" ) ,
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

$element_id = 0;
function createInput ( $name , $type="text" , $size=7 , $default_value=null , $list_name=null , $comment=null )
{
	$prefix = "convparams_";
	
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
	return "<tr style='font-size:12px; '><td>$getter_name</td>" . createInput ( $getter_name , $type  , $size  , $value  , $list_name  , $comment ) . "</tr>";
}

?>

<script>
$$ = function(x) { return document.getElementById(x); }
function updateSelect ( elem )
{
//	var inElement = $$(elem);	
	var current_value_elem = $$(elem.id + "_current_value" );
	if ( current_value_elem != null )	current_value_elem.innerHTML = elem.value;
}

function saveConvParams()
{
	var res;
	var convp_id = $$("convparams_id");
	if ( convp_id.value == "" )
	{
		res = confirm ( "Are you sure you want to create a new ConversionParams object in the DB with these details ??" );
	}
	else
	{ 
		res = confirm ( "Are you sure you want to save the new details for ConversionParams [" + convp_id.value  + "] ?? " );
	}
	if ( res )
	{
		var cmd = $$("command");
		cmd.value="save";
		return true;
	}
	else
	{
		return false;
	}
}
</script>


<form method="post">
<input type="hidden" id="command" name="command"> 
<div style="font-family: calibri; font-size:12px; ">
<span style='color:red; font-size:15px;'><?php echo  $error  ?></span><br />

Conversion Params Id: <input id="convparams_id" name="convparams_id" value='<?php echo  $conv_params_id ?>'> <input type='submit'  name='submit' value='Submit'/>
<input type="hidden" name="close_after_save" value ="<?php echo $close_after_save?>">
<br>
<span>* To create a new set of ConversionParams, leave the id empty and press the "save" button once form is filled.</span>
<hr/>

<table>
<?php echo  prop ( $conv_params , "partnerId"  , "text" , "15" , "" ) ?>
<?php echo  prop ( $conv_params , "name"  , "text" , "15" , "" , "" , "should reflect the set of params") ?>
<?php echo  prop ( $conv_params , "enabled"  , "select" , "1" , "1" , "boolean_int_type" ,"make this set of params usable or not" ) ?>
<?php echo  prop ( $conv_params , "profileType"  , "text" , "15" , "" ,"" , "CONNECTS BETWEEN THE PROFILE AND PARAMS" ) ?>
<?php echo  prop ( $conv_params , "profileTypeIndex"  , "text" , "15" , "" , "" , "the order in which this set of params will be used in a the profile" ) ?>
<?php echo  prop ( $conv_params , "commercialTranscoder"  , "select" , "1" , "" , "boolean_int_type" ,"" ) ?>
<?php echo  prop ( $conv_params , "width"  , "text" , "15" , "" ) ?>
<?php echo  prop ( $conv_params , "height"  , "text" , "15" , "" ) ?>
<?php echo  prop ( $conv_params , "aspectRatio"  , "select" , "1" , "1" , "aspect_ratio" , "" ) ?>
<?php echo  prop ( $conv_params , "gopSize"  , "text" , "15" , "" , "" , "once every X frames will force a keyframe") ?>
<?php echo  prop ( $conv_params , "bitrate"  , "text" , "15" , "" ) ?>
<?php echo  prop ( $conv_params , "qscale"  , "text" , "15" , "" , "" , "quality scale: 1-31, 1 is best. If should be the same as original, use 'sameq' in the extra params" ) ?>
<?php echo  prop ( $conv_params , "framerate"  , "text" , "15" , "25" , "" , "default should be 25") ?>
<?php echo  prop ( $conv_params , "audioBitrate"  , "text" , "15" , "" , "" , "NUMBER ONLY, Can leave empty, can be 64,80,96,112,128,160,192,224,256,288,320") ?>
<?php echo  prop ( $conv_params , "audioSamplingRate"  , "text" , "15" , "" , "" , "NUMBER ONLY in Hz, can be 22050/44100") ?>
<?php echo  prop ( $conv_params , "audioChannels"  , "select" , "1" , "2" , "audio_channels_type" , "" ) ?>
<?php echo  prop ( $conv_params , "fileSuffix"  , "text" , "15" , "" , "" , "The suffix of the file - MUST NOT be empty when using 'profileTypeIndex' > 1") ?>
<?php echo  prop ( $conv_params , "ffmpegParams"  , "text" , "25" , "" ) ?>
<?php echo  prop ( $conv_params , "mencoderParams"  , "text" , "25" , "" ) ?>
<?php echo  prop ( $conv_params , "flixParams"  , "text" , "25" , "" ) ?>
</table>
</div>
<div style="width:100%; text-align: right">
<?php if ( $conv_params->getId() == "" || $conv_params->getId() > 100 || $ok_to_save ) { ?>
<input type="submit" name="save" value="save" onclick="return saveConvParams()">
<?php } else {?>
<span style='background-color:red; color:white; text-align: center' ><b>System</b> Conversion Params cannot be modified</span> 
<?php } ?>
</div>
<?php
if ( $simulation )
{
	echo "<table border='0px' style='font-family: calibri; font-size:12px; ' cellpadding=1 cellspacing=1>";
	foreach ( $simulation as $engine => $result )
	{
		echo "<tr><td>$engine</td><td>" . @$result[1] . "</td></tr>";
	}
	echo "</table>";
}
 ?>
</form>


<script>
var wrap = document.getElementById("wrap");
wrap.setAttribute( "style" , null );
wrap.style = null;
</script>"
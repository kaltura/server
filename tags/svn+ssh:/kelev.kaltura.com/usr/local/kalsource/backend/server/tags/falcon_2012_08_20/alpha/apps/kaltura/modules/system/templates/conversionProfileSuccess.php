<?php


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

$element_id = 0;
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
	return "<tr style='font-size:12px;'><td>$getter_name</td>" . createInput ( $getter_name , $type  , $size  , $value  , $list_name  , $comment ) . "</tr>";
}

function propList ( $obj , array $prop_names )
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
	var convp_id = $$("convprofile_id").value;
	if ( convp_id == "" )
	{
		res = confirm ( "Are you sure you want to create a new ConversionProfile object in the DB with these details ??" );
	}
	else
	{ 
		res = confirm ( "Are you sure you want to save the new details for ConversionProfile [" + convp_id  + "] ?? " );
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

var convparams_window = null;
function openConversionParams( convparams_id , convprofile_type )
{
	_conversionParams = document.getElementById ( "conversionParams" );
	_conversionParams.style.visibility = "visible";
	
	// send command=fill to be able to preset some of the fielsd of the profile
	url = "./conversionParams?";
	if ( convparams_id != null )
		url += "convparams_id=" + convparams_id + "&";
	if ( convprofile_type != null )
	 	url += "command=fill&convparams_enabled=1&convparams_profileType=" + convprofile_type +"&convparams_partnerId=" + "<?php if ($conv_profile) echo $conv_profile->getPartnerId()?>" ;
	 	
	elem = document.getElementById( "ifrm" );
	elem.src = url;
/*		
	convparams_window = window.open( url , "convparams" , "location=0,status=0,height=600,width=400" );
			//"top=100,left=500,height=600,width=400,status=no,toolbar=no,menubar=no,location=no,scrollbars=no,resizable=no" );
	convparams_window.focus();
*/
}

function closeConversionParams()
{
	_conversionParams = document.getElementById ( "conversionParams" );
	_conversionParams.style.visibility = "hidden";

}

</script>

<form method="post">
<input type="hidden" id="command" name="command"> 
<div style="font-family: calibri; font-size:12px; ">

Conversion Profile Id: <input id="convprofile_id" name="convprofile_id" value='<?php echo  $conv_profile_id ?>'> <input type='submit'  name='submit' value='Submit'/>
display disalbed <input type=checkbox id="display_disabled" name="display_disabled" value='1' <?php echo $display_disabled ? "checked='checked'" : "" ?>>
<br>
<span style='color:red;'>Leave id empty to create new Conversion Profile</span>
<br>
<table>
<tr><td style="font-weight:bold; " colspan="3"><hr/>General</td></tr>
<?php echo  prop ( $conv_profile , "partnerId"  , "text" , "15" , "" ) ?>
<?php echo  prop ( $conv_profile , "name"  , "text" , "15" , "" ) ?>
<?php echo  prop ( $conv_profile , "enabled"  , "select" , "1" , "" , "boolean_int_type" ,"make this profile usable or not" ) ?>
<?php echo  prop ( $conv_profile , "profileType"  , "text" , "15" , "" , "" , 	 "* together with the partnerId, primary key to define the params that will be selected") ?>
<?php echo  prop ( $conv_profile , "profileTypeSuffix"  , "text" , "15" , "" , "" , "* together with the partnerId and profileType , secondary key to define the params that will be selected") ?>
<?php echo  prop ( $conv_profile , "bypassFlv"  , "select" , "1" , "" , "boolean_int_type" ,"" ) ?>
<?php echo  prop ( $conv_profile , "commercialTranscoder"  , "select" , "1" , "" , "boolean_int_type" ,"" ) ?>
<?php echo  prop ( $conv_profile , "width"  , "text" , "15" , "" ) ?>
<?php echo  prop ( $conv_profile , "height"  , "text" , "15" , "" ) ?>
<?php echo  prop ( $conv_profile , "aspectRatio"  , "select" , "1" , "1" , "aspect_ratio" , "" ) ?>
</table>
</div>
<br/>
<?php if ( $conv_profile_id == "" || $conv_profile_id > 2000 || $ok_to_save ) { ?>
<input type="submit" name="save" value="save" onclick="return saveConvParams()">
<?php } else {?>
<span style='background-color:red; color:white; text-align: center' ><b>System</b> Conversion Profile cannot be modified</span> 
<?php } ?>

</form>


<br />
<div style="font-family: calibri; font-size:12px; ">
<span style='color:red; font-size:14px'><?php echo $message ?></span>
<br>
Conversion Params that will be used with this profile: id [<?php echo  $conv_profile_id ?>] profileType [<?php echo $conv_profile_type ?>]<br>
Data used for selection:<br>
partnerId: <?php echo @$fallback_mode["partner_id"]?><br>
profileType: <?php echo @$fallback_mode["profile_type"]?><br>
<?php 
$mode = @$fallback_mode["mode"];
/*
 * 	reason taken from ConversionParamsPeer::retrieveByConversionProfile
 */
$reason = array ( 1 => "for this partner_id by conv_profile->profileType . conv_profile->profileTypeSuffix ( a simple concatenation with no separator )" ,
	2 => "for this partner_id by conv_profile->profileType" ,
	3 => "for global partner by conv_profile->profileType . conv_profile->profileTypeSuffix" , 
	4 => "for global partner by conv_profile->profileType" , 
	5 => "default of the system - always exists. ** PLEASE MAKE SURE THERE IS NO MISTAKE IN THE profileType OF THIS PROFILE **" );
?>
<span style='color:<?php echo $mode == 5 ? "red" : "black" ?>'>
reason: <?php echo "[$mode] " . @$reason[$mode] ?>
</span>
<br>
<?php
if ( $conv_params_list ) { ?>
	<table cellpadding="3px" style="width:60%; padding:3px; margin:3px; font-size:12px; ">
	 	<tr style="background-color:#E2E2E2" >
	 		<td></td><td>id</td><td>partner Id</td><td>name</td><td>enabled</td><td>profile Type</td><td>profile Type Index</td><td>width</td><td>height</td><td>aspect Ratio</td>
	 		<td>gop Size</td><td>bitrate</td><td>qscale</td>
	 		<td>framerate</td><td>audio Bitrate</td><td>audio Sampling Rate</td><td>audio Channels</td>
	 		<td>file Suffix</td><td>ffmpeg Params</td><td>mencoder Params</td><td>flix Params</td>
	 	</tr>
<?php

	$i=0;
	foreach ( $conv_params_list as $conv_params )
	{
		if ( $conv_params->getEnabled() == 0 )
			$sty = "background-color: #CCC";		
		else
			$sty = "background-color:" . ( $i%2  ? "#66FF99" : "#66FF99" ) . ";";
		echo "<tr style='$sty'>" .
			"<td><a href='javascript:openConversionParams( " . $conv_params->getId() . ", null );'>edit</a></td>" .
			propList ( $conv_params , array ( "id","partnerId" , "name" , "enabled" , "profileType" , "profileTypeIndex" , "width" , "height" , "aspectRatio" , 
	 				"gopSize" , "bitrate" , "qscale" , 
					"framerate" , "audioBitrate" , "audioSamplingRate" , "audioChannels" , 
	 				"fileSuffix" , "ffmpegParams" , "mencoderParams" , "flixParams" ) ) . "</tr>";
		$i++; 
	} 
?>	 	
	 </table>
<?php  } else { ?> 
	No ConversionParams for this ConvesioProfile
<?php }?>	

<br>
Create ConversionParams for this ConversionProfile <button onclick="openConversionParams( null , '<?php echo $conv_profile_type ?>')">Create</button>
</div>

<div id='conversionParams' 
	style='font-family: calibri; margin:2px; background-color:#E1E1E1; border:0px; position:absolute; top:1%; right: 10%; width:550px; height:800px; visibility:hidden;'>
	<div onclick='closeConversionParams();' style='float:right; font-size:10px; cursor: pointer'>[x]</div>
	<iframe id='ifrm' style='border:0px; width:100%; height:100%' src=""> </iframe>
</div>

<script>
var wrap = document.getElementById("wrap");
wrap.setAttribute( "style" , null );
wrap.style = null;
</script>
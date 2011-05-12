<?php
function input ( $label , $type , $name , $def_value=null , $extra =null )
{
	$value = @$_REQUEST[$name];
	
	$checked = "";
	if ( $type == 'checkbox' )
	{
		$checked = ( $value == $def_value ) ? "checked='checked'" : ""; 
		$value = $def_value;
	}
	else
	{
		if ( !$value ) $value = $def_value;	
	}
	
	$str = "$label <input type='$type' name='$name' id='$name' value='$value' $checked size='10' $extra> ";
	return $str;
}
?>

<script type="text/javascript">
var handle;
function investigate ( entry_id )
{
	handle = window.open("./investigate?entry_id=" + entry_id , "investigate" );
	handle.focus();
}

function playEntry ( entry_id )
{
	//alert ( "playEntry:" + entry_id );
	player = document.getElementById("kaltura_player");
	if ( player )
	{
		if ( player.insertMedia )
		{
			player.insertMedia ( "-1" , entry_id , true );		
		}
		else
		{
			alert ( "Player loading..." );
		}
	}
	else
	{
		alert ( "No player" );
	}
}

function gotoPage( page_to_go )
{
	pg = document.getElementById('page');
	pg.value= page_to_go;
	
	submitForm();
}

function updateParams ( force )
{
	prtnr = jQuery ( "#partner_id" );
	crnt_prtnr = jQuery ( "#current_partner_id" );
	page = jQuery ( "#page" );
	page.attr ( "value" , "0" ); 
//	alert ( prtnr.attr ( "value" ) + " " +  crnt_prtnr.attr ( "value" ) );
	if ( force || ( prtnr.attr ( "value" ) !=  crnt_prtnr.attr ( "value" ) ) )
	{
		frm = jQuery('#form1');
		frm.submit();
	}
	else
	{
		submitForm();
	}
}

function submitForm ( )
{
	frm = jQuery('#form1'); //document.getElementById('form1');
	dynm = jQuery('#dynamic');
	
	var url = "./gallery?partial=1&" ;
//	alert ( frm + " " + dynm );
	inputs = frm.find('input');
	len = inputs.length;
	for (i=0; i<len ; i++)
	{
		cu = inputs[i]; // === current input 
		if ( cu.type == 'checkbox' )
		{
			//alert  ( "name [" + cu.name + "] value [" + cu.vlaue + "] checked [" + cu.checked + "]" ); 
			if ( cu.checked == true )
				url += cu.name + "=" + cu.value + "&";
		} 
		else
		{
			url += cu.name + "=" + cu.value + "&";		
		}
	}

//	alert ( url ); 
	
	jQuery.ajax({
		url: url,
		async: true , 
		complete: updateDynamic
	} );
			
}

function updateDynamic ( xhr )
{
//	alert ( 'updateDynamic' );
	dynm = document.getElementById ( 'dynamic' );
	dynm.innerHTML = xhr.responseText;
}

function updateAllStatus(elem)
{
	if (elem.checked) 
	{
		jQuery("#status_elem").children("span").children("input").attr("disabled",true);
		jQuery("#status_elem").children("span").children("input").attr("checked",false);
	}
	else
		jQuery("#status_elem").children("span").children("input").removeAttr("disabled");
}


function updateAllType(elem)
{
	if (elem.checked) 
	{
		jQuery("#type_elem").children("span").children("input").attr("disabled",true);
		jQuery("#type_elem").children("span").children("input").attr("checked",false);
	}
	else
		jQuery("#type_elem").children("span").children("input").removeAttr("disabled");
}
</script>
<div style='font-family:sans-serif; font-size:23px;'> Le Gallery</div>
<form id='form1'>
<div style='font-family:verdana;font-size:12px;' id='filter'>

<?//= input ( '' , 'hidden' , 'page_size' , '' ) ?>
<?= input ( '' , 'hidden' , 'page' , '0' ) ?>

<?= input ( 'partner' , 'text' , 'partner_id' ) ?>
|<?= input ( 'entry ids' , 'text' , 'entry_ids' , null , "title='for more than 1, separate with \",\" with NO SPACES'") ?>
|<?= input ( 'widget id' , 'text' , 'widget_id' ) ?>
<? //input ( 'uiConf id' , 'text' , 'ui_conf_id' ) ?>
|
<?= input ( 'all' , 'checkbox' , 'filter__in_type_all' , "all" , "onclick='updateAllType(this)'") ?>
<span id='type_elem'>
<span style='color:lightblue'><?= input ( 'video' , 'checkbox' , 'filter__in_type_1' , "1") ?></span>
<span style='color:lightgreen'><?= input ( 'image' , 'checkbox' , 'filter__in_type_2' , "2") ?></span>
<span style='color:#FDD017'><?= input ( 'audio' , 'checkbox' , 'filter__in_type_5' , "5") ?></span>
<span style='color:lightgray'><?= input ( 'rc' , 'checkbox' , 'filter__in_type_6' , "6") ?></span>
</span>
|
<?= input ( 'text' , 'text' , 'filter__like_search_text' ) ?>
|
<?//= input ( 'int_id >=' , 'text' , 'gte_int_id' ) ?>

<br>
dates:
<?= input ( 'from' , 'text' , 'filter__gte_created_at'  ,null , "title='YYYY-MM-DD'") ?>
<?= input ( 'to' , 'text' , 'filter__lte_created_at' ,null , "title='YYYY-MM-DD'") ?>
|

status:
<?= input ( 'all' , 'checkbox' , 'filter__in_status_all' , "all" , " onclick='updateAllStatus(this)'") ?>
<span id='status_elem'>
<span style='color:red'><?= input ( 'error' , 'checkbox' , 'filter__in_status_err' , "-1" ) ?></span>
<span style='color:orange'><?= input ( 'import' , 'checkbox' , 'filter__in_type_0' , "0") ?></span>
<span style='color:#FDD017'><?= input ( 'convert' , 'checkbox' , 'filter__in_type_1' , "1") ?></span>
<span style='color:green'><?= input ( 'ready' , 'checkbox' , 'filter__in_type_2' , "2") ?></span>
<span style='color:gray'><?= input ( 'deleted' , 'checkbox' , 'filter__in_type_3' , "3") ?></span>
<span style='color:Violet'><?= input ( 'pre-moderate' , 'checkbox' , 'filter__in_type_6' , "6") ?></span>
</span>
|
<input type=button name='go' value='go' onclick='updateParams ( false )'>
<input type=button name='refresh' value='refresh' onclick='updateParams ( true )'>
<?//= input ( 'is playlist' , 'checkbox' , 'is_playlist' , "1") ?>
<?//= input ( 'playlist id' , 'text' , 'playlist_id' ) ?>

</div>

<div id='dynamic'>
<? require_once ( 'galleryPartialSuccess.php' ) ?>
</div id='dynamic'>
</form>
<div id='widget' style='position: absolute; left: 1200px; top: 200px'>
<!--  widget  -->
<?
if ( $is_playlist )
{
	echo myPlaylistUtils::getEmbedCode ( $playlist_id , $widget_id , $ui_conf_id );
}
else
{
	if ( $widget ) echo $widget->getWidgetHtml( "kaltura_player" );
	else echo "No widget to display";
}
?>
</div>
<script type="text/javascript">
<!--
jQuery.noConflict();
updateAllStatus(document.getElementById("filter__in_status_all"));
updateAllType(document.getElementById("filter__in_type_all"));
//-->
</script>


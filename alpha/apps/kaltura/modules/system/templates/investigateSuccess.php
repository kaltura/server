<?php

?>
<div style="font-family: arial; font-size:12px; ">
<script type="text/javascript">
jQuery.noConflict();
function show ( elem , ev )
{
//	alert ( "1" );
	e = jQuery ( elem );
	t = e.find( "textarea" ).text() ;
		
	_the_text = jQuery ( "#the_text" );
	text_area  = _the_text.find ( "textarea" );
	text_area.text ( t );

	mouse_y = ev.pageY;	
	mouse_x = ev.pageX;
	_the_text.css ("top" , mouse_y -300 ); // 600 is the height of the window
	_the_text.css ("left" , mouse_x - 700 ); 
	_the_text.css ("background-color" , "#D8D8D8" );
	
	_the_text.css ( "display" , "block" );
}

function closeText()
{
	_the_text = jQuery ( "#the_text" );
	_the_text.css ( "display" , "none" );
}

function restartJob(url)
{
	if (confirm("Restart Job?"))
		document.location = url;
}

function reconvert ( url )
{
	if (confirm("Reconvert ?"))
		document.location = url;
}

function resendNotification(url)
{
	if (confirm("Resend Notification?"))
		document.location = url;
}

function update ( val , id , property_name )
{
//	val = elem.innerHTML; 
	var new_val = prompt ( "Change propery '" +  property_name + "' from [" + val + "]" );
	if ( new_val )
	{
		res = confirm  ("are you sure you'd like to update propery '" +  property_name + "' with value [" + new_val + "] ??" );
		if ( res )
		{
			url = '<?php echo url_for ( "/system" )  ?>' + "/executeCommand?command=updateEntry&id=" + id + "&name=" + property_name + "&value=" + new_val;
			// ajax for the poor :-()
			var temp_image = new Image();
			temp_image.src = url; 
			
			setTimeout ( 'delayedReload()' , 1000 );
		}
	} 
}

var conv_window = null;
function conversionProfileMgr ( conv_quality )
{
	partner_id = '<?php echo $entry ? $entry->partnerId : "" ?>';
	url = '<?php echo url_for ( "/system" )  ?>' + "/conversionProfileMgr?go=go&filter__eq_partner_id=" + partner_id + "&filter__eq_profile_type=" + conv_quality;
	conv_window = window.open ( url , "conv_window" );
}

function delayedReload()
{
	window.location.reload();
}

function toggleBjDiv ( bj_elem )
{
	jq_bj_elem = jQuery ( bj_elem );
	current_max_height = jq_bj_elem.css("max-height");
	if ( current_max_height == "50px" )
		jq_bj_elem.css("max-height","1500px");
	else
		jq_bj_elem.css("max-height","50px");	
}


function toggleDisplay ( elem_id )
{
	jq_elem = jQuery ("#" + elem_id );

	if( jq_elem.css ("display" ) == "none" )
		jq_elem.css( "display" , "" );
	else 
		jq_elem.css( "display" , "none" );
}

function toggleFileSyncLink ( link_id ,e )
{
	jq_elem = jQuery ("#file_sync_link_id_" + link_id );
	file_sync_viewer_elem =  jQuery ("#file_sync_link" );

	mouse_y = e.pageY;	
	file_sync_viewer_elem.css ("top" , mouse_y - 25 );
	file_sync_viewer_elem.css ("background-color" , "lightgray" );

	file_sync_viewer_elem.html ( jq_elem.html() ) ;
	showElem ( file_sync_viewer_elem );
}

function hideElem ( elem )
{
	jq_elem = jQuery ( elem );
	jq_elem.css( "display" , "none" );
}

function showElem ( elem )
{
	jq_elem = jQuery ( elem );
	jq_elem.css( "display" , "" );
}
</script>

<span style="display:none;position:absolute;top:100px; width:400; height:600;" id="the_text">
<textarea style="border:3px solid #D8D8D8; padding: 5px; " cols=80 rows=30></textarea>
<button onclick="closeText()">X</button>
</span>

<a href="/index.php/system/login?exit=true">logout</a> DC id: [<?php echo kDataCenterMgr::getCurrentDcId()  ?>] Machine: [<?php echo $_SERVER["SERVER_NAME"] ?>]  Time on Machine: <?php echo date ( "Y-m-d H:i:s." , time() ) ?>
<br>
<form action="./investigate">
	Entry Id: <input type="text" name="entry_id" value="<?php echo $entry_id ?>">
	
	Kshow Id: <input type="text" name="kshow_id" value="<?php echo $kshow_id ?>">
	
	<a href="./editPending?kshow_id=<?php echo $kshow_id ?>&entry_id=<?php echo $entry_id ?>">editPending</a>
<br>	
<input type="submit" id="Go" name="Go" value="Go"/>
Fast <input type="checkbox" name="fast" <?php echo  $fast ? "checked='checked'" : "" ?>>
</form>


<?php 
if ( $kshow ) { ?>
Kshow:
<br>

<table border=1 cellspacing=0	<?php echo investigate::printKshowHeader() . " " . investigate::printKshow( $kshow ) ?> 
</table>

<br>
<table border=1 cellspacing=0	>
	<?php echo investigate::printEntryHeader() . " " 
		 . investigate::printEntry ( $kshow_original->getShowEntry() , true  , $kshow_original , "RC") 
		 . investigate::printEntry ( $kshow_original->getIntro() , true  , $kshow_original  , "INTRO") 
		 . investigate::printEntry ( $bg_entry , true  , $kshow_original , "BG" ) ;
	?>

<?php

if ( $kshow_entries )
{
	foreach ( $kshow_entries as $entry )
	{
		echo ( investigate::printEntry ( $entry , true , $kshow_original ) );
	}
}	
?>
</table>

<?php	return; } ?>

<?php
if ( !$entry )
{
	echo $result;
	return;
}?>
Entry:
<br>
<span style='color:red'><?php echo  $error ?></span>
<br>
<table border=1 cellspacing=0	>
	<?php echo investigate::printEntryHeader() . " " . investigate::printEntry ( $entry ) ?>
</table>



<br>
Link to download: <br>
www: <a href='<?php echo str_replace( "cdn" , "www" , $entry->dataUrl )?>'><?php echo str_replace( "cdn" , "www" , $entry->dataUrl ) ?></a><br>
cdn: <a href='<?php echo $entry->dataUrl ?>'><?php echo $entry->dataUrl ?></a><br>
raw: <a href='<?php echo $entry->downloadUrl ?>'><?php echo $entry->downloadUrl ?></a><br>
<br>

Track Entry<br>
<?php
echo "<table border=1  cellspacing=0 style='font-size:11px;'>";
echo investigate::printTrackEntryHeader();
foreach ( $track_entry_list as $te )
{
	echo investigate::printTrackEntryParams( $te );	
}
echo "</table><br/>"; 
?>

Files on disk<br>
<?php
echo "<table border=1  cellspacing=0>";
echo investigate::printFileSyncHeader();
foreach ( $file_syncs_by_sub_type as $sub_type => $fs_list )
{
	foreach ( $fs_list as $fs )
	{
		echo investigate::printFileSync( $fs );	
	}
}
echo "</table><br/>"; 
?>


<div>
Flavors Assets (<?php echo count($flavors) ?>):
<table border=1 style='margin: 10px 2px 6px; border: 1px solid #B7BABC; border-collapse: collapse'> 
<thead>
<?php 
//var_dump ( $flavors_file_syncs );
//var_dump ( $flavors );
$i=0;

echo investigate::printFlavorAssetHeader();
echo "</thead>";
foreach ( $flavors as $fa )
{
	$color = ( $i %2 == 0 ? "#E0E0E0" : "#F8F8F8 " );
 	$tr_style="background-color:$color; margin: 10px 1 6px; " ;
	$css_class = ( $i %2 == 0 ? "tr_even" : "tr_odd" );
	echo investigate::printFlavorAsset( $fa , $tr_style );
	
	echo "<tr class='$css_class'  style='$tr_style; display:none' id='flavor_asset_details_{$fa->getId()}' >" . 
		"<td colspan='" . count(investigate::$flavorAssetParams) . "' >" ; // the extra related objects 

		// link to clipper:
		$flavor_clipper_url = str_replace( "www" , "cdn" , $entry->dataUrl );
		
		$flavor_clipper_url .= "/flavor/" . $fa->getId();
		 
		echo "<table border=0  cellspacing=0>";
		echo "<tr><td>clipper link</td><td> <a target='clipper' href='$flavor_clipper_url'>$flavor_clipper_url</a></td></tr>";
		echo "</table>";
		
		// file sync list
		echo "<table border=0  cellspacing=0>";
		$flavor_file_syncs = @$flavors_file_syncs[$fa->getId()];
		if ( $flavor_file_syncs )	
		{
			echo "<tr><td>file sync</td><td><table border=1  cellspacing=0>";
			echo investigate::printFileSyncHeader();
			foreach ( $flavor_file_syncs as $fs )
			{
				echo investigate::printFileSync( $fs );	
			}	
			echo "</td></tr></table>";	
		}
		
		// media info
		echo "<tr><td>media info</td><td><table border=1  cellspacing=0>";
		echo investigate::printMediaInfoHeader();
		echo investigate::printMediaInfo( $fa->getMediaInfos() );
		echo "</td></tr></table>";	
		
		// flavor params
		echo "<tr><td>flavor params</td><td><table border=1  cellspacing=0>";
		echo investigate::printFlavorParamsHeader();
		echo investigate::printFlavorParams( $fa->getflavorparams() );
		echo "</td></tr></table>";	
		
		// flavor params output
		echo "<tr><td>flavor params output</td><td><table border=1  cellspacing=0>";
		echo investigate::printFlavorParamsOutputHeader();
		echo investigate::printFlavorParamsOutputs( $fa->getflavorparamsoutputs() );
		echo "</td></tr></table>";	
		echo "</table>";

	echo "</td></tr>";
	
	$i++;
} 
?>
</table>
</div>
<br>
Batch Jobs (<?php echo count ($batch_jobs) ?>):
<br>
<table border=1 cellspacing=0>
<?php echo investigate::printBatchjobHeader() ?>
	<?php $i=0 ; foreach ( $batch_jobs as $bj ) {
		$i++;
		$color = ( $i %2 == 0 ? "#E0E0E0" : "#F8F8F8 " );
 		$tr_style="background-color:$color; margin: 10px 1 6px; " ;
		
		echo investigate::printBatchjob( $bj , false , $tr_style );
	} ?>
</table>


<br>
Old Conversions (<?php echo count ($conversions) ?>):
<br>
<table border=1 cellspacing=0 >
	<?php echo investigate::printConversionHeader(  $entry->id , ($entry->mediaType == 1 ) && $entry->status >= 1 ) ; ?>
	<?php foreach ( $conversions as $conversion ) { 
			echo investigate::printConversion( $conversion , $entry->id ,  false , ($entry->type == 1 ) );
	} ?>
</table>

<div style='display:none'>
	
<?php	
	foreach ( $file_sync_links as $fsl )
	{
		echo "<div id='file_sync_link_id_{$fsl->getId()}'>";		
		echo "<table border=1  cellspacing=0>";
		echo investigate::printFileSyncHeader();
		echo investigate::printFileSync( $fsl );	
		echo "</table>";
		echo "</div>";
	}
?>
</div>

<div id='file_sync_link' style='display:none;position:absolute;left:10px;top:10px;cursor:pointer;' onclick='hideElem(this)'></div>
<br>
</div>
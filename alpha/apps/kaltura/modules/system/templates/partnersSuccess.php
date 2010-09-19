<?php


function createPackageList()
{
	$partnerPackages = new PartnerPackages();
	$packages = $partnerPackages->listPackages();
	$partner_package_list = array();
	foreach($packages as $package)
	{
		$partner_package_list[$package['id']] = $package['name'];
	}
	return $partner_package_list;
}

function createSelect ( $id , $name , $default_value , $list_name )
{
	$prefix = "partner_";
	
	$host = requestUtils::getHost();

	// TODO - all static lists should move out of this function !!
	if ( strpos ( $host , "localhost" ) != false  )
	{
		$limited = false;
	}
	else if ( strpos ( $host , "kaldev" ) !== false  )
	{
		$limited = false;
	}
	else
	{
		$limited = true;
	}

	
	//global $arrays;
	$media_type_list = array ( "1" => "Video" , "2" => "Image" , "5" => "Audio");
	$media_source_list = array ( "20" => "Kaltura" , "21" => "MyClips" , "23" => "KalturaPartner" , "1" => "* File" , "2" => "* Webcam" , "3" => "Flickr" , "4" => "YouTube" , "5" => "* URL" , "7" => "MySpace" , "8" =>
									"PhotoBucket" , "9" => "Jamendo" , "10" => "CCMixter" , "11" => "NYPL" , "12" => "Current" , "13" => "MediaCommons" , "22" => "Archive.org");
	if( $limited )
	{
		$format_list = array ( "1" => "JSON" , "2" => "XML" , "3" => "PHP" );
		if (strpos ( $host , "sandbox" ) !== false )
			$service_url_list = array ( "sandbox.kaltura.com" => "Sandbox", "www.kaltura.com" => "Kaltura" ) ;
		else	
			$service_url_list = array ( "www.kaltura.com" => "Kaltura", "sandbox.kaltura.com" => "Sandbox" ) ;
		$index_path_list = array ( "index.php" => "index"  ) ;
	}
	else
	{
		$format_list = array ( "1" => "JSON" , "2" => "XML" , "3" => "PHP" , "4" => "PHP_ARR" , "5" => "PHP_OBJ" );
		$service_url_list = array ( "localhost" => "localhost" , "kaldev.kaltura.com" => "kaldev" , "www.kaltura.com" => "Kaltura", "sandbox.kaltura.com" => "Sandbox" ) ;
		$index_path_list = array ( "index.php" => "index" , "kaltura_dev.php" => "debug" ) ;
	}

	$clazz_list = array ( "kshow" => "kshow" , "kuser" => "kuser" , "entry" => "entry" , "PuserKuser" => "PuserKuser" ) ;
	$moderation_object_type = array ( "1" => "kshow" , "2" => "entry" , "3" => "kuser" , "" => "none");
	$notification_status = array ( "" => "All" , "1" => "Pending" , "2" => "Sent" , "3" => "Error" , "4" => "Should Resend" );
	$entry_type = array ( "" => "All" , "1" => "Clip" , "2" => "Roughcut" );
	$entry_media_type = array ( "" => "All" , "1" => "Video" , "2" => "Image" , "5" => "Audio" , "6" => "Roughcut" );

	$boolean_type = array ( "" => "" , "true" => "true" , "false" => "false"  );
	$boolean_int_type = array ( "" => "" , "1" => "true" , "0" => "false"  );
	$partner_status_int_type = array ( "1" => "Normal" , "2" => "Content Blocked" , "3" => "Fully Blocked", "0" => "Deleted" );
	$partner_group_int_type = array ( "1" => "Publisher" , "2" => "VAR" , "3" => "Group" );
	
	$arrays = array ( "format_list" => $format_list , "media_type" => $media_type_list , "media_source" => $media_source_list ,
		"service_urls" => $service_url_list ,
		"service_urls1" => array_merge( array ( "" => "" ) , $service_url_list  ),
		"index_paths" => $index_path_list ,
		"clazz_list" => $clazz_list ,
		"moderation_object_type" => $moderation_object_type ,
		"boolean_type" => $boolean_type ,
		"boolean_int_type" => $boolean_int_type ,
		"notification_status" => $notification_status ,
		"partner_status_int_type" => $partner_status_int_type ,
		"partner_group_int_type" => $partner_group_int_type ,
		"appear_in_saerch_list" => array ( "0" => "Not at all" , "1" => "Partner only" , "2" => "Kaltura network" ) ,
		"net_storage_priority" => array ( 
			StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_ONLY => 'Kaltura DCs only',
			StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_FIRST => 'Kaltura first',
			StorageProfile::STORAGE_SERVE_PRIORITY_EXTERNAL_FIRST => 'External storages first',
			StorageProfile::STORAGE_SERVE_PRIORITY_EXTERNAL_ONLY => 'External storages only',
		) ,
		"partner_packages" => createPackageList(),
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
	$prefix = "partner_";
	
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
	if ( !$value && $value !== 0 ) $value = $default_value;
	return "<tr class='prop'><td>$getter_name</td>" . createInput ( $getter_name , $type  , $size  , $value  , $list_name  , $comment ) . "</tr>";
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

function savePartner()
{
	var res = confirm ( "Are you sure you want to save the new details ?? " );
	if ( res )
	{
		var cmd = $$("command");
		cmd.value="save";
	}
	else
	{
		return false;
	}
}

function findPartner()
{
	var search_text =document.getElementById("search_text").value;
	jQuery.ajax({
		url: "<?php echo url_for('system') ?>/findPartner?hint=" + search_text  ,
		type: "POST",
		dataType: "json",
		success: function(data) {
			if (data && data.length) {
				ui_confs_playlist = data;
			}
		}
	});
}
</script>

<form method="post">
<input type="hidden" id="command" name="command"> 
<div style="font-family: arial; font-size:12px; ">

Partner Id: <input name="partner_id" value='<?php echo  $partner_id ?>'>
<input type='submit'  name='go' value='go'/>
Text in name / description  <input id="search_text"  name="search_text" value='<?php echo  $search_text ?>'>
<input type='button' name='search' value='search' onclick='return findPartner();'>
<br><br>
<?php
if ( count ( $partner_list ) > 1 )
{
	// multiple partners
	// let select the partner from a list
}
?>
<table>
<tr><td style="font-weight:bold; text-" colspan="3"><hr/>General</td></tr>
<?php echo  prop ( $partner , "status" , "select" , "1" , "3" , "partner_status_int_type" , "Partner Status" ) ?>
<?php echo  prop ( $partner , "partnerName"  , "text" , "70" , "" ) ?>
<?php echo  prop ( $partner , "description"  , "text" , "70" , "" ) ?>
<?php echo  prop ( $partner , "adminName"  , "text" , "70" , "" ) ?>
<?php echo  prop ( $partner , "adminEmail"  , "text" , "70" , "" ) ?>
<tr><td style="font-weight:bold; text-" colspan="3"><hr/>Notifications</td></tr>
<?php echo  prop ( $partner , "notify"  , "select" , "1" , "2" , "boolean_int_type" ,"Enable notifications" ) ?>
<?php echo  prop ( $partner , "url2"  , "text" , "70" , "" , null , "Will be used to send notifications") ?>
<?php echo  prop ( $partner , "notificationsConfig"  , "text" , "70" , "" ) ?>
<?php echo  prop ( $partner , "allowMultiNotification"  , "select" , "1" , "2" , "boolean_int_type" ,"Allow multiple notifications for a single http hit or leave them one-by-one" ) ?>

<tr><td style="font-weight:bold; text-" colspan="3"><hr/>Add entry</td></tr>
<?php echo  prop ( $partner , "allowQuickEdit"  , "select" , "1" , "2" , "boolean_int_type" ,"Allows to add entries to a kshow" ) ?>
<?php echo  prop ( $partner , "useDefaultKshow"  , "select" , "1" , "2" , "boolean_int_type" ,"Allow on-the-fly creation of kshows in addentry (kahos_id = -1 | -2)" ) ?>
<?php echo  prop ( $partner , "shouldForceUniqueKshow"  , "select" , "1" , "2" , "boolean_int_type" ,"Should return an error if a kshow of this name already exists" ) ?>
<?php echo  prop ( $partner , "returnDuplicateKshow"  , "select" , "1" , "2" , "boolean_int_type" ,"If for a kshow a duplicate was found but duplicates are not allowed - should return the original one in the error" ) ?>

<?php echo  prop ( $partner , "appearInSearch"  , "select" , "1" , "0" , "appear_in_saerch_list" ,"Policy for new entries to appear in the saerch " ) ?>
<?php echo  prop ( $partner , "moderateContent"  , "select" , "1" , "0" , "boolean_int_type" ,"Moderation policy for new entries" ) ?>

<tr><td style="font-weight:bold; text-" colspan="3"><hr/>Conversion</td></tr>
<?php
	/*= prop ( $partner , "conversionString"  , "text" , "70" , ""  , "" , "Some oprions:<BR>" . myFileConverter::NO_COVERSION . "<BR>-b 500kb -r 25 -g 5 -s 400x300 -ar 22050 -ac 2 -y <BR>" .
	"-sameq  -r 25 -g 25 -s 400x300 -ar 22050 -ac 2 -y<BR>" . 
	"-b 500kb -r 25 -g 5 -s 400x{height} -ar 22050 -ac 2 -y <BR>" .
	"-b 500kb -r 25 -g 5 -s {width}x{height} -ar 22050 -ac 2 -y <BR>") */ 
?>
<?php
	/*= prop ( $partner , "flvConversionString"  , "text" , "70" , ""  , ""  ) */
?>
<?php echo  prop ( $partner , "defConversionProfileType"  , "text" , "15" , ""  , "" , "*DEPRECATED* should be used INSTEAD of the conversion string"  ) ?>
<?php echo  prop ( $partner , "currentConversionProfileType"  , "text" , "15" , ""  , "" , "*DEPRECATED* the last conversion profile set for this partner from the kmc"  ) ?>
<?php echo  prop ( $partner , "defaultConversionProfileId"  , "text" , "15" , ""  , "" , "the last conversion profile id for this partner from the kmc"  ) ?>
<?php echo  prop ( $partner , "defThumbOffset"  , "text" , "3" , "3"  , "" , "default second from witch to take thumbnail for video"  ) ?>
<?php
/*
<?php echo  prop ( $partner , "conversionString2"  , "text" , "70" , ""  , ""  ) ?>
<?php echo  prop ( $partner , "conversionString3"  , "text" , "70" , ""  , ""  ) ?>
<?php echo  prop ( $partner , "conversionString4"  , "text" , "70" , ""  , ""  ) ?>
<?php echo  prop ( $partner , "conversionString5"  , "text" , "70" , ""  , ""  ) ?>
*/
?>

<tr><td style="font-weight:bold; text-" colspan="3"><hr/>Var/Group Settings</td></tr>
<?php echo  prop ( $partner , "partnerGroupType" , "select" , "1" , "3" , "partner_group_int_type" , "Partner Group Type" ) ?>
<?php echo  prop ( $partner , "partnerParentId"  , "text" , "30" , ""  , "" , "ID of parent partner. TO CLEAR THE FIELD - SEND `-1000`"  ) ?>

<tr><td style="font-weight:bold; text-" colspan="3"><hr/>Misc</td></tr>
<?php echo  prop ( $partner , "mergeEntryLists"  , "select" , "1" , "0" , "boolean_int_type" ,"for service getallentries- merge the kshow & user lists" ) ?>
<?php echo  prop ( $partner , "allowLks"  , "select" , "1" , "0" , "boolean_int_type" ,"enables the lite-ks feature" ) ?>
<?php echo  prop ( $partner , "matchIp"  , "text" , "30" , ""  , "" , "ip to match for specific services"  ) ?>
<?php echo  prop ( $partner , "allowAnonymousRanking"  , "select" , "1" , "0" , "boolean_int_type" ,"enables anonymous comments" ) ?>
<?php echo  prop ( $partner , "landingPage"  , "text" , "60" , ""  , "" ,  "landing page for the entry on the partner's site"  ) ?>
<?php echo  prop ( $partner , "userLandingPage"  , "text" , "60" , ""  , "" , "landing page for the user on the partner's site"  ) ?>
<?php echo  prop ( $partner , "serviceConfigId"  , "text" , "60" , ""  , "" , "configuration file to use for partner"  ) ?>
<?php echo  prop ( $partner , "partnerPackage"  , "select" , "1" , "0"  , "partner_packages" , "package type of partner"  ) ?>
<?php echo  prop ( $partner , "monitorUsage"  , "select" , "1" , "0"  , "boolean_int_type" , "when true, partner will be included in daily usage monitoring and will get warning emails."  ) ?>
<?php echo  prop ( $partner , "isFirstLogin"  , "select" , "1" , "0"  , "boolean_int_type" , "when true, on next login to kmc a different msg will be displayed"  ) ?>
<?php echo  prop ( $partner , "templatePartnerId"  , "text" , "" , ""  , "" , "partner id to fetch uiconftemplates from"  ) ?>
<?php echo  prop ( $partner , "addEntryMaxFiles"  , "text" , "" , ""  , "" , "limit add-entry files per call"  ) ?>
<?php echo  prop ( $partner , "partnerSpecificServices"  , "text" , "" , ""  , "" , "Class name for partner's specific services"  ) ?>
<?php echo  prop ( $partner , "appStudioExampleEntry"  , "text" , "60" , ""  , "" ,  "appstudio: default Entry when creating a new playlist"  ) ?>
<?php echo  prop ( $partner , "appStudioExamplePlayList0"  , "text" , "60" , ""  , "" ,  "appstudio: default playlist 0 id for multiple playlists"  ) ?>
<?php echo  prop ( $partner , "appStudioExamplePlayList1"  , "text" , "60" , ""  , "" ,  "appstudio: default playlist 1 id for multiple playlists"  ) ?>
<?php echo  prop ( $partner , "delivryBlockCountries"  , "text" , "" , ""  , "" , "country code to block file-serving for users from that country - to have no block at all put DONT_BLOCK"  ) ?>


<tr><td style="font-weight:bold; text-" colspan="3"><hr/>Files Storage and Delivery</td></tr>
<?php echo  prop ( $partner , "host"  , "text" , "60" , ""  , ""  , "host url for all the services"  ) ?>
<?php echo  prop ( $partner , "cdnHost"  , "text" , "60" , ""  , ""  , "cdn url for all the content"  ) ?>
<?php echo  prop ( $partner , "rtmpUrl"  , "text" , "60" , ""  , ""  , "RTMP url (rtmp://partner.url/ondemand)"  ) ?>
<?php echo  prop ( $partner , "storageDeleteFromKaltura"  , "select" , "1" , "0"  , "boolean_int_type" , "Indicates if file should be deleted from kaltura data centers after exporting to external storage"  ) ?>
<?php echo  prop ( $partner , "storageServePriority"  , "select" , "1" , "1"  , "net_storage_priority" , "Indicates what storage will be used for files delivery"  ) ?>

<tr><td style="font-weight:bold; text-" colspan="3"><hr/>Password Security</td></tr>
<?php echo  prop ( $partner , "maxLoginAttempts"  , "text" , "60" , ""  , ""  , "maximum login attempts before being blocked"  ) ?>
<?php echo  prop ( $partner , "loginBlockPeriod"  , "text" , "60" , ""  , ""  , "for how long is login block valid"  ) ?>
<?php echo  prop ( $partner , "numPrevPassToKeep"  , "text" , "60" , ""  , ""  , "number of previous passwords to keep (and not allow re-use)"  ) ?>
<?php echo  prop ( $partner , "passReplaceFreq"  , "text" , "60" , ""  , ""  , "password replacement frequency"  ) ?>



<tr><td style="font-weight:bold; text-" colspan="3"><hr/>Andromeda - KMC2</td></tr>
<tr class="prop">
	<td>kmcVersion</td>
<?php
$version = 2;
if($partner)
	$version = $partner->getKmcVersion();
	
$version_text = (version_compare($version, 2, "<"))? "Old KMC": "New KMC";
?>
	<td>
		<input type="text" value="<?php echo $version; ?>" readonly="readonly" />
		<span style="color: blue;"><?php echo $version_text; ?></span>
	</td>
	<td style="color:gray; font-size:11px; font-family:arial;">THIS CANNOT BE EDITED HERE, TO MIGRATE OLD PARTNER TO KMC 2 PLEASE CONTACT R&D</td>
</tr>
<?php echo  prop ( $partner , "liveStreamEnabled"  , "select" , "1" , "0"  , "boolean_int_type" , "Live-Stream enabled"  ) ?>
<?php echo  prop ( $partner , "enableAnalyticsTab"  , "select" , "1" , "0"  , "boolean_int_type" , "Reports and Analytics tab enabled"  ) ?>
<?php echo  prop ( $partner , "enableSilverLight"  , "select" , "1" , "0"  , "boolean_int_type" , "Silver Light players enabled"  ) ?>
<?php echo  prop ( $partner , "enableVast"  , "select" , "1" , "0"  , "boolean_int_type" , "VAST support in app-studio enabled"  ) ?>
<?php echo  prop ( $partner , "enable508Players"  , "select" , "1" , "0"  , "boolean_int_type" , "508 players in the KMC preview&embed are available"  ) ?>
<tr class='prop'>
	<td>Enable Metadata</td>
	<td>
		<select id="partner_enableMetadata" style="font-family:arial; font-size:12px;" name="partner_enableMetadata" onkeyup="updateSelect( this )" onchange="updateSelect( this )">
		<?php 
			$list = array ( "" => "" , "1" => "true" , "0" => "false"  );
		
			$default_value = false;
			if($partner)
				$default_value = $partner->getPluginEnabled(MetadataPlugin::PLUGIN_NAME);
				
			$default_value_selected = "";
			foreach ( $list as $value => $option  )
			{
				// not always the default value is found 
				if ( $value == $default_value ) $default_value_selected = $default_value;
				$selected = ($value == $default_value ) ? 'selected="selected"' : '' ;
				echo "<option value=\"$value\" $selected>$option</option>\n";
			}
		?>
		</select>
		<span style="color:blue;" id="partner_enableMetadata_current_value"><?php echo $default_value_selected; ?></span>
	</td>
	<td style="color:gray; font-size:11px; font-family:arial;">
		Custom data enabled
	</td>
</tr>
</table>
</div>

<input type="submit" name="save" value="save" onclick="return savePartner()">
</form>
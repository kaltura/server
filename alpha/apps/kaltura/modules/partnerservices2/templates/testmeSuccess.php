<?php

define ( 'TESTME_GROUP_START' , 'TESTME_GROUP_START' );
define ( 'TESTME_GROUP_END' , 'TESTME_GROUP_END' );

$SERVICE_URL = "http://localhost/kaltura_dev.php/partnerservices2/";

function getLimited()
{
	$host = requestUtils::getHost();

	// TODO - all static lists should move out of this function !!
	if ( strpos ( $host , "www.kaltura.com" ) !== false  )
	{
		$limited = true;
	}
	else if ( strpos ( $host , "sandbox.kaltura.com" ) !== false  )
	{
		$limited = true;
	}
	else
	{
		$limited = kConf::get('testmeconsole_state');
	}

	return $limited;
}

function createSelect ( $id , $name , $default_value , $list_name , $pid_str = null )
{
	$host = requestUtils::getHost();
	$limited = getLimited();

	//global $arrays;
	$download_file_formats = array ( "avi" => "avi" , "mp4" => "mp4", "mov" => "mov" , "flv" => "flv" , "mp3" => "mp3" , "3gp" => "3gp" , "original" => "original" );
	$media_type_list = array ( "1" => "Video" , "2" => "Image" , "5" => "Audio", "11" => "Document", "12" => "Swf" , "-1" => "Automatic" );
	$media_source_list = array ( "20" => "Kaltura" , "21" => "MyClips" , "23" => "KalturaPartner" , "26" => "KalturaKshow" , "27" => "KalturaPartnerKshow" , 
									"1" => "* File" , "2" => "* Webcam" , "3" => "Flickr" , "4" => "YouTube" , "5" => "* URL" , "7" => "MySpace" , "8" =>
									"PhotoBucket" , "9" => "Jamendo" , "10" => "CCMixter" , "11" => "NYPL" , "13" => "MediaCommons" , "22" => "Archive.org" ,
									"24"  => "MetaCafe" );
	if( !$limited )								
		$media_source_list = array ( "25" => "KalturaQa") + $media_source_list ;									

	$current_server = str_replace("http://", "", requestUtils::getRequestHost());
	$service_url_list[$current_server] = $current_server;
		
	if( $limited )
	{
		
		$format_list = array ( "1" => "JSON" , "2" => "XML" , "3" => "PHP" );
		if (strpos ( $host , "sandbox" ) !== false )
		{
			$service_url_list["sandbox.kaltura.com"] = "Sandbox";
			$service_url_list["www.kaltura.com"] = "Kaltura";
		}
		else
		{
			$service_url_list["www.kaltura.com"] = "Kaltura";
			$service_url_list["sandbox.kaltura.com"] = "Sandbox";
		}
		
		$index_path_list = array ( "index.php" => "index"  ) ;
	}
	else
	{
		$format_list = array ( "1" => "JSON" , "2" => "XML" , "3" => "PHP" , "4" => "PHP_ARR" , "5" => "PHP_OBJ" , "8" => "mRSS");
		$service_url_list = array_merge ( $service_url_list , array ( "localhost" => "localhost" , "qac.kaltura.dev" => "qac" , "kelev.kaltura.com:9007" => "qac-external" , "kaldev.kaltura.com" => "kaldev" , "www.kaltura.com" => "Kaltura", "sandbox.kaltura.com" => "Sandbox" ) );
		$index_path_list = array ( "index.php" => "index" , "kaltura_dev.php" => "debug" ) ;
		
		$service_url_list["debian.kaltura.dev/kalturaCE"] = "debian";
		
	}

	$clazz_list = array ( "kshow" => "kshow" , "kuser" => "kuser" , "entry" => "entry" , "PuserKuser" => "PuserKuser" ) ;
	$moderation_object_type = array ( "1" => "kshow" , "2" => "entry" , "3" => "kuser" , "" => "none");
	$moderation_status = array ( "1" => "pending" , "2" => "allow" , "3" => "block" , "4" => "delete", "5" => "review");
	$notification_status = array ( "" => "All" , "1" => "Pending" , "2" => "Sent" , "3" => "Error" , "4" => "Should Resend" );
	$entry_status = array ( "" => "All" , 
		"-1" => "ERROR_CONVERTING",
		"0" => "IMPORT",
		"1" => "PRECONVERT",
		"2" => "READY",
		"3" => "DELETED",
		"4" => "PENDING",
		"5" => "MODERATE",
		"6" => "BLOCKED");

	$entry_type = array ( "" => "All" , "1" => "Clip" , "2" => "Roughcut", "10" => "Document", "-1" => "Automatic" );
	$entry_media_type = array ( "1" => "Video" , "2" => "Image" , "5" => "Audio" , "6" => "Roughcut" , "10" => "XML", "11" => "Document", "-1" => "Automatic");
	$entry_media_type_filter = array( "" => "All" ,"1" => "Video" , "2" => "Image" , "5" => "Audio" , "6" => "Roughcut" , "10" => "XML", "11" => "Document", "-1" => "Automatic");
	
	$widget_security_type = array ( "1" => "none" , "2" => "timehash" );
	$entries_list_type = array ( "15" => "All" , "1" => "Kshow" , "2" => "Kuser" , "4" => "Roughcut" , "8" => "Episode" );

	$boolean_type = array ( "true" => "true" , "false" => "false"  );
	$boolean_int_type = array ( "" => "" , "1" => "true" , "0" => "false"  );
	$display_in_search_filter = array ( "" => "All" , "0" => "Not displayed" , "1" => "In Partner"  , "2" => "Kaltura Network" );
	
	$usage_graph_resolutions = array ( "days" => "days", "months" => "months" );
	$months_list = array ( "1" => "1", 
		"2" => "2", 
		"3" => "3", 
		"4" => "4",
		"5" => "5", 
		"6" => "6", 
		"7" => "7", 
		"8" => "8", 
		"9" => "9", 
		"10" => "10", 
		"11" => "11", 
		"12" => "12" );

	$obj_type_list = array ( "kshow" => "kshow" , "entry" => "entry" );
	
	// TODO - fix list for moderation_status
	$entry_moderation_status = array ( "1" => "PENDING" , "2" => "ALLOW" , "3" => "BLOCK");
	$entry_moderation_status_filter = array ( "" => "All" , "1" => "PENDING" , "2" => "ALLOW" , "3" => "BLOCK");
	
	$arrays = array ( "format_list" => $format_list , "media_type" => $media_type_list , "media_source" => $media_source_list ,
		"download_file_formats" => $download_file_formats,
		"service_urls" => $service_url_list ,
		"service_urls1" => array_merge( array ( "" => "" ) , $service_url_list  ),
		"index_paths" => $index_path_list ,
		"clazz_list" => $clazz_list ,
		"moderation_object_type" => $moderation_object_type ,
		"moderation_status" => $moderation_status ,
		"boolean_type" => $boolean_type ,
		"boolean_int_type" => $boolean_int_type ,
		"notification_status" => $notification_status ,
		"notification_type" => array_merge ( array ( "" => "All" )  , kNotificationJobData::getNotificationTypeMap() ) ,
		"entry_media_type" => $entry_media_type ,
		"entry_media_type_filter" => $entry_media_type_filter ,
		"entry_type" => $entry_type ,
		"entry_status" => $entry_status ,
		"widget_security_type" => $widget_security_type ,
		"entries_list_type" => $entries_list_type ,
		"entry_moderation_status_filter" => $entry_moderation_status_filter ,
		"entry_moderation_status" =>$entry_moderation_status , 
		"entries_filter_order_by" => array ( "" => "None" ,
			"+id" => "id asc" , "-id" => "id desc" , 
			"+created_at" => "created_at asc" , "-created_at" => "created_at desc",
			"+media_date" => "media_date asc" , "-media_date" => "media_date desc",
			"+name" => "name asc" , "-name" => "name desc",
			"+views" => "views asc" , "-views" => "views desc" , 
			"+type" => "type asc" , "-type" => "type desc" ,
			"+media_type" => "media_type asc" , "-media_type" => "media_type desc" ,
			"+plays" => "plays asc" , "-plays" => "plays desc" , 
			"+views" => "views asc" , "-views" => "views desc" ,
			"+rank" => "rank asc" , "-rank" => "rank desc" ,
			"+moderation_count" => "moderation_count asc" , "-moderation_count" => "moderation_count desc" ,
			"+moderation_status" => "moderation_status asc" , "-moderation_status" => "moderation_status desc" ) ,
			"obj_type_list" => $obj_type_list , 
		"command_list" => array ( "view" => "view" , "play" => "play" , "viewEnd" => "viewEnd" ) ,
		"display_in_search_filter" => $display_in_search_filter ,
		"playlist_media_type" => array ( "10" => "Dynamic" , "3" => "Static" , "101" => "External" ) ,
		"playlist_media_type_filter" => array ( "" => "All" , "10" => "Dynamic" , "3" => "Static" , "101" => "External" ) ,
		
		"conversion_profile_type" => array ( "low" => "low" , "med" => "med" , "high" => "high" , "hd" => "HD" ) ,
		"conversion_profile_aspect_ratio" => array ( "1" => "keep aspect ratio" , "2" => "keep original size" , "3" => "4:3" , "4" => "16:9" ) ,
		"uiconf_obj_type" => array ( "1" => "kdp" , "2" => "kcw" , "3" => "kse" , "4" => "kae" , "6" => "app-studio" ) ,
		"uiconf_obj_type_filter" => array ( "" => "ALL" , "1" => "kdp" , "2" => "kcw" , "3" => "kse" , "4" => "kae" , "6" => "app-studio" ) ,
		"uiconf_filter_order_by" => array ( "" => "None" ,
			"+id" => "id asc" , "-id" => "id desc" , 
			"+created_at" => "created_at asc" , "-created_at" => "created_at desc",
			"+updated_at" => "updated_at asc" , "-updated_at" => "updated_at desc",) ,
		"conversion_quality" => array ( "" => "DEFAULT" , "low" => "low" , "med" => "medium" , "high" => "high" , "hd" => "hd" ),
		"download_job_type_filter" => array ( "" => "ALL" , "3" => "flatten" , "6" => "download" ),
		"download_filter_order_by" => array ("" => "None" ,
			"+id" => "id asc" , "-id" => "id desc" , 
			"+created_at" => "created_at asc" , "-created_at" => "created_at desc", ),
		"uiconf_creation_mode" => array ( "1" => "Manual", "2" => "Wizard" , "3" => "Advance" ), 
		"months_list" => $months_list,
		"usage_graph_resolutions" => $usage_graph_resolutions,
	);


	$list = $arrays[$list_name];
if ( ! $list ) die ("<div>cannot find list of name: [$list_name]</div>" );
	//echo "createSelect: list_name:[$list_name] count:[" . count ( $list ) . "]<br>";

	$str = "<select id='$id' style='font-family:arial; font-size:12px;' name='$name' onkeyup='updateSelect( this )' onchange='updateSelect( this )' $pid_str>";

	$default_value_selected = "";
	foreach ( $list as $value => $option  )
	{
		// not always the default value is found 
		if ( $value == $default_value ) $default_value_selected = $default_value;
		if ( $default_value === "" )
		{
			$selected = ($value === "" ) ? "selected='selected'" : "" ;			
		}
		else
		{
			$selected = ($value == $default_value ) ? "selected='selected'" : "" ;
		}
		$str .= "<option value='$value' $selected >$option</option>\n";
	}
	$str .= "</select> <span style='color:blue;' id='{$id}_current_value'>$default_value_selected</span>\n";

	return $str;
}

$element_id = 0;
$pid_arr = array();
function createInput ( $name , $type="text" , $size=7 , $default_value=null , $list_name=null , $comment=null , $content_id=null , $checked = true )
{
	global $element_id, $pid_arr;
	$element_id++;
	$id = $name . "_" . $element_id;

	// a good enough unique algo for the path of the element (including the context_id)
	$pid = substr( md5($content_id.$name) , 0 , 3 );
//	if ( isset ( $pid_arr[$pid] ) ) echo "DUPLICATE PID $pid";
	$pid_arr[$pid]=$pid;
	$pid_str = " pid='$pid' ";
	
	if ( !$type ) $type="text";
	if ( !$size ) $size = 20;
	if ( !$default_value) $default_vlaue="";


	$copyToClipboard = "";
	
	if ($name == "ks" || $name == "ks2")
	{
		$copyToClipboard = "<a href='#' onclick='copyToClipboard(\"$id\"); return false;'>(copy)</a>";
	}
	
	// set the default "checked" for the radio to be according to $checked
	$str = "<tr>" .
		"<td ><input type='checkbox' " . ( $checked ? "checked=checked" : "" ). " class='shouldsend' sibling_id='{$id}' onclick='enableDisable ( this , \"{$id}\")'>".
		"$name:$copyToClipboard</td><td>" ;

	if ( $type == "select" )
	{
		$str .= createSelect ( $id, $name , $default_value , $list_name , $pid_str);
	}
	elseif ( $type == "textarea" )
	{
		@list ( $rows,$cols ) = explode ( "," , $size ) ;
		$str .= "<textarea id='$id' name='{$name}' rows='$rows' cols='$cols' $pid_str>{$default_value}</textarea>" ;
	}
	else
	{
		$str .= "<input id='$id' name='$name' id='$name' type='$type'' size='$size' value='$default_value' $pid_str>";
	}

	$str .= "</td><td style='color:gray; font-size:11px; font-family:arial;'>" . ( $comment ?  "* " . $comment : "&nbsp;" ) . "</td></tr>";
	return $str;
}


function createInputs ( $arr , $context_id )
{
	static $global_checked = true;
	static $group_prefix = "";
	$str = "";
	
	$is_ie = strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false ;
	
	if(!is_array($arr))
		return '';
		
	foreach ( $arr as $input )
	{
		if ( $input[0] == TESTME_GROUP_START )
		{
			// 0 - the TESTME_GROUP_START
			// 1 - group name (can be used as prefix for child fields)
			// 2 - should children be checkded by default
			// 3 - should children be expanded by default 
			$id = $context_id. "_" . @$input[1];
			$name = $input[1];
			$global_checked = @$input[2];
			$expanded = @$input[3];
			$str .= "<tr><td colspan=2>" .
				// select / unselect group 
				//   enableDisableChildren doesn't seem to work in ie - TODO - fix !
				( $is_ie ? "" :  
				"<input type='checkbox' " . ( $global_checked ? "checked=checked" : "" ). " class='shouldsend' sibling_id='{$id}' onclick='enableDisableChildren ( this , \"{$id}\")'>" ) .
				// open/close group
				"<button onclick=\"return showHideGroup ( this , '{$id}' );\">$name</button></td>" .   
				"</tr>";
			$str .= "<tr><td colspan=3><div style='" . ( $expanded ? "" : "display:none;" ) . "' id='$id'><table>";
			
			$group_prefix = @$input[1];
		}
		elseif  ( $input[0] == TESTME_GROUP_END )
		{
			$str .= "</table></div></td></tr>";
			$global_checked = true; // when exiting the score of a group - stick to checked=true
			$group_prefix = "";
		}
		else
		{
			$name = $input[0];
			if ( strpos ( $name , "." ) === 0 ) 
			{
				$name = $group_prefix . "_" . substr ( $name , 1 ) ;  // add the prefix to the name (ommit the '.')
			}
			$str .= createInput ( $name , @$input[1] , @$input[2] ,@$input[3] , @$input[4] , @$input[5] , $context_id , $global_checked );
		}
	}

	return $str;
}

$limited = getLimited();
?>

<?php require_once ( "testme_js.php" ) ?>

<span style="font-family:arial; font-size:13px;">
<form id="theform" method="post" action="<?php echo $SERVICE_URL ?>" target="target_frame">
<div>
<span style="background-color:yellow" >? See docs at: <a target="KalturaAPI" href="http://www.kaltura.com/wiki/index.php/KalturaAPI:main">KalturaAPI:main</a></span>
<table style="font-family:arial; font-size:13px;">
<?php
	$fields1 = array (
		array ( "service_url" , "select" , "" , "localhost" , "service_urls" ),
		array ( "index_path" , "select" , "" , "index" , "index_paths" ),
		array ( "format" , "select" , "" , "2" , "format_list" ),
		array ( "partner_id" , "" , "5" , "1" ) ,
		array ( "subp_id" , "" , "5" , "100") ,
		array ( "uid" , "" , "10" , "2" ) ,
		array ( "ks" , "" , "34" , "" ) ,
		array ( "ks2" , "" , "34" , "" ) ,
		array ( "kalsig" , "" , "34" , "" ) ,
	);
	
	if( ! $limited ) 
	{
		$fields1[] = array ( "add_benchmarks" , "select" , "" , "" , "boolean_int_type" );
		$fields1[] = array ( "nocache" , "select" , "" , "" , "boolean_int_type" );
	}

	echo createInputs ( $fields1 , "" ) ;

	echo "<tr><td></td><td><input type='submit' name='submit' value='submit' onclick='return submitForm()'>" .
			( $limited ? "" :
			" <button onclick='return switchKs()'>switchKs</button>" . 
			" <button style='color:#050;font-family:arial;font-size:11px; width:40px; border-style:none;' onMouseOver='over(this);' onMouseOut='out(this);' onclick='return save()'>save</button>".
			" <button style='color:#050;font-family:arial;font-size:11px; width:45px; border-style:none;' onMouseOver='over(this);' onMouseOut='out(this);' onclick='return restore(true)'>restore</button>" ) . 
		"</td></tr>";

?>

<?php if  ( !$limited) { // add the history selection ?>
<tr><td>url</td>
	<td><input id='submit_url' readonly='readonly' value='' size='40'></td>
</tr>

<tr><td>History</td><td>
<select id="history" pid="2" name="history" onkeyup="selectService( this , false )" onchange="selectService( this , false ) " style='font-family:arial; font-size:12px;'>
</select></td></tr>
<?php } ?>
<tr><td>Service</td><td>
<select id="service" pid="1" name="service" onkeyup="selectService( this , true )" onchange="selectService( this , true ) " style='font-family:arial; font-size:12px;'>
	<option value="">-- Select service --</option>

	<optgroup label="session">
		<option value="startsession">start session</option>
		<option value="startwidgetsession">start widget session</option>
	</optgroup>

	<optgroup  label="partner">
		<option value="registerpartner">register partner</option>
		<option value="getpartner">get partner</option>
		<option value="getpartnerinfo">get partner info</option>
		<option value="getpartnerusage">get partner usage</option>
		<option value="updatepartner">update partner</option>
		<option value="listpartnerpackages">list partner packages</option>
		<option value="adminlogin">admin login</option>
		<option value="resetadminpassword">reset admin password</option>
		<option value="updateadminpassword">update admin password</option>
		<option value="purchasepackage">purchase package</option>
	</optgroup>

	<optgroup label="search">
		<option value="searchmediaproviders">search media providers</option>
		<option value="searchauthdata">search auth data</option>
		<option value="search">search</option>
		<option value="searchfromurl">search from url</option>
		<option value="searchmediainfo">search media info</option>
		<option value="addsearchresult">add search result</option>
	</optgroup>

	<optgroup label="file/webcam">
		<option value="upload">upload</option>
		<option value="uploadjpeg">upload jpeg</option>
		<option value="webcamdummy">webcam DUMMY</option>
	</optgroup>

	<optgroup label="entry">
		<option value="addentry">add entry</option>
		<option value="getentry">get entry</option>
		<option value="updateentry">update entry</option>
		<option value="getentries">get entries</option>
		<option value="updateentrythumbnail">update entry thumbnail</option>
		<option value="updateentriesthumbnails">update entries thumbnails</option>
		<option value="getroughcut">get roughcut</option>
		<option value="deleteentry">delete entry</option>
		<option value="listentries">list entries</option>
		<option value="listmyentries">list MY entries</option>
		<option value="addpartnerentry">add partner entry</option>
		<option value="listpartnerentries">list partner entries</option>		
		<option value="getadmintags">get admin tags</option>
		<option value="addroughcutentry">add roughcut entry</option>
		<option value="cloneroughcut">clone roughcut</option>
		<option value="updateentrymoderation">update entry moderation</option>
		<option value="getentryroughcuts">get entry roughcuts</option>
	</optgroup>
	
	<optgroup label="data entry">
		<option value="adddataentry">add data entry</option>
		<option value="getdataentry">get data entry</option>
		<option value="updatedataentry">update data entry</option>
		<option value="listdataentries">list data entries</option>
		<option value="deletedataentry">delete data entry</option>
	</optgroup>

	<optgroup label="kshow">
		<option value="addkshow">add kshow</option>
		<option value="clonekshow">clone kshow</option>
		<option value="updatekshow">update kshow</option>
		<option value="getkshow">get kshow</option>
		<option value="listkshows">list kshows</option>
		<option value="rankkshow">rank kshow</option>
		<option value="deletekshow">delete kshow</option>
		<option value="listmykshows">list MY kshows</option>
		<option value="updatekshowowner">update kshow owner</option>
		<option value="getlastversionsinfo">get last versions info</option>
	</optgroup>

	<optgroup label="user">
		<option value="adduser">add user</option>
		<option value="getuser">get user</option>
		<option value="updateuser">update user</option>
		<option value="deleteuser">delete user</option>
		<option value="updateuserid">update user id</option>
		<option value="reportuser">report user</option>
		<option value="listusers">list users</option>
	</optgroup>

	<optgroup label="moderation">
		<option value="addmoderation">add moderation</option>
		<option value="reportentry">report entry</option>
<?php // 		<option value="reportkshow">report kshow</option> ?>
		<option value="listmoderations">list moderations</option>
		<option value="handlemoderation">handle moderation</option>
	</optgroup>

	<optgroup label="notification">
		<option value="updatenotification">update notification</option>
		<option value="listnotifications">list notifications</option>
		<option value="checknotifications">check notifications</option>
	</optgroup>

	<optgroup label="widget">
		<option value="viewwidget">view widget</option>
		<option value="addwidget">add widget</option>
		<option value="getwidget">get widget</option>
		<option value="getdefaultwidget">get default widget</option>
	</optgroup>

	<optgroup label="keditor">
		<option value="getallentries">get all entries</option>
		<option value="getmetadata">get metadata</option>
		<option value="setmetadata">set metadata</option>
		<option value="appendentrytoroughcut">append entry to roughcut</option>
	</optgroup>

	<optgroup label="dvdProject">
		<option value="adddvdentry">add dvdEntry</option>
		<option value="getdvdentry">get dvdEntry</option>
		<option value="updatedvdentry">update dvdEntry</option>
		<option value="listdvdentries">list dvdEntries</option>
		<option value="listmydvdentries">list my dvdEntries</option>
		<option value="adddvdjob">add a dvd job</option>
	</optgroup>

	<optgroup label="playlist">
		<option value="addplaylist">add playlist</option>
		<option value="getplaylist">get playlist</option>
		<option value="updateplaylist">update playlist</option>
		<option value="listplaylists">list playlists</option>
		<option value="deleteplaylist">delete playlists</option>		
		<option value="executeplaylist">execute playlist</option>
		<option value="executeplaylistfromcontent">execute playlist from content</option>
		<option value="getplayliststatsfromcontent">get playlist stats from content</option>
				
	</optgroup>

	<optgroup label="conversion profiles">
		<option value="addconversionprofile">add conversion profile</option>
		<option value="listconversionprofiles">list conversion profiles</option>
	</optgroup>
	
	<optgroup label="bulkupload">
		<option value="addbulkupload">add bulk upload</option>
		<option value="listbulkuploads">list bulk uploads</option>
	</optgroup>

	<optgroup label="uiconf">
		<option value="getuiconf">get uiConf</option>
		<option value="adduiconf">add uiconf</option>
		<option value="updateuiconf">update uiconf</option>
		<option value="cloneuiconf">clone uiconf</option>
		<option value="listuiconfs">list uiconfs</option>
		<option value="deleteuiconf">delete uiconf</option>
	</optgroup>

	<optgroup label="download">
		<option value="adddownload">add download</option>
		<option value="listdownloads">list downloads</option>
	</optgroup>
	
	<optgroup label="misc">
		<option value="mrss">mRss</option>
		<option value="objdetails">obj details</option>
		<option value="collectstats">collect stats</option>
		<option value="contactsalesforce">contact salesforce</option>
		<option value="transcode">transcode</option>		
	</optgroup>


<?php 
$multi_request_2 = null;
if ( !@$limited ) { 
require_once ( "testme_multirequest.php" );
?>
	<optgroup label="multi">
		<option value="multirequest">multi request</option>
	</optgroup>
<?php } 
require_once ( "testme_dvdentries.php" );
require_once ( "testme_playlists.php" );
require_once ( "testme_conversionprofiles.php" );
require_once ( "testme_uiconf.php" );
require_once ( "testme_dataentries.php" );
?>


</select>&nbsp<span id="service_name"></span>
</td></tr>
</table>
</div>
<div style="font-size:11px;" id="extra_fields" name="extra_fields">

</div>


</span>
<div id="hidden_divs">
<?php
	$divs = array (
		"purchasepackage|2" => array (
			array ( "package_id" , "" , "5" ),
			array ( "customer_first_name" , "" , "30" ),
			array ( "customer_last_name" , "" , "30" ),
			array ( "customer_cc_type" , "" , "20" ),
			array ( "customer_cc_number" , "" , "30" ),
			array ( "cc_expiration_month" , "" , "5" ),
			array ( "cc_expiration_year" , "" , "5" ),
			array ( "cc_cvv2_number" , "" , "5" ),
			array ( "customer_address1" , "" , "30" ),
			array ( "customer_address2" , "" , "30" ),
			array ( "customer_city" , "" , "30" ),
			array ( "customer_state" , "" , "30" ),
			array ( "customer_zip" , "" , "10" ),
			array ( "customer_country" , "" , "5" ),
		),
		"registerpartner" => array (
			array ( "partner_name" , "" , "30" ) ,
			array ( "partner_url1" , "" , "30" ) ,
			array ( "partner_url2" , "" , "30" ) ,
			array ( "partner_appearInSearch" , "" , "1" , "2"),
			array ( "partner_adminName" , "" , "30" ) ,
			array ( "partner_adminEmail" , "" , "30" ) ,
			array ( "partner_description" , "textarea"  , "3,23" ),
			array ( "partner_contentCategories" , "" , "30" ) ,
			array ( "partner_type" , "" , "3" ) ,
			array ( "partner_phone" , "" , "15" ) ,
			array ( "partner_describeYourself" , "" , "10" ) ,
			array ( "partner_adultContent" , "select" , "" , "0" , "boolean_int_type" ) ,
			array ( "cms_password" , "" , "30" ) ,
			array ( "partner_defConversionProfileType" , "" , "15" ) ,
		) ,

		"getpartner" => array (
			array ( "partner_adminEmail" , "" , "30" ) ,
			array ( "cms_password" , "" , "30" ) ,
			array ( "detailed" , "" , "1" ),
		),

		"getpartnerinfo|2" => array (
		),

		"getpartnerusage|2" => array (
			array ( "year", "", "15" ) ,
			array ( "month", "select", "", "", "months_list" ) ,  
			array ( "resolution", "select", "", "", "usage_graph_resolutions" ) ,  
		),
				
		"updatepartner|2" => array (
			array ( "partner_url2" , "" , "30" ) ,
			array ( "partner_url1" , "" , "30" ) ,			
			array ( "partner_notificationsConfig" , "" , "5" ) ,
			array ( "partner_allowMultiNotification" , "" , "5" ) ,
			array ( "partner_notify" , "" , "5" ) ,
			array ( "partner_appearInSearch" , "" , "1" , "2"),
			array ( "partner_adminName" , "" , "30" ) ,
			array ( "partner_adminEmail" , "" , "30" ) ,
			array ( "partner_description" , "textarea"  , "3,23" ),
			array ( "partner_contentCategories" , "" , "30" ) ,
			array ( "partner_type" , "" , "3" ) ,
			array ( "partner_phone" , "" , "15" ) ,
			array ( "partner_describeYourself" , "" , "10" ) ,
			array ( "partner_adultContent" , "select" , "" , "0" , "boolean_int_type" ) ,
			array ( "partner_defConversionProfileType" , "" , "15" ) ,
			array ( "partner_allowQuickEdit" , "select" , "" , "" , "boolean_int_type") ,
			array ( "partner_mergeEntryLists" , "select" , "" , "" , "boolean_int_type") ,
			array ( "partner_userLandingPage" , "" , "30" ) ,
			array ( "partner_landingPage" , "" , "30" ) ,
			array ( "partner_maxUploadSize" , "" , "30" ) ,
			array ( "allow_empty_field" , "select" , "" , "false" , "boolean_type" ) ,
		),

		"listpartnerpackages|2" => array (
		),		

		"adminlogin" => array (
			array ( "email" , "" , "30" ) ,
			array ( "password" , "" , "30" ) ,
		),

		"resetadminpassword" => array (
			array ( "email" , "" , "30" ) ,
		),

		"updateadminpassword|2" => array (
			array ( "adminKuser_email" , "" , "30" ) ,
			array ( "new_email" , "" , "30", null, null, "optional" ) ,
			array ( "adminKuser_password" , "" , "30" ) ,
			array ( "new_password" , "" , "30" ) ,
		),
		
		"searchmediaproviders" => array (
		),

		"searchfromurl" => array (
			array ( "media_type" , "select" , "5" , "" , "media_type" ) ,
			array ( "url" , "" , "50" ) ,
		) ,

		"search" => array (
			array ( "search" , "" , "15" , "dogs" ),
			array ( "media_source" , "select" , "2" , "20" , "media_source" ) ,
			array ( "media_type" , "select" , "2" , "" , "media_type" ) ,
			array ( "auth_data" , "" , "20" ),
			array ( "extra_data" , "" , "20" ),
			array ( "page" , "" , "2" , "1"),
			array ( "page_size" , "" , "2" , "10" ),
		) ,

		"searchmediainfo" => array (
			array ( "media_source" , "select" , "2" , "20" , "media_source" ) ,
			array ( "media_type" , "select" , "2" , "" , "media_type" ) ,
			array ( "media_id" , "" , "20" ),
		) ,

		"addsearchresult" => array (
			array ( "searchResult_keywords" , "" , "15" , "dogs" ),
			array ( "searchResult_source" , "select" , "2" , "20" , "media_source" ) ,
			array ( "searchResult_mediaType" , "select" , "2" , "" , "media_type" ) ,
			array ( "searchResult_title" , "" , "20" ),
			array ( "searchResult_tags" , "" , "20" ),
			array ( "searchResult_description" , "" , "20" ),
			array ( "searchResult_url" , "" , "20" ),
			array ( "searchResult_thumbUrl" , "" , "20" ),
			array ( "searchResult_sourceLink" , "" , "20" ),
			array ( "searchResult_credit" , "" , "20" ),
			array ( "searchResult_embedCode" , "" , "20" ),
			array ( "searchResult_licenseType" , "" , "" , null , null  ) ,
			array ( "searchResult_thumbUrl" , "" , "20" ),
			array ( "searchResult_sourceLink" , "" , "20" ),
			array ( "searchResult_credit" , "" , "20" ),
			
		) ,
		
		"searchauthdata|1" => array (
			array ( "media_source" , "select" , "2" , "20" , "media_source" ) ,
			array ( "username" , "" , "10" ) ,
			array ( "password" , "" , "10" ),
		) ,

		"getentry|1" => array (
			array ( "entry_id" ),
			array ( "detailed" , "" , "1" ),
			array ( "version" ),
		),
		
		

		"getentries|1" => array (
			array ( "entry_ids" ),
			array ( "detailed" , "" , "1" ),
			array ( "seaparator" , "" , "," ),
		),


		"getroughcut|1" => array (
			array ( "entry_id" ),
			array ( "detailed" , "" , "1" ),
		),

		"getentryroughcuts|1" => array (
			array ( "entry_id" ),
//			array ( "detailed" , "" , "1" ),
		),
		
		"deleteentry|2" => array (
			array ( "entry_id" ),
		),

		"addentry|1" => array (
			array ( "kshow_id" ),
			array ( "entry1_name" ),
			array ( "entry1_type" , "select" , "1" , "1" , "entry_type" ) ,
			array ( "entry1_source" , "select" , "2" , "20" , "media_source" ) ,
			array ( "entry1_mediaType" , "select" , "2" , "" , "media_type" ) ,
			array ( "entry1_tags" , "" , "20" ),
			array ( "entry1_description" , "" , "20" ),
			array ( "entry1_filename" , "" , "" , "data" , null , "File upload / webcam") ,
			array ( "entry1_realFilename" , "" , "20" , "a.jpg" , null , "File upload"),
			array ( "entry1_url" , "" , "" , null , null , "URL grab") ,
			array ( "entry1_thumbUrl" , "" , "" , null , null , "URL grab" ) ,
			array ( "media1_id" , "" , "" , null , null , "The media origianl id" ) ,
			array ( "entry1_sourceLink" , "" , "" , null , null  ) ,
			array ( "entry1_licenseType" , "" , "" , null , null  ) ,
			array ( "entry1_credit" , "" , "" , null , null  ) ,
			array ( "entry1_groupId" , null , 20 ) ,
			array ( "entry1_partnerData" , null , 20 ) ,
			array ( "entry1_indexedCustomData1" , null , 20 ) ,			
			array ( "entry1_thumbOffset" , null , 5 , "3" , "" , "In seconds" ) ,
			array ( "entry1_fromTime" , null , 7 , "" , "" , "In milliseconds" ) ,		
			array ( "entry1_toTime" , null , 7 , "" , "" , "In milliseconds" ) ,
			array ( "entry1_adminTags" , null , 20 , "" , "" , "Can be set only by admins" ) ,
			array ( "entry1_conversionQuality" , "select" , "2" , "" , "conversion_quality" ) ,
			array ( "quick_edit" , null , 1  ) ,
		) ,
		
		"addroughcutentry|1" => array (
			array ( "kshow_id" ),
			array ( "entry_name" ),
			array ( "entry_tags" , "" , "20" ),
			array ( "entry_description" , "" , "20" ),
			array ( "entry_groupId" , null , 20 ) ,
			array ( "entry_partnerData" , null , 20 ) ,
			array ( "entry_indexedCustomData1" , null , 20 ) ,			
			array ( "entry_adminTags" , null , 20 , "" , "" , "Can be set only by admins" ) ,
		) ,
		
		"adddownload|1" => array (
			array ( "entry_id" ),
			array ( "file_format", "select" , "" , "" , "download_file_formats" ),
			array ( "version" ),
			array ( "conversion_quality", null , 20 ),			
			array ( "force_download", "select" , "" , "" , "boolean_int_type" ),
		),

		"getadmintags|2" => array (
		),
		
		"getkshow|1" => array (
			array ( "kshow_id" ),
			array ( "detailed" , "" , "1" ),
		),

		"updateentry|1" => array (
			array ( "entry_id" ),
			array ( "entry_name" ),
			array ( "entry_tags" , "" , "20" ),
			array ( "entry_description" , "" , "20" ),
			array ( "entry_partnerData" , null , 20 ) ,
			array ( "entry_indexedCustomData1" , null , 20 ) ,	
			array ( "entry_groupId" , null , 20 ) ,
			array ( "entry_mediaDate" , null , 20 ) ,
			array ( "entry_adminTags" , null , 20 , "" , "" , "Can be set only by admins" ) ,	
			array ( "entry_securityPolicy" ),
			array ( "allow_empty_field" , "select" , "" , "false" , "boolean_type" ) ,
		) ,
					
		"cloneroughcut|2" => array (
			array ( "entry_id" ),
			array ( "detailed" , "" , "1" ),
		),

		"updateentrymoderation|2" => array (
			array ( "entry_id" ),
			array ( "moderation_status" , "select" , "" , "2" , "entry_moderation_status" ) ,
		),
		
		
		"clonekshow|2" => array (
			array ( "kshow_id" ),
			array ( "detailed" , "" , "1" ),
		),

		"addkshow|1" => array (
			array ( "kshow_name" ),
			array ( "kshow_description" , null , "20"  ) ,
			array ( "kshow_tags" ),
//			array ( "kshow_mediaType" , "select" , "2" , "" , "media_type" ) ,
			array ( "kshow_indexedCustomData3" , null , 20 ) ,
			array ( "kshow_customData" , null , 20 ) ,
			array ( "kshow_groupId" , null , 20 ) ,
			array ( "kshow_permissions" , "" , 20  ) ,
			array ( "kshow_partnerData" , "" , 20  ) ,
			array ( "kshow_allowQuickEdit" , "select" , "" , "1" , "boolean_int_type" ) ,
			array ( "allow_duplicate_names" , null , 1  ) ,
//			array ( "metadata" , null , 1  ) ,
			array ( "detailed" , null , 1  ) ,
		) ,


		"updatekshow|1" => array (
			array ( "kshow_id" ),
			array ( "kshow_name" ),
			array ( "kshow_description" , null , "20"  ) ,
			array ( "kshow_tags" ),
			array ( "kshow_mediaType" , "select" , "2" , "" , "media_type" ) ,
//			array ( "kshow_indexedCustomData3" , null , 20 ) ,
			array ( "kshow_customData" , null , 20 ) ,
			array ( "kshow_groupId" , null , 20 ) ,
			array ( "kshow_permissions" , "" , 20  ) ,
			array ( "kshow_partnerData" , "" , 20  ) ,
			array ( "allow_duplicate_names" , null , 1  ) ,
			array ( "detailed" , null , 1  ) ,
		) ,

		"updatekshowowner|2" => array (
			array ( "kshow_id" ),
			array ( "user_id" ),
			array ( "detailed" , null , 1  ) ,
		) , 
				
		"deletekshow|2" => array (
			array ( "kshow_id" ),
		),

		"rankkshow|1" => array (
			array ( "kshow_id" ),
			array ( "rank" , null , 1),
		),

		"getlastversionsinfo|1" => array (
			array ( "kshow_id" ),
			array ( "number_of_versions" , null , 1),
		),
		
/*
		"generatewidget|2" => array (
			array ( "kshow_id" ),
			array ( "kshow_name" ),
			array ( "kshow_description" , null , "20"  ) ,
		),
*/
		"viewwidget|2" => array (
			array ( "kshow_id" ),
			array ( "entry_id" ),
			array ( "widget_id" , null , "20"  ) ,
			array ( "host" , "select" , "" , "" , "service_urls1" ),
		),



		"startsession|9" => array (
			array ( "secret" ,null , 34),
			array ( "admin" ,null , 1),
			array ( "expiry" ,null , 6 , "86400" , "Expiry in seconds"),
			array ( "privileges" ,null , 34 , "edit:*" , null , " '*' = Will have edit privileges for all kshows"),
		),

		"startwidgetsession|9" => array (
			array ( "widget_id" ,null , 34),
			array ( "expiry" ,null , 6 , "86400" , "Expiry in seconds"),
//			array ( "privileges" ,null , 34 , "edit:*" , null , " '*' = Will have edit privileges for all kshows"),
		),


		"adduser|2" => array (
			array ( "user_id" ),
			array ( "user_screenName" ),
			array ( "user_fullName" , null , "20"  ) ,
			array ( "user_email" ),
			array ( "user_aboutMe" ) ,
			array ( "user_tags" , null , 20 ) ,
			array ( "user_gender" , null , 1 ,null , "1-Male , 2-Female") ,
			array ( "user_partnerData" , "" , 20  ) ,
		) ,

		"getuser|2" => array (
			array ( "user_id" ),
			array ( "detailed" , "" , "1" ),
		),

		"updateuser|2" => array (
			array ( "user_id" ),
			array ( "user_screenName" ),
			array ( "user_fullName" , null , "20"  ) ,
			array ( "user_email" ),
			array ( "user_aboutMe" ) ,
			array ( "user_tags" , null , 20 ) ,
			array ( "user_gender" , null , 1 ,null , "1-Male , 2-Female") ,
			array ( "user_partnerData" , "" , 20  ) ,
		) ,

		"deleteuser|2" => array (
			array ( "user_id" ),
		),

		"updateuserid|2" => array (
			array ( "user_id" ),
			array ( "new_user_id" ),
			),

		"reportuser|1" => array (
			array ( "moderation_objectId" , "" , "7" , "" , null , "puser_id NOT kuser_id"),
//			array ( "moderation_objectType" , "select" , "2" , "2" , "moderation_object_type" ) ,
			array ( "moderation_comments" , "" , "20" ),
			array ( "moderation_reportCode" , "" , "2" ),
		),
		
		"listusers|2" => array (
			array ( "detailed" , "" , "1" ),
			array ( "page" , "" , "2" , "1"),
			array ( "page_size" , "" , "2" , "10" ),
		),
			
		
		"upload|1" => array (
			array ( "Filedata", "file" ),
			array ( "filename" , "" , null , "data" , null , "Same as addentry:filename1" ),
		),

		"uploadjpeg|1" => array (
			array ( "data" , "" , null , "" , null , "This should contain the binary data" ),
			array ( "filename" , "" , null , "data" , null , "Same as addentry:filename1" ),
		),

		"webcamdummy" => array (
			array ( "Filedata", "file" ),
			array ( "filename" , "" , null , "data" , null , "Same as addentry:filename1" ),
		) ,

		"listkshows|2" => array (
			array ( "detailed" , "" , "1" ),
			array ( "page" , "" , "2" , "1"),
			array ( "page_size" , "" , "2" , "10" ),
//			array ( "filter__eq_type", "select" , "2" , "1" , "kshow_type" ) ,
			array ( "filter__eq_id", null , "6" ),
			array ( "filter__eq_producer_id", null , "6" ),
			array ( "filter__like_tags", null , "20" ),
			array ( "filter__mlikeor_tags", null , "20" ),			
			array ( "filter__mlikeand_tags", null , "20" ),
//			array ( "filter__like_name", null , "20" ),
			array ( "filter__gte_created_at", null , "20" ),
			array ( "filter__lte_created_at", null , "20" ),
			array ( "filter__bitand_status", null , "2" ),
			array ( "use_filter_puser_id" , "select" , "true" , "" , "boolean_type" ) ,
		) ,

		"listmykshows|1" => array (
			array ( "detailed" , "" , "1" ),
			array ( "page" , "" , "2" , "1"),
			array ( "page_size" , "" , "2" , "10" ),
			array ( "filter__eq_id", null , "6" ),
			array ( "filter__like_tags", null , "20" ),
			array ( "filter__like_name", null , "20" ),
			array ( "use_filter_puser_id" , "select" , "true" , "" , "boolean_type" ) ,
		) ,


		"listentries|2" => array (
			array ( "detailed" , "" , "1" ),
			array ( "detailed_fields" , null , "20" ),
			array ( "page" , "" , "2" , "1"),
			array ( "page_size" , "" , "2" , "10" ),
			array ( "filter__eq_id", null , "10" ),
			array ( "filter__eq_status", "select" , "2" , "2" , "entry_status" ) ,
			array ( "filter__in_status", "" , 10 ) ,
			array ( "filter__eq_media_type", "select" , "2" , "1" , "entry_media_type_filter" ) ,
			array ( "filter__in_media_type", "" , 10 ) ,			
			array ( "filter__eq_user_id", null , "6" ),
			array ( "filter__eq_kshow_id", null , "6" ),
			array ( "filter__like_tags", null , "20" ),
			array ( "filter__mlikeor_tags", null , "20" ),			
			array ( "filter__mlikeand_tags", null , "20" ),
			array ( "filter__eq_name", null , "20" ),
			array ( "filter__like_name", null , "20" ),
			array ( "filter__mlikeor_name", null , "20" ),			
			array ( "filter__mlikeand_name", null , "20" ),
			array ( "filter__mlikeor_admin_tags", null , "20" ),			
			array ( "filter__mlikeand_admin_tags", null , "20" ),
			array ( "filter__mlikeor_search_text", null , "20" ),			
			array ( "filter__mlikeand_search_text", null , "20" ),
			array ( "filter__eq_indexed_custom_data_1", null , "20" ),
			array ( "filter__gte_created_at", null , "20" ),
			array ( "filter__lte_created_at", null , "20" ),
			array ( "filter__gte_media_date", null , "20" ),
			array ( "filter__lte_media_date", null , "20" ),
			array ( "filter__eq_moderation_status", "select" , "" , "" , "entry_moderation_status_filter" ) ,
			array ( "filter__in_moderation_status", null , "20" ),
			array ( "filter__in_display_in_search" , "select" , "" , "" , "display_in_search_filter" ) ,
			array ( "filter__mlikeor_tags-admin_tags", null , "20" ),
			array ( "filter__mlikeor_tags-admin_tags-name", null , "20" ),
			array ( "filter__matchand_search_text", null , "20" ),
			array ( "filter__matchor_search_text", null , "20" ),
			array ( "filter__order_by" , "select" , "2" , "1" , "entries_filter_order_by"  ),
			array ( "use_filter_puser_id" , "select" , "true" , "" , "boolean_type" ) ,
			array ( "display_deleted" , "select" , "true" , "" , "boolean_type" ) ,
		) ,

		"listmyentries|1" => array (
			array ( "detailed" , "" , "1" ),
			array ( "page" , "" , "2" , "1"),
			array ( "page_size" , "" , "2" , "10" ),
			array ( "filter__eq_status", "select" , "2" , "2" , "entry_status" ) ,
			array ( "filter__eq_type", "select" , "2" , "1" , "entry_type" ) ,
			array ( "filter__eq_id", null , "6" ),
			array ( "filter__eq_media_type", "select" , "" , "" , "entry_media_type_filter" ),
			array ( "filter__in_media_type", null , "" ),
			array ( "filter__in_indexed_custom_data_1", null , "" ),
			array ( "filter__like_tags", null , "20" ),
			array ( "filter__mlikeor_tags", null , "20" ),			
			array ( "filter__mlikeand_tags", null , "20" ),
			array ( "filter__eq_name", null , "20" ),
			array ( "filter__like_name", null , "20" ),
			array ( "filter__eq_indexed_custom_data_1", null , "20" ),
			array ( "filter__order_by" , "select" , "2" , "1" , "entries_filter_order_by"  ),
			array ( "use_filter_puser_id" , "select" , "true" , "" , "boolean_type" ) ,
		) ,


		"listpartnerentries|1" => array (
			array ( "detailed" , "" , "1" ),
			array ( "page" , "" , "2" , "1"),
			array ( "page_size" , "" , "2" , "10" ),
			array ( "filter__eq_type", "select" , "2" , "1" , "entry_type" ) ,
			array ( "filter__eq_id", null , "6" ),
			array ( "filter__eq_media_type", "select" , "" , "" , "entry_media_type_filter" ),
			array ( "filter__in_media_type", null , "" ),
			array ( "filter__in_indexed_custom_data_1", null , "" ),
			array ( "filter__like_name", null , "20" ),
			array ( "filter__eq_indexed_custom_data_1", null , "20" ),
			array ( "filter__order_by" , "select" , "2" , "1" , "entries_filter_order_by"  ),
			array ( "use_filter_puser_id" , "select" , "true" , "" , "boolean_type" ) ,
		) ,
		
		"addpartnerentry|2" => array (
			array ( "kshow_id" ),
			array ( "entry1_name" ),
			array ( "entry1_source" , "select" , "2" , "20" , "media_source" ) ,
			array ( "entry1_mediaType" , "select" , "2" , "" , "media_type" ) ,
			array ( "entry1_tags" , "" , "20" ),
			array ( "entry1_filename" , "" , "" , "data" , null , "File upload / webcam") ,
			array ( "entry1_realFilename" , "" , "20" , "a.jpg" , null , "File upload"),
			array ( "entry1_url" , "" , "" , null , null , "URL grab") ,
			array ( "entry1_thumbUrl" , "" , "" , null , null , "URL grab" ) ,
			array ( "media1_id" , "" , "" , null , null , "The media origianl id" ) ,
			array ( "entry1_sourceLink" , "" , "" , null , null  ) ,
			array ( "entry1_licenseType" , "" , "" , null , null  ) ,
			array ( "entry1_credit" , "" , "" , null , null  ) ,
			array ( "entry1_partnerData" , null , 20 ) ,
			array ( "entry1_indexedCustomData1" , null , 20 ) ,			
			array ( "entry1_thumbOffset" , null , 5 , "3" , "" , "In seconds" ) ,
			array ( "quick_edit" , null , 1  ) ,
		) ,

		
		"objdetails" => array (
			array ( "clazz" , "select" , "2" , "" , "clazz_list" ) ,
		) ,

		"collectstats" => array (
			array ( "obj_type" , "select" , "2" , "" , "obj_type_list" ) ,
			array ( "obj_id" , null , 20 ) ,
			array ( "command" , "select" , "2" , "" , "command_list" ) ,
			array ( "value" , null , 20 ) ,
			array ( "extra_info" , null , 20 ) ,
		) ,

		"multirequest|0" => $multi_request_2 ,
		
		"addmoderation|2" => array (
			array ( "moderation_objectId" , "" , "7" ),
			array ( "moderation_objectType" , "select" , "2" , "2" , "moderation_object_type" ) ,
			array ( "moderation_status" , "select" , "2" , "2" , "moderation_status" ) ,
			array ( "moderation_comments" , "" , "20" ),
		),

		"reportentry|1" => array (
			array ( "moderation_objectId" , "" , "7" ),
			array ( "moderation_objectType" , "select" , "2" , "2" , "moderation_object_type" ) ,
			array ( "moderation_comments" , "" , "20" ),
			array ( "moderation_reportCode" , "" , "2" ),
		),

		"handlemoderation|2" => array (
			array ( "moderation_id" , "" , "7" ),
			array ( "moderation_status" , "select" , "2" , "2" , "moderation_status" ) ,
		),

/*
		"reportkshow|1" => array (
			array ( "moderation_objectId" , "" , "7" ),
			array ( "moderation_comments" , "" , "20" ),
		),
*/

		"listmoderations|2" => array (
			array ( "detailed" , "" , "1" ),
			array ( "page" , "" , "2" , "1"),
			array ( "page_size" , "" , "2" , "10" ),
			array ( "filter__eq_id", null , "6" ),
			array ( "filter__eq_object_id",  ) ,
			array ( "filter__eq_object_type", "select" , "2" , "2" , "moderation_object_type" ) ,
//			array ( "filter__eq_puser_id", null , "6" ),
			array ( "filter__eq_group_id", null , "15" ),
			array ( "filter__eq_status", null , "1" ),
			array ( "filter__in_status", null , "10" ),
			array ( "filter__like_comments", null , "20" ),
		) ,


		"updatenotification|2" => array (
			array ( "notification_id" , "" , "7" ),
			array ( "notification_status", "select" , "2" , "2" , "notification_status" ) ,
			array ( "notification_notificationResult" , "" , "30" ),
			array ( "notification1_id" , "" , "7" ),
			array ( "notification1_status", "select" , "2" , "2" , "notification_status" ) ,
			array ( "notification1_notificationResult" , "" , "30" ),
			array ( "notification2_id" , "" , "7" ),
			array ( "notification2_status", "select" , "2" , "2" , "notification_status" ) ,
			array ( "notification2_notificationResult" , "" , "30" ),

		),

		"listnotifications|2" => array (
//			array ( "detailed" , "" , "1" ), // no detailed for notification
			array ( "page" , "" , "2" , "1"),
			array ( "page_size" , "" , "2" , "10" ),
			array ( "filter__eq_id", null , "6" , null , null , "Match this id exactly"),
			array ( "filter__gte_id", null , "6" , null , null , "Start from this id"),
			array ( "filter__eq_status", "select" , "2" , "1" , "notification_status" ) ,
			array ( "filter__eq_type", "select" , "2" , " " , "notification_type" ) ,
		) ,

		"checknotifications|1" => array (
			array ( "notification_ids" ),
			array ( "detailed" , "" , "1" ),
			array ( "seaparator" , "" , "," ),
		),
		
		"addwidget|1" => array (
			array ( "widget_sourceWidgetId" ),
			array ( "widget_kshowId" ),
			array ( "widget_entryId" ),
			array ( "widget_uiConfId" ),
//			array ( "widget_customData" ),
			array ( "widget_securityType", "select" , "1" , " " , "widget_security_type" ) ,
			array ( "widget_partnerData" , "textarea"  , "4,30" ),
			array ( "detailed" , "" , "1" ),
		),

		"getwidget|" => array (
			array ( "widget_id" ),
			array ( "detailed" , "" , "1" ),
			array ( "uiconf_id" ) ,
		),
		
		"getdefaultwidget|" => array (
			array ( "detailed" , "" , "1" ),
		),
		
		"getuiconf|1" => $getuiconf,

		"getallentries|1" => array (
			array ( "kshow_id" ),
			array ( "entry_id" ),
			array ( "version" ),
			array ( "list_type", "select" , "1" , " " , "entries_list_type" ) ,
			array ( "disable_roughcut_entry_data", "select" , "1" , " " , "boolean_int_type" ) ,
		),

		"getmetadata|1" => array (
			array ( "kshow_id" ),
			array ( "entry_id" ),
			array ( "version" ),
		),

		"setmetadata|1" => array (
			array ( "kshow_id" ),
			array ( "entry_id" ),
			array ( "xml" , "textarea"  , "4,30" ),
			array ( "HasRoughCut" , "" , "1" ),
		),
		
		"appendentrytoroughcut|1" => array (
			array ( "entry_id" ),
			array ( "kshow_id" ),
			array ( "show_entry_id" ),
		),

		"updateentrythumbnail|1" => array (
			array ( "entry_id" , "" , "" ),
			array ( "source_entry_id" , "" , "" ),
			array ( "time_offset" , "" , "-1" ),
		),

		"updateentriesthumbnails|2" => array (
			array ( "entry_ids" , "textarea"  , "3,20"),
			array ( "time_offset" , "" , "3" ),
			array ( "detailed" , "" , "1" ),
			array ( "seaparator" , "" , "," ),
		),
		
/* -------------- dvdentry ----------- */		
		"adddvdentry|1" => $adddvdentry,
		"getdvdentry|1" => $getdvdentry,
		"updatedvdentry|1" => $updatedvdentry,		
		"listdvdentries|2" => $listdvdentries ,
		"listmydvdentries|1" => $listmydvdentries ,
		"adddvdjob|1" => $adddvdjob ,

/* -------------- dataentry ----------- */		
		"adddataentry|1" => $adddataentry,
		"getdataentry|1" => $getdataentry,
		"updatedataentry|1" => $updatedataentry,		
		"listdataentries|2" => $listdataentries ,
		"deletedataentry|2" => $deletedataentry,
		
/* -------------- playlists ----------- */
		"executeplaylist|1" => $executeplaylist,
		"executeplaylistfromcontent|2" => $executeplaylistfromcontent,
		"addplaylist|2" => $addplaylist,
		"getplaylist|2" => $getplaylist,
		"getplayliststatsfromcontent|2" => $getplayliststatsfromcontent,
		"updateplaylist|2" => $updateplaylist,		
		"listplaylists|2" => $listplaylists ,
		"deleteplaylist|2" => $deleteplaylist ,
		
/* -------------- conversion profiles ----------- */
		"addconversionprofile|2" => $addconversionprofile,
		"listconversionprofiles|2" => $listconversionprofiles,		
		
		"addbulkupload|2" => array (
			array ( "csv_file", "file" ),
			array ( "profile_id" ),
		),
		
		"listbulkuploads|2" => array (
			array ( "page" , "" , "2" , "1"),
			array ( "page_size" , "" , "2" , "20" ),
		),


		"listdownloads|2" => array (
			array ( "page" , "" , "2" , "1"),
			array ( "page_size" , "" , "2" , "20" ),
			array ( "filter__eq_id", null , "6" ),
			array ( "filter__gte_id", null , "6" ),
			array ( "filter__status", null , "6" ),
			array ( "filter__eq_job_type", "select" , "" , "" , "download_job_type_filter" ),			
			array ( "filter__gte_created_at", null , "20" ),
			array ( "filter__lte_created_at", null , "20" ),
			array ( "filter__order_by" , "select" , "2" , "1" , "download_filter_order_by"  ),
		),

		
		"adduiconf|2" => $adduiconf,
		"updateuiconf|2" => $updateuiconf ,
		"cloneuiconf|2" => array (
			array ( "uiconf_id", "", "20" ),
			array ( "new_name", "", "20" ),
			array ( "detailed", "", "5" ),
		),

		"listuiconfs|2" => $listuiconfs,		
		"deleteuiconf|2" => $deleteuiconf,
		
		"contactsalesforce|2" => array(
			array ( "name" , "" , "20"),
			array ( "phone" , "" , "20"),
			array ( "comments" , "textarea"  , "3,23" ),
			array ( "services" , ""  , "30", "", "", "comma-separated values" ),
			
		),
		
		"transcode|2" => array(
			array ( "entry_id" , "" , "20"),
			array ( "data1" , "" , "20"),
			array ( "type" , "" , "1"),
		),
		
	) ;

	if ( ! $limited )
	{
		$divs["mrss|3"] =
		 array (
		 	array ( "code" , "" , "20" ),
			array ( "page" , "" , "2" , "1"),
			array ( "page_size" , "" , "2" , "10" ),
			array ( "filter__eq_media_type", "select" , "2" , "1" , "entry_media_type_filter" ) ,
			array ( "filter__eq_id", null , "6" ),
			array ( "filter__eq_user_id", null , "6" ),
			array ( "filter__eq_kshow_id", null , "6" ),
//			array ( "filter__like_name", null , "20" ),
			array ( "filter__eq_indexed_custom_data_1", null , "20" ),
			array ( "filter__gte_created_at", null , "20" ),
			array ( "filter__lte_created_at", null , "20" ),
			array ( "filter__order_by" , "select" , "2" , "1" , "entries_filter_order_by"  ),
		) ;
	}
	
	
	
	$div_colors = array ( "0" => "white" , "1" => "lime" , "2" => "orange" , "9" => "lightblue" );

	foreach ( $divs as $div => $attrs )
	{
		$div_params = explode ( "|" , $div );
		$div_id = $div_params[0];
		$div_ticket_type = @$div_params[1];
		if ( empty ( $div_ticket_type ) ) $div_ticket_type=0;
		$div_color = @$div_colors[$div_ticket_type];
		echo "<div id='div_" . $div_id . "' style='width:410px;background-color:{$div_color};display:none'><table>\n";
			echo createInputs ( $attrs , $div_id ) ;
		echo "</table></div>";
	}

?>
</div>

</form>

<div style="position:absolute; left:450px; top:10px; width:800px; height:95%; font-family:arial; font-size:12px;">
	Result:<br>
	<iframe id='target_frame' name='target_frame' style="width:800px; height:95%; _height:600px; font-family:arial; font-size:10px;">
</iframe>
</div>

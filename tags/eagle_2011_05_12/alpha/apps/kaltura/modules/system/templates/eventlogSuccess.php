<?php
function getEventType ( $consts_map , $t )
{
	if ( isset ( $consts_map[$t] ) )
		return  $consts_map[$t];
	else
		return $t;
}



require_once ( SF_ROOT_DIR . "/../api_v3/lib/types/KalturaEnum.php");
require_once ( SF_ROOT_DIR . "/../api_v3/lib/types/enums/KalturaStatsEventType.php");
// build the map for the KalturaStatsEventType
$ref = new ReflectionClass('KalturaStatsEventType');

$consts = $ref->getConstants();

$consts_map = array();
foreach ( $consts as $c => $val )
{
	$consts_map[$val] = $c;
}

if ( $type == "kdp" )
{
$header = array ( "client version" , "event id" , "datetime" , "session id" , "partner id" , "entry id" ,
	"uv" , "widget id" , "uiconf id" , "uid" , "current point" , "duration" , "user ip" , "process duration" ,
	"control id" , "seek" , "new point" , "referrer" , "--"
	);
}
elseif ( $type == "batch" )
{
/*
 * 	,batch_client_version	varchar(20)
	,batch_event_type_id	smallint
	,batch_name	varchar(50)
	,batch_event_time	datetime
	,batch_session_id	varchar(50)
	,batch_type smallint
	,host_name	varchar(20)
	,location_id	int
	,section_id	int
	,batch_id	int
	,partner_id	int
	,entry_id varchar(20)
	,bulk_upload_id int
	,batch_parant_id int
	,batch_root_id int
	,batch_status smallint
	,batch_progress int
	,value_1 int
 */
$header = array ( "client version" , "event type id" , "batch name" , "event_time" , "session_id" , "batch type" , "host name" ,
	"location id" , "section id" , "batch id" , "partner id" , "entry id" , "bulk upload id" , "batch parent id" , "batch root id" ,
	"batch status" , "batch process" , "value 1" 
	);	 
}
elseif ( $type == "api" ) 
{
$header = array (
	"api_client_version", 
	"datetime" ,
	"session_id" ,
	"service",
	"action",
	"ps_version",
	"is_multi_request",
	"ks",
	"ks_type",
	"partner_id",
	"uid",
	"entry_id",
	"ui_conf_id",
	"widget_id",
	"flavor_id",
	"invoke_duration",
	"dispatch_duration",
	"serialize_duration",
	"total_duration",
	"result",
	"all_params",
	"exception"
	);	
}
elseif ( $type == "kmc" ) 
{
$header = array (
	"apiClientVersion", 
	"kmcEventType" ,
	"server time" ,
	"kmcEventActionPath",
	"eventTimestamp",
	"partnerId",
	"userId",
	"entryId",
	"widgetId",
	"uiconfId",
	"ks",
	"ip",
	);	
}
?>
<script>
var handle;
function investigate ( entry_id )
{
	handle = window.open("./investigate?entry_id=" + entry_id , "investigate" );
	handle.focus();
}

function partners ( partner_id )
{
	handle = window.open("./partners?partner_id=" + partner_id , "partners" );
	handle.focus();
}
</script>
<table border=1 cellpadding=2 cellspacing=0 style="font-family:serif; font-size:12px">
<tr>
<?php  foreach ( $header as $td ) 
{
	echo "<td>$td</td>";
}
?>
</tr>
<?php
foreach ( $lines as $line )
{
	$i =1;
	echo "<tr>";
	$line_arr = explode ( "," , $line );
	if ( $type == "kdp" )
	{
		foreach ( $line_arr as $td )
		{
			if ( $i == 2 )
				echo "<td title='" . getEventType( $consts_map , $td ) . "'>$td</td>";
			else if ( $i == 6 )
				echo "<td><a href='javascript:investigate(\"$td\")'>$td</a></td>";
			else if ( $i == 5 )
				echo "<td><a href='javascript:partners(\"$td\")'>$td</a></td>";			
			else
				echo "<td>$td</td>";
			$i++;
		}
	}	
	else
	{
		foreach ( $line_arr as $td )
		{		
			echo "<td>$td</td>";
		}
	}
	echo "</tr>";
}
?>
</table>
<br/>
<form method="post" >

<textarea id="lines" name="lines" cols="240" rows="15" style="font-size: 10px; font-family: serif">
<?php echo $lines_str?>
</textarea>
<br/>
Event types: 
KDP: <input type='radio' name='type' value='kdp' <?php if ( $type == "kdp" ) echo "checked='checked'" ?>>
BATCH: <input type='radio' name='type' value='batch' <?php if ( $type == "batch" ) echo "checked='checked'" ?>>
API: <input type='radio' name='type' value='api' <?php if ( $type == "api" ) echo "checked='checked'" ?>>
KMC: <input type='radio' name='type' value='kmc' <?php if ( $type == "kmc" ) echo "checked='checked'" ?>>
<br/>

Line separator: 
<input type="radio" name="line_sep" value="n" <?php echo $line_sep == "n" ? "checked='checked'" : "" ?>>\n

<input type="radio" name="line_sep" value="rn" <?php echo $line_sep != "n" ? "checked='checked'" : "" ?>>\r\n
<button>Submit</button>

</form>
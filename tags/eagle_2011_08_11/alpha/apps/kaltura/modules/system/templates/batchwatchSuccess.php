<?php
 
function formatStatus ( $status )
{
	static $status_dictionary =array ( 0 => "PENDING" , 1 => "QUEUED" , 2 => "PROCESSING" , 3 => "PROCESSED" , 4 => "MOVEFILE" , 5 => "FINISHED" , 6 => "FAILED" ); 
	return "<span title='{" . $status . "}=" . $status_dictionary[$status] . "'>" . $status_dictionary[$status] . "</span>";
}

function formatCount ( $stats_object )
{
	$date_format = "%y-%m-%d %H:%M:%S";
	$count = @$stats_object["count"];
	$str =  $count;
	if ( $count > 0 )
	{
		$str .= " ( " . strftime( $date_format , @$stats_object["oldest"] ). " -> " . strftime( $date_format ,  @$stats_object["newest"] ) . " )";
	}

	return $str;
}

function printStats ( $title , $stats_object , $green_limit , $yellow_limit , $critical )
{
	$count = @$stats_object["count"];
	$color = "#CCFFFF"; // start with blue for count 0  
	if ( $count  > 0 ) $color = "#99FF66";
	if ( $count  > $green_limit ) $color = "#FFFF66";
	if ( $count  > $yellow_limit ) $color = "#FF9966";
	if ( $count  > $critical ) $color = "#CC3366";
	
	$log_color = $color;
	$log_warning = "";
	if ( @$stats_object["log_timestamp"] )
	{
		$delta = time() - @$stats_object["log_timestamp"] ;
		if ( $delta > 600 ) 
		{	
			$log_color = "#CC3366";
			$log_warning = " log too old - (" . $delta . ") seconds from now";
		}
	}
	$date_format = "%y-%m-%d %H:%M:%S";
	
	$restart_div = $stats_object["service_name"]  ? 
		"<div style='position:relative; float:right; '  onClick='restartBatch (\"" . $stats_object["service_name"] . "\")'>&nbsp;</div>" :
		"";
	$str = "<div style='padding:5px;background-color:$color'> " . 
		$restart_div .
		"<b>$title</b> <br>" .
		"path : " .  @$stats_object["path"] . "<br>" . 
	 	"count: " . formatCount ( $stats_object ) . "<br>" ;
	if ( @$stats_object["full_stats"] )
	{
		$str .= "<div style='padding: 0 0 0 10px '>";
		// extra stats per status
		foreach ( $stats_object["full_stats"] as $status => $stats_per_status )
		{
			if ( $status == BatchJob::BATCHJOB_STATUS_FINISHED ) continue; // don't include the BatchJob::BATCHJOB_STATUS_FINISHED in this loop
			$str .= "status " . formatStatus ( $status ) . ": count: " . formatCount ( $stats_per_status ) . "<br>" ;
		}
		$str .= "</div>";
	}
	
	if ( @$stats_object["successful_stats"] )
	{
		$str .= "<div style='background-color:white;'>";
		// extra stats per status
		$stats_per_status = $stats_object["successful_stats"] ;
		$str .= "successful count: " . formatCount ( $stats_per_status ) . "<br>" ;
		$str .= "</div>";
	}
	$str .= "log: " . @$stats_object["log_name"] . "<br> " .
			"<div style='background-color:$log_color'>time: " . @strftime( $date_format , @$stats_object["log_timestamp"] ) . "$log_warning</div>" .  
	 		" size: " . @$stats_object["log_size"] . " b " .
		"</div>";
	return $str;
}
?>
<div style="font-family: calibri; font-size:13px;">
time on server: <?php echo strftime ( "%Y-%m-%d %H:%M:%S")?>, queries on DB [<?php echo $hours_back ?>] hours back<br>
<table>
<tr >
	<td width="450">
<?php echo printStats ( "import" , $import , 10 , 30 , 50 ) ?>	
	</td>
	
	<td width="450">
<?php echo printStats ( "flatten" , $flatten , 10 , 30 , 50 ) ?>	
	</td>

	<td width="450">
<?php echo printStats ( "bulk" , $bulk , 10 , 30 , 50 ) ?>
	</td>
	
</tr>
<?php
if ( $include_old ) {  ?>
<tr><td colspan=3>old convert client</td></tr>
<tr >
	<td >
<?php echo printStats ( "old convert client (in)" , $old_convert_client_in , 10 , 30 , 50 ) ?>	
	</td>
	
	<td >
<?php echo printStats ( "old convert client (out)" , $old_convert_client_out , 10 , 30 , 50 ) ?>	
	</td>

	<td >
<?php echo printStats ( "old convert client errors" , $old_convert_client_errors , 1 , 40 , 60 ) ?>	
	</td>

</tr>

<tr><td colspan=3>old convert server</td></tr>
	 
<tr >
	<td >
<?php echo printStats ( "old convert server" , $old_convert_server, 10 , 30 , 50 ) ?>	
	</td>
	
	<td >
<?php echo printStats ( "old convert server errors" , $old_convert_server_errors, 10 , 30 , 50 ) ?>	
	</td>
	<td>
	</td>
</tr>

<?php } ?>
<tr><td colspan=3>new convert client</td></tr>

<tr >
	<td >
<?php echo printStats ( "new convert client (in)" , $new_convert_client_in , 10 , 30 , 50 ) ?>	
	</td>
	
	<td >
<?php echo printStats ( "new convert client (out)" , $new_convert_client_out , 10 , 30 , 50 ) ?>	
	</td>

	<td>
	</td>
	
</tr>

<tr><td colspan=3>new convert server </td></tr>

<tr >
	<td >
<?php echo printStats ( "new convert server" , $new_convert_server , 10 , 30 , 50 ) ?>	
	</td>
	
	<td >
<?php echo printStats ( "new commercial convert server" , $new_commercial_convert_server , 10 , 30 , 50 ) ?>	
	</td>
	
	<td>
	</td>
	
</tr>

<tr><td colspan=3>download video</td></tr>

<tr >
	<td >
<?php echo printStats ( "download video (in)" , $download_video_in , 10 , 30 , 50 ) ?>	
	</td>
	
	<td >
<?php echo printStats ( "download video (out)" , $download_video_out , 10 , 30 , 50 ) ?>	
	</td>

	<td>
	</td>
	
</tr>
</table>
</div>

<script>
var wrap = document.getElementById("wrap");
wrap.setAttribute( "style" , "" );

function restartBatch ( batch_name )
{
	loc = "" + window.document.location;
	res = confirm ( "would you really like to restart batch [" + batch_name + "]" );

	if ( !res ) return; 
	if ( loc.indexOf (  "?" ) < 0 ) 
		loc += "?";
	else
		loc += "&";

	loc += "mode=restart&batch=" + batch_name;
	img = new Image();
	img.src = loc;
}
</script>

<?php
// will take an apache access log and check how many events of the same type happened in a specific period 
// the default period is 10 minutes
// the format of the log is similar to the one on production
/*
 * 
65.74.174.196 - - [21/Jan/2009:15:20:36 -0500] "POST /index.php/partnerservices2/getkshow HTTP/1.1" 200 5266 "-" "-" "-" 65.74.174.196 
90.210.218.121 - - [21/Jan/2009:15:20:36 -0500] "GET /index.php/partnerservices2/collectstats?kalsig=74b8a6b00aad74f5f20e5b9530cb02c1&value=&command=view&obj%5Fid=3p2dqq3z58&uid=0&extra%5
Finfo=A%253Dt%2526SA%253Dt%2526SV%253Dt%2526EV%253Dt%2526MP3%253Dt%2526AE%253Dt%2526VE%253Dt%2526ACC%253Dt%2526PR%253Dt%2526SP%253Dt%2526SB%253Df%2526DEB%253Df%2526V%253DWIN%2525209%25252
C0%25252C124%25252C0%2526M%253DAdobe%252520Windows%2526R%253D1280x800%2526DP%253D72%2526COL%253Dcolor%2526AR%253D1%2E0%2526OS%253DWindows%252520Vista%2526L%253Den%2526IME%253Dt%2526PT%253
DActiveX%2526AVD%253Df%2526LFD%253Df%2526WD%253Df%2526TLS%253Dt&obj%5Ftype=entry&subp%5Fid=1444200&partner%5Fid=14442&ks=YzQyOGI5MjEzNGQ5ZDZlNDMzOTIwZTQ2MjNlYWFmNDY0OWYxNzU3ZXwxNDQ0MjsxND
Q0MjsxMjMyNjU1NjI2OzA7MTIzMjU2OTIyNi43NjMzOzt2aWV3Oio%3D HTTP/1.1" 200 439 "http://cdn.kaltura.com/p/14442/sp/1444200/flash/kdp/v1.0.15/kdp.swf" "Mozilla/4.0 (compatible; MSIE 7.0; Window
s NT 6.0; SLCC1; .NET CLR 2.0.50727; Media Center PC 5.0; .NET CLR 3.0.04506)" "uv_4caaa8577612c8cb090003dd3f66d956" 90.210.218.121
87.248.197.33 - - [21/Jan/2009:15:20:36 -0500] "GET /swf/plugins/cvesdk/transitions.xml HTTP/1.1" 304 - "http://cdn.kaltura.com/p/14442/sp/1444200/flash/kdp/v1.0.15/kdp.swf" "Mozilla/4.0
(compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.0.3705; .NET CLR 1.1.4322; Media Center PC 4.0; .NET CLR 2.0.50727)" "-" 87.248.197.33
70.185.210.64 - - [21/Jan/2009:15:20:36 -0500] "POST /index.php/partnerservices2/setmetadata HTTP/1.1" 200 1060 "http://www.kaltura.com/[[IMPORT]]/cdn.kaltura.com/p/0/sp/0/flash/kse/v2.0.
6/simpleeditor.swf" "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.4; en-US; rv:1.9.0.5) Gecko/2008120121 Firefox/3.0.5" "uv_6ca80e643e36c05872523f4cb0f478f7" 70.185.210.64

 */
function print_timeframe ( $day , $timeframe , $timeframe_stats )
{
	
	if ( ! $timeframe_stats ) 
	{
		if ( ! $timeframe ) return;	
		print "$day $timeframe\t-\t-\n";
		return; 
	}
	
	ksort ( $timeframe_stats );
	$total = 0;
	$display_timeframe = @$timeframe_stats["display"];
	foreach ( $timeframe_stats as $category_stats => $value  )
	{
		if ( $category_stats == "display" ) continue;
		print "$day $display_timeframe\t$category_stats\t$value\n";	
		$total+= $value;
	}
	print "$day $display_timeframe\ttotal\t$total\n";
//	print $timeframe . "\n";
//	print_r ( $timeframe_stats );	
}

$file_name = $argv[1];
$limit = @$argv[2];
$start_timeframe = @$argv[3];
$end_timeframe = @$argv[4];
$resolution = @$argv[5];  // can be empty = 10 minutes or 1 - single minute  

$stderr = fopen("php://stderr", "w");
$i = 0;

$should_collect_stats = true;
$found_end_timeframe = false;

if ( $start_timeframe )
{
	fprintf($stderr, "\n\n	waiting for timeframe [$start_timeframe]\n");
	$should_collect_stats = false; // will start collecting stats only after first found the timeframe
}

$f = @fopen($file_name, "r");
// will hold  a 2 dimension array first key -  the time (per 10 minute) the second - the category .
// the value will be the nubmer of times the category was found in timeframe
$stats = array(); 

$timeframe = "?";

$stats_to_print = array();
while(!feof($f))
{
	++$i;
	if ($i % 10000 == 0)
		fprintf($stderr, "$i lines [$timeframe]\r");

	if ( $limit && $i > $limit )
	{
		fprintf($stderr, "\n\n	reached the limit [$limit] lines\n");
		break;
	}
	
	$s = fgets($f);
	
	if ( ! $s ) continue ; // skip an empty line
	
	$parts = explode ( " " , $s ,10); // no need to split beyond 10 tokens
	$date = @$parts[3];
	$url = @$parts[6];
	$res_code = @$parts[8];
	
	@list ( $day , $hour , $minute , $second ) = explode ( ":" , $date );
	
	
	if ( $resolution == "1" )
	{
		if ( $should_collect_stats ) $display_timeframe = $hour . ":" . $minute;
		$timeframe = $hour . $minute;
	}
	elseif ( $resolution == "2" )
	{
		// use the first digit of the seconds too
		if ( $should_collect_stats ) $display_timeframe = $hour . ":" . $minute . ":" . $second[0] . "0";
		$timeframe = $hour . $minute . $second[0] . "0";
	}
	else
	{
		// the key is the hour and the first digit of the minute
		if ( $should_collect_stats ) $display_timeframe = $hour . ":" . $minute[0] . "0";
		$timeframe = $hour . $minute[0] . "0";		
	}
	
//	$display_timeframe = $hour . ":" . ( $resolution == "1" ? $minute : $minute[0] ); // for display 
//	if ( $hour[0] == "0" ) $hour = $hour[1]; // only last digit
//	$timeframe = $hour . ( $resolution == "1" ? $minute : $minute[0] ) ;  // the hour and the first digit of the minutes or the minute - depending on the resolution
	
	if ( ! $timeframe ) 
	{
		fprintf($stderr, "\n\n	cannot log line with date [$date]. skipping...\n");
		continue; 
	}
	
//fprintf($stderr, "[$start_timeframe][$end_timeframe][$resolution] timeframe [$timeframe]\n");	
	
	if ( !$should_collect_stats && $start_timeframe && $start_timeframe == $timeframe )
	{
		fprintf($stderr, "\n\n	found start timeframe [$start_timeframe]. starting.\n");
		$should_collect_stats = true;
	}
	if ( ! $should_collect_stats ) continue;
	
	if ( ! $found_end_timeframe && $end_timeframe == $timeframe )
	{
		fprintf($stderr, "\n\n	found end timeframe [$end_timeframe]\n");
		$found_end_timeframe = true;
	}
	
	// see if we reached the end condition
	if ( $found_end_timeframe && $end_timeframe != $timeframe)
	{
		 fprintf($stderr, "\n\n	found end timeframe [$end_timeframe] and now switched to [$timeframe]. exiting.\n");
		 break;
	}
//print_r ( $parts );
//print ( $timeframe );

	$new_timeframe = false;
	$timeframe_stats = @$stats[$timeframe];
	if ( ! $timeframe_stats )
	{
		$stats_to_print[]=$timeframe;
		$new_timeframe = true; 
		$timeframe_stats = array(); // creaet a new array to fill
		$timeframe_stats["display"] = $display_timeframe;
		$formated_day = substr ($day,1);
	}
	
	if ( strpos($url,"collectstats") != false )
		$category = "ps2:collectstats";
	elseif ( strpos($url,"startwidgetsession") != false )
		$category = "ps2:startwidgetsession";
	elseif ( strpos($url,"getentry") != false )
		$category = "ps2:getentry";
	elseif ( strpos($url,"multirequest") != false )
		$category = "ps2:multirequest";
	elseif ( strpos($url,"startsession") != false )
		$category = "ps2:startsession";
	elseif ( strpos($url,"executeplaylist") != false )
		$category = "ps2:executeplaylist";
	elseif ( strpos($url,"partnerservices2") != false )
		$category = "ps2:other";
	elseif ( strpos($url,"kwidget") !== false )
		$category = "kwidget";
	elseif ( strpos($url,"crossdomain") !== false  )
		$category = "crossdomain";
	elseif ( strpos($url,"uiconf/") !== false  )
		$category = "uiconf";
	elseif ( strpos($url,"content/entry/data") !== false  )
		$category = "entry/data";
	elseif ( strpos($url,"kdpwrapper") !== false  )
		$category = "kdpwrapper";
	elseif ( strpos($url,"cacheswf") !== false  )
		$category = "cacheswf";
	elseif ( strpos($url,"/thumbnail/entry_id/") !== false  )
		$category = "thumbnail";
	elseif ( strpos($url,"kse/") !== false  )
		$category = "kse";
	elseif ( strpos($url,"/flv/") !== false  )
		$category = "flv";
	elseif ( strpos($url,"kcw/") !== false  )
		$category = "kcw";
	elseif ( strpos($url,"swf/plugins") !== false  )
		$category = "swf/plugins";
	elseif ( strpos($url,"FlexWrapper/") !== false  )
		$category = "flexwrapper";
	elseif ( strpos($url,"keditorservices/") !== false  )
		$category = "keditorservices";
	else
		$category = "other";  // TODO - write to different log so we'll be able to decide if to clasify

	$category_stats = @$timeframe_stats[$category];
	$category_stats++;
	$timeframe_stats[$category] = 	$category_stats;
	$stats[$timeframe] = $timeframe_stats;
	
	// print the stats of 2 timeframes back
	if( $new_timeframe )
	{
		if ( count ( $stats_to_print ) > 2 ) // the stack has data of 2 timeframes back
		{
			$timeframe_to_pop = $stats_to_print[0];
			unset ( $stats_to_print[0] ); // remove the head of the stack
			print_timeframe ( $formated_day , $timeframe_to_pop , @$stats[$timeframe_to_pop] );
			unset ( $stats[$timeframe_to_pop] );
		}
	}
	
	$last_timeframe = $timeframe;
}

//pop all the rest of the timeframes
foreach ( $stats_to_print as $timeframe )
{

	print_timeframe ( $formated_day , $timeframe , @$stats[$timeframe] );
	unset ( $stats[$timeframe_to_pop] );
}

fprintf($stderr, "$i lines\r");

//var_dump ( $stats );


fclose($stderr);
fclose($f);
?>
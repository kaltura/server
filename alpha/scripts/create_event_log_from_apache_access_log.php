<?php
define ( "EVENT_LOG_SEPARATOR" , "," );

class kEvent 
{
	private $arr;
	private $prefix;
	public function kEvent ( array $arr , $prefix )
	{
		$this->arr = $arr;
		$this->prefix = $prefix;
	}
	
	public function __get( $prop )
	{
		// to be on the safe side - replace the separator with a special character ~
		return str_replace( EVENT_LOG_SEPARATOR , "~" , @$this->arr[$this->prefix.":" . $prop] );
	}
}

function parse_vars($s)
{
	$params = explode("&", $s);
	$vars = array();
	foreach($params as $param)
	{
		$pair = explode("=", $param, 2);
		if (count($pair) == 2)
			$vars[$pair[0]] = $pair[1];
	}

	return $vars;
}


$stderr = fopen("php://stderr", "w");

$i = 0;

$file_name = @$argv[1];
$desired_mode = @$argv[2];

if ($file_name)
{
	fprintf($stderr, "\nfilename: $file_name\n");
	$f = @fopen($file_name, "r");
	if (!$f)
	{
		fprintf(stderr, "no file or file not found [$file_name]\n");
		die;
	}
}
else
	$f = fopen("php://stdin", "w");


if ( ! $desired_mode ) $desired_mode = 3 ; // KDP events from ps2 & ps3	

$total_lines = 0;
$ps2_lines = 0;
$ps3_lines = 0;
$ps3_kmc_lines = 0;
$ignored_lines = 0;

while(!feof($f))
{

	++$i;
/*
	if ($i % 10000 == 0)
		fprintf($stderr, "$i\r");
*/
	$total_lines++;
		
	$s = fgets($f);
	// collect statistics for old and new KDP
	if ( strstr($s, "collectstats") ) $mode = 1;		// ps2 collectstats
	elseif ( strstr($s, "action=collect") && strstr($s, "service=stats") )  $mode = 2;	// ps3 collect stats	
	elseif ( strstr($s, "action=kmcCollect") && strstr($s, "service=stats") )  $mode = 4;	// ps3 kmcCollect stats
	else $mode = 0;	// not a relevant line 

	if ( $mode == 0 ) 
	{
		$ignored_lines ++;
		continue;
	}

	$arr = explode(" ", $s);
	$ip = $arr[0];
	$date = $arr[3]." ".$arr[4];
	$url = $arr[6];
	
	$uv = "";
	for ($j=2; $j<=5; $j++ )
	{
		$uv = @$arr[count($arr) - $j];
		if (strpos($uv, '"uv_') === 0)
		{
			$uv = substr($uv, 4, 32);
			break;
		}
	}
	
	$date = strtotime(substr($date, 1, strlen($date) - 2));
	$formatted_date = strftime("%Y-%m-%d %H:%M:%S", $date);

	
	$s = urldecode($url);
	$s = parse_url($s, PHP_URL_QUERY);

	$vars = parse_vars($s);

	if ( $mode == 1 && ($desired_mode&1) )
	{
		$ps2_lines ++;
		
		$partner_id = @$vars["partner_id"];
		$obj_type = @$vars["obj_type"];
		
		if ($obj_type == "entry")
		{
			$extra_info = @$vars["extra_info"];
			$evars = parse_vars($extra_info);
	
			if (!array_key_exists("obj_id", $vars) || !array_key_exists("command", $vars))
			{
				fprintf($stderr, "\n%s\n", $s);
				continue;
			}
			$widget_id = @$evars["widgetId"];	
			$entry_id = @$vars["obj_id"];
			$uid = @$vars["uid"];
			
			$command = @$vars["command"];
			
			if ( $command == "view" ) $event_type = "2";
			else if ( $command == "play" ) $event_type = "3";
			else $event_type ="0";	
					
			
			$eventLine = 
				"0.5:0.1" . EVENT_LOG_SEPARATOR 
				. $event_type . EVENT_LOG_SEPARATOR
				. $formatted_date . EVENT_LOG_SEPARATOR   // use server time
				. ""  . EVENT_LOG_SEPARATOR
				. $partner_id  . EVENT_LOG_SEPARATOR
				. $entry_id  . EVENT_LOG_SEPARATOR
				. $uv  . EVENT_LOG_SEPARATOR
				. $widget_id  . EVENT_LOG_SEPARATOR
				. ""  . EVENT_LOG_SEPARATOR
				. $uid  . EVENT_LOG_SEPARATOR
				. ""  . EVENT_LOG_SEPARATOR
				. ""  . EVENT_LOG_SEPARATOR
				. $ip  . EVENT_LOG_SEPARATOR
				. ""  . EVENT_LOG_SEPARATOR
				. ""  . EVENT_LOG_SEPARATOR
				. 0  . EVENT_LOG_SEPARATOR
				. ""  . EVENT_LOG_SEPARATOR
				. ""	. EVENT_LOG_SEPARATOR	// duw to the way flash sends the referrer - allow it to override
				. "" 
				. PHP_EOL ;
			
		}
	}
	elseif  ( $mode == 2 && ($desired_mode&2)  )
	{
		$ps3_lines ++;
		// create event lines for ps3
		$event = new kEvent ( $vars , "event" );
		
		$eventLine = 
			$event->clientVer . EVENT_LOG_SEPARATOR 
			. $event->eventType  . EVENT_LOG_SEPARATOR
			. $formatted_date . EVENT_LOG_SEPARATOR   // use server time
			. $event->sessionId  . EVENT_LOG_SEPARATOR
			. $event->partnerId  . EVENT_LOG_SEPARATOR
			. $event->entryId  . EVENT_LOG_SEPARATOR
			. $event->uniqueViewer  . EVENT_LOG_SEPARATOR
			. $event->widgetId  . EVENT_LOG_SEPARATOR
			. $event->uiconfId  . EVENT_LOG_SEPARATOR
			. $event->userId  . EVENT_LOG_SEPARATOR
			. $event->currentPoint  . EVENT_LOG_SEPARATOR
			. $event->duration  . EVENT_LOG_SEPARATOR
			. $ip  . EVENT_LOG_SEPARATOR
			. $event->processDuration  . EVENT_LOG_SEPARATOR
			. $event->controlId  . EVENT_LOG_SEPARATOR
			. ($event->seek == "1" || $event->seek == "true" ? "1" : "0" ) . EVENT_LOG_SEPARATOR
			. $event->newPoint  . EVENT_LOG_SEPARATOR
			. ( $event->referrer ? $event->referrer : "" )	. EVENT_LOG_SEPARATOR	// duw to the way flash sends the referrer - allow it to override
			. $event->eventTimestamp
			. PHP_EOL ;
					
			
	}
	elseif  ( $mode == 4 && ($desired_mode&4)  )
	{
		$ps3_kmc_lines ++;
		// create event lines for ps3
		$event = new kEvent ( $vars , "kmcEvent" );
		
		$ks = @$vars["ks"];
		
		$eventLine = 
			$event->clientVer . EVENT_LOG_SEPARATOR 
			. $event->kmcEventType  . EVENT_LOG_SEPARATOR
			. $formatted_date . EVENT_LOG_SEPARATOR   // use server time
			. $event->kmcEventActionPath  . EVENT_LOG_SEPARATOR
			. $event->eventTimestamp  . EVENT_LOG_SEPARATOR
			. $event->partnerId  . EVENT_LOG_SEPARATOR
			. $event->userId  . EVENT_LOG_SEPARATOR
			. $event->entryId  . EVENT_LOG_SEPARATOR
			. $event->widgetId  . EVENT_LOG_SEPARATOR
			. $event->uiconfId  . EVENT_LOG_SEPARATOR
			. $ks . EVENT_LOG_SEPARATOR
			. $ip  . EVENT_LOG_SEPARATOR
			. PHP_EOL ;
		
		// write to the the kmcEvents log NOT the kdpEvents log
	}
	else
	{
		$ignored_lines++;
		continue;
	}
	
	print $eventLine;
}

fprintf($stderr, PHP_EOL . "total_lines [$total_lines] ps2_lines [$ps2_lines] ps3_lines [$ps3_lines] ps3_kmc_lines [$ps3_kmc_lines] ignored_lines [$ignored_lines]" . PHP_EOL );

fclose($stderr);
fclose($f);

?>
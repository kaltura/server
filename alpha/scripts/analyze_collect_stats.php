<?php

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


if ( ! $desired_mode ) $desired_mode = 3 ; //all modes 	
while(!feof($f))
{

	++$i;
	if ($i % 10000 == 0)
		fprintf($stderr, "$i\r");

	$s = fgets($f);
	// collect statistics for old and new KDP
	if ( strstr($s, "collectstats") ) $mode = 1;		// ps2 collectstats
	elseif ( strstr($s, "action=collect") && strstr($s, "service=stats") )  $mode = 2;	// ps3 collect stats	
	else $mode = 0;	// not a relevant line 

	if ( $mode == 0 ) continue;

	$arr = explode(" ", $s);
	$ip = ip2long($arr[0]);
	$date = $arr[3]." ".$arr[4];
	$url = $arr[6];
	
	$uv = @$arr[count($arr) - 2];
		if (strpos($uv, '"uv_') === 0)
	$uv = substr($uv, 4, 32);
	else
		$uv = "";
                
	$date = strtotime(substr($date, 1, strlen($date) - 2));
	$date = strftime("%Y-%m-%d %H:%M:%S", $date);

	$s = urldecode($url);
	$s = parse_url($s, PHP_URL_QUERY);

	$vars = parse_vars($s);

	if ( $mode == 1 && ($desired_mode&1) )
	{
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
				
			$entry_id = $vars["obj_id"];
	
			$command = $vars["command"];
	
			$widget_id = @$evars["widgetId"];
			
			if (strpos($entry_id, "'") !== false || strpos($widget_id, "'") !== false)
				continue;
	
			print "INSERT INTO collect_stats VALUES(".
			"$ip,'$date','$partner_id','$entry_id','$widget_id','$command', '$uv');/*ps2*/\n";
			//print $ip." ".$date." ";
			//print "$partner_id $entry_id [$command] [$widget_id]\n";
		}
	}
	elseif  ( $mode == 2 && ($desired_mode&2)  )
	{
		$entry_id = @$vars["event:entryId"];
		$partner_id = @$vars["event:partnerId"];
		
		$event_type = @$vars["event:eventType"];
		
		if ( $event_type == 2 )
			$command = "view";
		elseif ( $event_type == 3 )
			$command = "play";
		else
			continue;
		
		$widget_id = @$vars["event:widgetId"];
		
		if (strpos($entry_id, "'") !== false || strpos($widget_id, "'") !== false)
				continue;
	
		print "INSERT INTO collect_stats VALUES(".
			"$ip,'$date','$partner_id','$entry_id','$widget_id','$command', '$uv');/*ps3*/\n";
			//print $ip." ".$date." ";		
	}

}


fclose($stderr);
fclose($f);


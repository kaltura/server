<?php

/*
known errors:
swf
crossdomain
Powered
robots
sandbox
favico
extwid
data
www.kaltura.com/]
grep -v swf errs|grep -v crossdo |grep -v Powered |grep -v robots|grep -v sandbox|grep -v favico|grep -v extwid|grep -v data|grep -v ".com/]" |less
*/

$summary = array();
$i = 0;

$errhandle = null;
if (@$argv[1])
{
	$errhandle = fopen($argv[1], "w");
}

$stderr = fopen("php://stderr", "w");
$handle = fopen("php://stdin", "r");
$pattern = '!^([^ ]+) ([^ ]+) ([^ ]+) \[([^\]]+)\] "([^ ]+) ([^ ]+) ([^/]+)/([^"]+)" ([^ ]+) ([^ ]+) "([^"]+)" "(.+)"!';
if ($handle) {
    while (!feof($handle)) {
        $line = fgets($handle);
	$i++;
	if ($i % 10000 == 0) fprintf($stderr, "$i\r");

        $result = array();
        if (!preg_match($pattern, $line, $result))
        {
		logerror("($i) error (no match) in [" . $line . "]\n");
        }
	else if (count($result) != 13)
	{
        	logerror("($i) error (args={count($result}) in [" . $line . "]\n");
	}
        else
        {
        	$url = $result[6];
        	$partner = getPartnerDetailsFromUrl($url);
		//if ($partner !== null)
		{
     		   	$contentSize = $result[10];
       	 		$contentSize = $contentSize / 1024;
       	 		if (@$summary[$partner["pid"]])
        			$summary[$partner["pid"]] += $contentSize;
        		else
        			$summary[$partner["pid"]] = $contentSize; 
		}
        }
    }
    fclose($handle);
   
    ksort($summary);
	foreach($summary as $key => $value)
		print "$key,".floor($value)."\n"; 
    //print_r($summary);
}

if ($errhandle)
	fclose($errhandle);

function logerror($msg)
{
	global $errhandle;
	if ($errhandle)
		fprintf($errhandle, "%s", $msg);
}

function parseDate($text)
{
	return;
}

function getPartnerDetailsFromUrl($url)
{
	$result = array();
	if (preg_match("/http:\/\/[^.]+.kaltura.com\/p\/([0-9]+)\//", $url, $result) || preg_match("/http:\/\/www.kaltura.com\/p\/([0-9]+)\/sp\/([0-9]+)\//", $url, $result))
	{
		return array (
			"pid" => $result[1],
			"subpid" => @$result[2]
		);
	}
	else
	{
		logerror("ERROR: partner details not found in url [" . $url . "]\n");
		return null;
	}
}

?>

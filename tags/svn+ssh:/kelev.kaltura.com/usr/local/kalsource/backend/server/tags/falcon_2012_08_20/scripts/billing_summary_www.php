<?php

$summary = array();
$i = 0;

$stderr = fopen("php://stderr", "w");
$handle = fopen("php://stdin", "r");
if ($handle) {
    while (!feof($handle)) {
        $line = fgets($handle);
		$i++;
		if ($i % 10000 == 0) fprintf($stderr, "$i\r");
        
        $result = array();
	        if (preg_match_all("/\"GET \/p\/(\d+)\/.*?\" 200 (\d+)/", $line, $matches))
        {
        	//print $matches[0][0].":".$matches[1][0].":".$matches[2][0]."\n";
        	$partner_id = intval($matches[1][0]);
        	
        	$contentSize = $matches[2][0];
        	$contentSize = $contentSize / 1024;
        	if (@$summary[$partner_id])
        		$summary[$partner_id] += $contentSize;
        	else
        		$summary[$partner_id] = $contentSize; 
        }
    }
    fclose($handle);
    
    ksort($summary);
	foreach($summary as $key => $value)
		print "$key,".floor($value)."\n"; 
    //print_r($summary);
}

?>
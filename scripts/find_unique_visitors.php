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
        if (preg_match_all("/\"uv_(.*?)\"/", $line, $matches))
        {
                $uv = $matches[1][0];
		if(strlen($uv) == 32)
		{
	                $summary[$uv] = true;
        	        //print $uv."\n";
		}
        }
    }
    fclose($handle);
//print "\n".count($summary)."\n";
	foreach($summary as $uv => $v)
        print "$uv\n";
}

?>
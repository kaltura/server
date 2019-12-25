<?php

$rc = null;
$output = null;
$src = implode(" ", array_slice($argv, 1));

$patterns = array('/-geometry (\d+x?\d*)/', '/-colorspace RGB/');
$replace = array('-resize $1 -extent $1', '-colorspace RGB -gravity center -regard-warnings');

$dest = preg_replace($patterns, $replace, $src);
print "Executing " . $dest . "\n";
$path = getenv("PATH");
exec("convert " . $dest . " 2>&1", $output, $rc);
$outputStr = implode("\n", $output);
print $outputStr . "\n";

$reconvertMessages = array ("Segmentation fault", "Error: /rangecheck in --run--", "/undefined in findresource");
if($rc != 0) {
        $reconvert = false;
        foreach($reconvertMessages as $msg) {
                if(strpos($outputStr,$msg) !== False) {
                        $reconvert = true;
                        break;
                }
        }

        if($reconvert) {
                print "Execute on higher GS version\n";
                putenv("PATH=" . $path);
                passthru("convert " . $dest, $rc);
        }
}

exit( $rc );


<?php
// convert Digital Element anonymous db csv file into a binary file with constant record length
// from ip, to ip, 20 chars proxyType, 20 chars proxyDescription
const MAX_LEN = 20;

$f = fopen($argv[1], "r");
while(!feof($f))
{
        $s = trim(fgets($f));
        if (!strlen($s))
                break;

        list($startIp, $endIp, $proxyType, $proxyDescription) = explode(",", $s);
        #echo "$startIp, $endIp, $proxyType, $proxyDescription\n";

        if (strpos($startIp, "#") === 0)
                continue;

        $proxyType = substr($proxyType, 0, MAX_LEN);
        $proxyDescripion = substr($proxyDescription, 0, MAX_LEN);
        $bin = pack("LLA".MAX_LEN."A".MAX_LEN, ip2long($startIp), ip2long($endIp), $proxyType, $proxyDescripion);
        echo $bin;
}

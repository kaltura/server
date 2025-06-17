<?php
// convert Digital Element anonymous db csv file into a binary file with constant record length
// the bin file header consists of two comma separated lines for proxy types, and proxy descriptions
// followed by ip ranges: // from ip, to ip, proxy type lookup, proxy description lookup

$types = array();
$descs = array();
$ips = array();

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

        if (!isset($types[$proxyType]))
        {
                $types[$proxyType] = count($types);
        }

        $typeNum = $types[$proxyType];

        if (!isset($descs[$proxyDescription]))
        {
                $descs[$proxyDescription] = count($descs);
        }

        $descNum = $descs[$proxyDescription];
        $record = pack("LLCC", ipToLong($startIp), ipToLong($endIp), $typeNum, $descNum);
        $ips[ipToLong($startIp)] = $record;
}

echo implode(",", array_keys($types))."\n";
echo implode(",", array_keys($descs))."\n";
ksort($ips);
foreach($ips as $k => $v)
{
        echo $v;
}
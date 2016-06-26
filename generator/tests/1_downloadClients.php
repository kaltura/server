<?php

require_once(__DIR__ . '/utils.php');

if ($argc < 3)
	die("Usage:\n\tphp " . basename(__file__) . " <clients name> <target dir>\n");
	
$clientsName = $argv[1];
$targetDir = fixSlashes($argv[2]);

if (!is_dir($targetDir))
	mkdir($targetDir, 0777, true);

$config = parse_ini_file(dirname(__file__) . '/config.ini', true);

$serverName = $config['general']['server_name'];

$summary = file_get_contents("http://{$serverName}/{$clientsName}/summary.kinf");
$summary = unserialize($summary);

$generatedDate = $summary['generatedDate'];
foreach ($summary as $name => $params)
{
	if (!is_array($params))
		continue;
	
	$fileName = "{$name}_{$generatedDate}.tar.gz";
	$fileUrl = "http://{$serverName}/{$clientsName}/{$fileName}";
	$localPath = "{$targetDir}/{$fileName}";
	
	echo "Downloading {$fileUrl} to {$localPath}\n";
	$clientTarGz = file_get_contents($fileUrl);
	file_put_contents($localPath, $clientTarGz);
}

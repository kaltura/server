<?php

if ($argc < 2)
	die("Usage:\n\tphp " . basename(__file__) . " <clients name>\n");
	
$clientsName = $argv[1];

$config = parse_ini_file(dirname(__file__) . '/config.ini', true);

$serverName = $config['general']['server_name'];

$summary = file_get_contents("http://{$serverName}/{$clientsName}/summary.kinf");
$summary = unserialize($summary);

$generatedDate = $summary['generatedDate'];
foreach ($summary as $index => $name)
{
	if (!is_numeric($index))
		continue;
	
	$fileName = "{$name}_{$generatedDate}.tar.gz";
	$fileUrl = "http://{$serverName}/{$clientsName}/{$fileName}";
	$localPath = str_replace('\\', '/', dirname(__file__) . "/{$fileName}");
	
	echo "Downloading {$fileUrl} to {$localPath}\n";
	$clientTarGz = file_get_contents($fileUrl);
	file_put_contents($localPath, $clientTarGz);
}

<?php

function getIndexSchemas($dirname) 
{
	$indexSchemas = array();
	$it = new RecursiveDirectoryIterator($dirname);
	foreach (new RecursiveIteratorIterator($it) as $file) {
		if(basename($file) == "IndexSchema.xml") { 
			$path = dirname($file) . "/../lib/model/index/";
			if(!file_exists($path)) {
				mkdir($path, null, true);
			}
			$indexSchemas[realpath($file)] = $path;
		}
	}
	return $indexSchemas;
}

$indexSchemas = getIndexSchemas(__DIR__ . "/../../../../alpha/");
$indexSchemas = array_merge( $indexSchemas, getIndexSchemas(__DIR__ . "/../../../../plugins/"));

$args = "";
foreach($indexSchemas as $schemaPath => $dirPath) {
	$args .= "$schemaPath=$dirPath ";
}

$exe = __DIR__ . "/IndexObjectsGenerator.php";
$template = __DIR__ . "/../../../../configurations/sphinx/kaltura.conf.source";
$generatedConf = __DIR__ . "/../../../../configurations/sphinx/kaltura.conf.template";

$exeApiSearchObjects = __DIR__ . "/ApiSearchObjectsGenerator.php";

$returnVar = 0;
passthru("php {$exe} {$template} {$generatedConf} {$args}", $returnVar);
if ($returnVar > 0)
	exit ($returnVar);

$args = implode(array_keys($indexSchemas), ' ');

$exe = __DIR__ . "/IndexObjectsGenerator.php";
$template = __DIR__ . "/../../../../configurations/sphinx/kaltura.conf.source";
$generatedConf = __DIR__ . "/../../../../configurations/sphinx/kaltura.conf.template";

$exeApiSearchObjects = __DIR__ . "/ApiSearchObjectsGenerator.php";

$returnVar = 0;
passthru("php {$exeApiSearchObjects} {$args}", $returnVar);
exit ($returnVar);
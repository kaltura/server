<?php

function getIndexSchemas($dirname) 
{
	$indexSchemas = array();
	$it = new RecursiveDirectoryIterator($dirname);
	foreach (new RecursiveIteratorIterator($it) as $file) {
		if(basename($file) == "IndexSchema.xml") { 
			$indexSchemas[realpath($file)] =  realpath(dirname($file) . "/../lib/model/index/" );
		}
	}
	return $indexSchemas;
}

function getIndexSchemasForApiObjects($dirname, $isPlugin)
{
	$indexSchemas = array();
	$it = new RecursiveDirectoryIterator($dirname);
	foreach (new RecursiveIteratorIterator($it) as $file) {
		if(basename($file) == "IndexSchema.xml") {
			if ($isPlugin)
				$path = dirname($file) . "/../lib/api/search/";
			else
				$path = dirname($file) . "/../../api_v3/lib/types/search/";

			if (!file_exists($path))
				mkdir($path, 0777, true);

			$indexSchemas[realpath($file)] = realpath($path);
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

$indexSchemas = getIndexSchemasForApiObjects(__DIR__ . "/../../../../alpha/", false);
$indexSchemas = array_merge( $indexSchemas, getIndexSchemasForApiObjects(__DIR__ . "/../../../../plugins/", true));

$args = "";
foreach($indexSchemas as $schemaPath => $dirPath) {
	$args .= "$schemaPath=$dirPath ";
}

$exe = __DIR__ . "/IndexObjectsGenerator.php";
$template = __DIR__ . "/../../../../configurations/sphinx/kaltura.conf.source";
$generatedConf = __DIR__ . "/../../../../configurations/sphinx/kaltura.conf.template";

$exeApiSearchObjects = __DIR__ . "/ApiSearchObjectsGenerator.php";

$returnVar = 0;
passthru("php {$exeApiSearchObjects} {$args}", $returnVar);
exit ($returnVar);
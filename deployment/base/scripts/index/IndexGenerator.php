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
	
$indexSchemas = getIndexSchemas("@APP_DIR@/alpha/");
$indexSchemas = array_merge( $indexSchemas, getIndexSchemas("@APP_DIR@/plugins/"));

$args = "";
foreach($indexSchemas as $schemaPath => $dirPath) {
	$args .= "$schemaPath=$dirPath ";
}

$exe = __DIR__ . "/IndexObjectsGenerator.php";
$template = "@APP_DIR@/configurations/sphinx/kaltura.conf.source";
$generatedConf = "@APP_DIR@/configurations/sphinx/kaltura.conf.template";

$returnVar = 0;
passthru("php {$exe} {$template} {$generatedConf} {$args}", $returnVar);

exit ($returnVar);	
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
	
$indexSchemas = getIndexSchemas("c:\\opt\\kaltura\\app\\alpha\\");
$indexSchemas = array_merge( $indexSchemas, getIndexSchemas("c:\\opt\\kaltura\\app\\plugins\\"));

$args = "";
foreach($indexSchemas as $schemaPath => $dirPath) {
	$args .= "$schemaPath=$dirPath ";
}

$exe = __DIR__ . "/IndexObjectsGenerator.php";
$template = __DIR__ . "/../../../../../configurations/sphinx/kaltura.conf.template";
$generatedConf = __DIR__ . "/../../../../../configurations/sphinx/kaltura.conf.generated";
passthru("php {$exe} {$template} {$generatedConf} {$args}");
	
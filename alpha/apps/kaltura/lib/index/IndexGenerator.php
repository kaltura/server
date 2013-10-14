<?php

function generate($dirname) 
{
	$it = new RecursiveDirectoryIterator($dirname);
	foreach (new RecursiveIteratorIterator($it) as $file) {
		if(basename($file) == "IndexSchema.xml") { 
			$exe = __DIR__ . "/IndexObjectsGenerator.php";
			$path =  realpath(dirname($file) . "/../" );
			passthru("php {$exe} {$file} {$path}");
		}
	}
}
	
generate("c:\\opt\\kaltura\\app\\alpha\\");
generate("c:\\opt\\kaltura\\app\\plugins\\");
	
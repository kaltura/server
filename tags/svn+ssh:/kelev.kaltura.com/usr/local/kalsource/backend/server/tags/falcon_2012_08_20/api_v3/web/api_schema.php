<?php

require_once("../bootstrap.php");

$root = myContentStorage::getFSContentRootPath();
$outputPathBase = "$root/content/clientlibs";

$fileLocation = "$outputPathBase/KalturaClient.xml";

if (!file_exists($fileLocation))
	die("KalturaClient.xml was not found");
	
header("Content-Type: text/xml");
readfile($fileLocation);

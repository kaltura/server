<?php
$fileLocation = "../../generator/output/xml/KalturaClient.xml";
if (!file_exists($fileLocation))
	die("KalturaClient.xml was not found");
	
header("Content-Type: text/xml");
readfile($fileLocation);

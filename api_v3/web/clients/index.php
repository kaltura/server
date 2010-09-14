<?php
require_once("..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."bootstrap.php");
KalturaLog::setContext("CLIENTS");
KalturaLog::debug(__FILE__ . " start");

$generatorPath 			= KAutoloader::buildPath(KALTURA_ROOT_PATH, "generator");
$generatorOutputPath 	= KAutoloader::buildPath(KALTURA_ROOT_PATH, "generator", "output");
$generatorConfigPath 	= KAutoloader::buildPath(KALTURA_ROOT_PATH, "generator", "config.ini");
$config = new Zend_Config_Ini($generatorConfigPath);
?>
<ul>
<?php 
foreach($config as $name => $item)
{
	if (!$item->get("public-download"))
		continue;
		
	$outputFilePath = KAutoloader::buildPath($generatorOutputPath, $name.".tar.gz");
	$outputFileRealPath = realpath($outputFilePath);
	if ($outputFileRealPath)
	{
		print('<li>');
		print('<a href="download.php?name='.$name.'"> Download '.$name.'</a>');
		print('</li>');
	}
}
?>
</ul>
<?php 
KalturaLog::debug(__FILE__ . " end");
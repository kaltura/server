<?php 
error_reporting(E_ALL);
ini_set( "memory_limit","512M" );

chdir(__DIR__);

//bootstrap connects the generator to the rest of Kaltura system
require_once(__DIR__ . "/bootstrap.php");

$options = getopt('h', array(
	'help',
));

function showHelpAndExit()
{
	echo "Usage:\n";
	echo "\tphp " . __FILE__ . " [options] [destination]\n";
	echo "\tOptions:\n";
	echo "\t\t-h, --help:   \tShow this help.\n";
	
	exit;
}

$schemaXmlPath = null;
foreach($options as $option => $value)
{
	if($option == 'h' || $option == 'help')
	{
		showHelpAndExit();
	}
	array_shift($argv);
}	 

//command line argument specifies the output path, if not specified will default to 
//<content root>/content/clientlibs
if (isset($argv[1]))
{
	$outputPathBase = $argv[1];
}
else
{
	$root = myContentStorage::getFSContentRootPath();
	$outputPathBase = "$root/content/clientlibs";
}

if(!file_exists($outputPathBase))
	mkdir($outputPathBase, 0755, true);

$xmlFileName = "$outputPathBase/KalturaClient.xml";

KalturaLog::info("Using code introspection to generate XML schema");
$xmlGenerator = new XmlClientGenerator();
$xmlGenerator->generate();

$files = $xmlGenerator->getOutputFiles();
file_put_contents($xmlFileName, $files["KalturaClient.xml"]);

KalturaLog::info("XML generated: $xmlFileName");

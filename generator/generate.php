<?php 
require_once("bootstrap.php");
$generateSingle = isset($argv[1]) ? $argv[1] : null;

$config = new Zend_Config_Ini("config.ini");
KalturaTypeReflectorCacher::disable();

if ($generateSingle)
{
	$found = false;
	// just to check that is exists
	foreach($config as $name => $item)
	{
		if (strtolower($name) === strtolower($generateSingle))
			$found = true;
	}
	if (!$found)
		throw new Exception("Configuration for [".$generateSingle."] was not found");
}

foreach($config as $name => $item)
{
	$generator = $item->get("generator");
	if ($generator === null)
		throw new Exception("No generator for [".$name."]");
		
	if ($generateSingle && strtolower($name) !== strtolower($generateSingle))
	{
		continue;
	}
		
	KalturaLog::info("$name using $generator");
	
	$include = $item->get("include");
	$exclude = $item->get("exclude");
	$additional = $item->get("additional");
	
	if ($include !== null && $exclude !== null)
		throw new Exception("Only include or exclude should be declared");
		
	if (!class_exists($generator))
		throw new Exception("Generator [".$generator."] not found");
		
	$pluginList = explode(',', $item->get("plugins"));
	foreach($pluginList as $plugin)
	{
		$pluginName = trim($plugin);
		if(!$pluginName) continue;
		KalturaPluginManager::addPlugin($pluginName);
	}
	
	$reflectionClass = new ReflectionClass($generator);
	$fromXml = $reflectionClass->isSubclassOf("ClientGeneratorFromXml");
	$fromPhp = $reflectionClass->isSubclassOf("ClientGeneratorFromPhp");
	if ($fromXml)
	{
		KalturaLog::info("Using XmlSchemaGenerator to generate the api schema");
		// first generate the xml
		$xmlGenerator = new XmlClientGenerator();
		$xmlGenerator->setIncludeOrExcludeList($include, $exclude);
		$xmlGenerator->setAdditionalList($additional);
		$xmlGenerator->generate();
		$files = $xmlGenerator->getOutputFiles();
		file_put_contents("temp.xml", $files["KalturaClient.xml"]);
		
		$instance = $reflectionClass->newInstance("temp.xml");
		
		if($item->get("generateDocs"))
			$instance->setGenerateDocs($item->get("generateDocs"));
			
		if($item->get("package"))
			$instance->setPackage($item->get("package"));
			
		if($item->get("subpackage"))
			$instance->setSubpackage($item->get("subpackage"));
		
		if (isset($item->params))
		{
			foreach($item->params as $key => $val)
			{
				$instance->setParam($key, $val);
			}
		}
	}
	else if ($fromPhp)
	{
		$instance = $reflectionClass->newInstance();
		$instance->setIncludeOrExcludeList($include, $exclude);
		$instance->setAdditionalList($additional);
		
		if($item->get("package"))
			$instance->setPackage($item->get("package"));
			
		if($item->get("subpackage"))
			$instance->setSubpackage($item->get("subpackage"));
	}
	else
	{
		throw new Exception("Invalid generator [$generator]");
	}
	
	KalturaLog::info("Trying to generate");
	$instance->generate();
	$outputPath = "output".DIRECTORY_SEPARATOR.$name;
	if (realpath($outputPath) === false)
		mkdir($outputPath, "0777", true);

	$files = $instance->getOutputFiles();
	foreach($files as $file => $data)
	{
		$filePath = realpath($outputPath).DIRECTORY_SEPARATOR.$file;
		$dirName = pathinfo($filePath, PATHINFO_DIRNAME);
		if (!file_exists($dirName))
			mkdir($dirName, "0777", true);

		file_put_contents($filePath, $data);
	}
	
	if ($fromXml)
		unlink("temp.xml");
		

	if (count($files) == 0)
	{
		KalturaLog::info("No output files found");
	}
	else
	{
		createPackage($outputPath, $name);
	}
		
	KalturaLog::info("$name generated successfully");
}

function createPackage($outputPath, $generatorName)
{
	KalturaLog::info("Trying to package");
	$output = shell_exec("tar --version");
	if ($output === null)
	{
		KalturaLog::warning("Skipping packaging, \"tar\" command not found! On Windows, tar can be installed using Cygwin, and it should be added to the path");
	}
	else
	{
		$apiRevision = getApiRevision();
		if ($apiRevision)
			$fileName =  "$generatorName"."_r".$apiRevision.".tar.gz";
		else
			$fileName =  "$generatorName.tar.gz";
		$gzipOutputPath = "..".DIRECTORY_SEPARATOR.$fileName;
		$cmd = "tar -czf \"$gzipOutputPath\" * --exclude=\".*\"";
		$oldDir = getcwd();
		
		$outputPath = realpath($outputPath);
		KalturaLog::debug("Changing dir to [$outputPath]");
		chdir($outputPath);
		
		KalturaLog::info("Executing: $cmd"); 
		passthru($cmd);
		
		if (file_exists($gzipOutputPath))
			KalturaLog::info("Package created successfully: $gzipOutputPath");
		else
			KalturaLog::err("Failed to create package");
			
		KalturaLog::debug("Restoring dir to [$oldDir]");
		chdir($oldDir);
	}
}

function getApiRevision()
{
	return null;
	$output = shell_exec("svn info \"".KALTURA_API_PATH."\"");
	if ($output === null)
	{
		KalturaLog::warning("Can't find the api subversion revision. SVN command is probably not installed, or not available in path");
		return null;
	}
	else
	{
		$match = array();
		if (preg_match("/Revision: ([0-9]*)/", $output, $match))
		{
			$r = $match[1];
			KalturaLog::info("API revision is [$r]");
			return $r;
		}
		else
		{
			KalturaLog::err("Failed to parse the api subversion revision.");
			return null;
		}
	}
}
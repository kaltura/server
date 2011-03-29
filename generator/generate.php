<?php 
/**
 * Client Libraries Generator - generate all the clients specified in the given config.ini according to Kaltura API reflection.
 * 
 * Create your client generator by - 
 * 		1. Copying one of the existing client generators (*ClientGenerator.php) and name it accordingly.
 * 		2. Edit the classes/functions rules according to the target client language.
 * 		3. Add your static code to the sources folder under a folder by your language name.
 * 		4. Add your generator to config.ini, the ini category in brackets should be the folder name you used in the previous step.
 * 		5. Then below the ini category, write: generator = MyClientGenerator
 * 			MyClientGenerator being the name of your client generator class file without the .php extension.
 * To run the generator, use command line to run: php generate.php [arguments]
 * 
 * Optional arguments:
 * 		1. Generate Single - The first argument dictates that a single generator should be run instead of all defined in the config.ini
 * 							To use this argument, pass a valid id of one of the available generator classes.
 * 
 * config.ini file paramteres:
 * 		1. The client name is defined by block brackets (ini object) [client_name]
 * 		2. under the [clientname], define the generator parameters, each in new line, as following:
 * 			a. generator - The class name of the client library generator to use
 * 			b. exclude - whether to exclude any specific API services from the client library (can only be either exclude or include defined)
 * 			c. include - whether to include any specific API services from the client library (can only be either exclude or include defined)
 * 			d. plugins - whether to include any specific API services from plugins
 * 			e. additional - whether to include any additional objects not directly defined through API services
 * 			f. internal - whether to show this client in the Client Libraries UI in the testme console, or not
 * 			g. nopackage - whether to generate a tar.gz package from the client library folder 
 * 			h. nofolder - there will not be client folder, the client files will be in output folder (if it's a single file like XML schema)
 * 
 * Notes:
 * 		* Kaltura API ignores only un-sent parameters. Thus, if you would like a parameter value to be left unchanged
 * 			or in classes that contain read-only parameters, make sure to NOT send any un-changed parameters in your HTTP requests.
 * 			A common issue with this, is with languages like Java and ActionScript where Boolean variables can't be set to null, 
 * 			thus it is uknown if the variable was modified before constructing the HTTP request to the server. If this is the case with your language, 
 * 			either create a Nullable Boolean type, or keep a map of changed parameters, then only send the variables in that map.
 */
error_reporting(E_ALL);

//the name of the output folder for the generators -
$outputPathBase = 'output';
//the name of the summary file that will be used by the UI -
$summaryFileName = 'summary.kinf';

//bootstrap connects the generator to the rest of Kaltura system
require_once("bootstrap.php");

//pass the name of the generator as the first argument of the command line to
//generate a single library. if this argument is empty, generator will create all libs.
$generateSingle = isset($argv[1]) ? $argv[1] : null;

//pull the generator config ini
$config = new Zend_Config_Ini("config.ini");

//TODO: fix cache mechanism to have an expiry of some kind.
KalturaTypeReflectorCacher::disable();

//if we got specific generator request, tes if this requested generator does exist
if ($generateSingle != null)
{
	$found = false;
	foreach($config as $name => $item)
	{
		if (strtolower($name) === strtolower($generateSingle)) {
			$found = true;
			break;
		}
	}
	if (!$found) throw new Exception("Configuration for [".$generateSingle."] was not found");
}

//get the API version
$apiVersion = KALTURA_API_VERSION;
//get the generation date in string (we'll use that for the client tgz file name)
$generatedDate = date('d-m-Y', time());

// Clear the output folder -
exec ("rm -rf $outputPathBase/*");

$generatedClients = array(
	'generatedDate' => $generatedDate,
	'apiVersion' => $apiVersion,
);

// Loop through the config.ini and generate the client libraries -
foreach($config as $name => $item)
{
	//get the generator class name
	$generator = $item->get("generator");
	
	//check if this client should not be packaged as tar.gz file
	$shouldNotPackage = $item->get("nopackage");

	//check if we should create a folder for this client library files, or directly create files on main output folder
	$mainOutput = $item->get("nofolder");

	//check if this client should be internal or public (on the UI)
	$isInternal = $item->get("internal");
	if ($isInternal === null)
		$generatedClients[] = $name;
	
	// check if generator is valid (not null and there is a class by this name)
	if ($generator === null)
		throw new Exception("No generator for [".$name."]");
	if (!class_exists($generator))
		throw new Exception("Generator [".$generator."] not found");
	
	// when generating a single client, skip the generators not relvant
	if ($generateSingle && strtolower($name) !== strtolower($generateSingle)) 
		continue;
	
	KalturaLog::info("Now generating: $name using $generator");
	
	// get the API list to include in this client generate
	$include = $item->get("include");
	// get the API list to exclude in this client generate
	$exclude = $item->get("exclude");
	// can only do either include or exclude
	if ($include !== null && $exclude !== null)
		throw new Exception("Only include or exclude should be declared");

	// get the list of Objects to include in this client generate	
	$additional = $item->get("additional");
	
	// get the list of Plugins to include in this client generate
	$pluginList = explode(',', $item->get("plugins"));
	
	// include the plugins requested for this package
	foreach($pluginList as $plugin)
	{
		$pluginName = trim($plugin);
		if(!$pluginName) continue;
		KalturaPluginManager::addPlugin($pluginName);
	}
	
	// create the API schema to be used by the generator
	$reflectionClass = new ReflectionClass($generator);
	$fromXml = $reflectionClass->isSubclassOf("ClientGeneratorFromXml");
	$fromPhp = $reflectionClass->isSubclassOf("ClientGeneratorFromPhp");
	
	// if it's an XML schema based generator -
	if ($fromXml)
	{
		KalturaLog::info("Using XmlSchemaGenerator to generate the api schema");
		$xmlGenerator = new XmlClientGenerator();
		$xmlGenerator->setIncludeOrExcludeList($include, $exclude);
		$xmlGenerator->setAdditionalList($additional);
		$xmlGenerator->generate();
		$files = $xmlGenerator->getOutputFiles();
		//save a temp schema to the disk to be used by the xml generator
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
	//if it's a native php based schema generator
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
		throw new Exception("Invalid generator [$generator], can't determine if this is XML or PHP based");
	}
	
	KalturaLog::info("Generate client library [$name]");
	$instance->generate();
	
	if ($mainOutput) 
		$outputPath = $outputPathBase;
	else
		$outputPath = $outputPathBase.DIRECTORY_SEPARATOR.$name;
	KalturaLog::info("Saving client library to [$outputPath]");
	if (realpath($outputPath) === false)
	{
		$oldMask = umask();
		umask(0);
		mkdir($outputPath, 0777, true);
		umask($oldMask);
	}
	$files = $instance->getOutputFiles();
	foreach($files as $file => $data)
	{
		$filePath = realpath($outputPath).DIRECTORY_SEPARATOR.$file;
		$dirName = pathinfo($filePath, PATHINFO_DIRNAME);
		if (!file_exists($dirName))
		{
			$oldMask = umask();
			umask(0);
			mkdir($dirName, 0777, true);
			umask($oldMask);
		}

		file_put_contents($filePath, $data);
	}
	
	//delete the api services xml schema file
	if ($fromXml)
		unlink("temp.xml");
		

	if (count($files) == 0)
	{
		//something went wrong in this generator?
		KalturaLog::info("No output files created [$name]");
	}
	else
	{
		//tar gzip the client library
		if (!$shouldNotPackage) 
			createPackage($outputPath, $name);
	}
		
	KalturaLog::info("$name generated successfully");
}

//write the summary file (will be used by the generator UI)
file_put_contents($outputPathBase.DIRECTORY_SEPARATOR.$summaryFileName, serialize($generatedClients));

/**
 * Build a packaged tarball for the client library.
 * @param $outputPath 		The path the client library files are located at.
 * @param $generatorName	The name of the client library.
 */
function createPackage($outputPath, $generatorName)
{
	global $generatedDate;
	
	KalturaLog::info("Trying to package");
	$output = shell_exec("tar --version");
	if ($output === null)
	{
		KalturaLog::warning("Skipping packaging, \"tar\" command not found! On Windows, tar can be installed using Cygwin, and it should be added to the path");
	}
	else
	{
		$fileName = "{$generatorName}_{$generatedDate}.tar.gz";
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

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
 * 			b. exclude - whether to exclude any specific API services from the client library (ignored if include defined)
 * 			c. include - whether to include any specific API services from the client library (overrides exclude)
 * 			d. plugins - whether to include any specific API services from plugins
 * 			e. additional - whether to include any additional objects not directly defined through API services
 * 			f. internal - whether to show this client in the Client Libraries UI in the testme console, or not. 
 * 							note that setting schemaxml, will also make the client internal
 * 			g. nopackage - whether to generate a tar.gz package from the client library folder 
 * 			h. nofolder - there will not be client folder, the client files will be in output folder (if it's a single file like XML schema) 
 * 			i. ignore - whether to ignore any objects although defined through API services by inheritance
 * 			j. schemaxml - if empty, will introspect the code and create the schema XML, 
 * 							otherwise this should be a url to download schema XML from. Setting this will make the client internal
 * 
 * Notes:
 * 		* Kaltura API ignores only un-sent parameters. Thus, if you would like a parameter value to be left unchanged
 * 			or in classes that contain read-only parameters, make sure to NOT send any un-changed parameters in your HTTP requests.
 * 			A common issue with this, is with languages like Java and ActionScript where Boolean variables can't be set to null, 
 * 			thus it is uknown if the variable was modified before constructing the HTTP request to the server. If this is the case with your language, 
 * 			either create a Nullable Boolean type, or keep a map of changed parameters, then only send the variables in that map.
 */
error_reporting(E_ALL);
ini_set( "memory_limit","512M" );

//the name of the output folder for the generators -

chdir(__DIR__);

//bootstrap connects the generator to the rest of Kaltura system
require_once(dirname(__FILE__) . "/bootstrap.php");

//the name of the summary file that will be used by the UI -
$summaryFileName = 'summary.kinf';
$tmpXmlFileName = tempnam(sys_get_temp_dir(), 'kaltura.generator.');

//pass the name of the generator as the first argument of the command line to
//generate a single library. if this argument is empty or 'all', generator will create all libs.
$generateSingle = isset($argv[1]) ? $argv[1] : null;

//second command line argument specifies the output path, if not specified will default to 
//<content root>/content/clientlibs
if (isset($argv[2]))
{
	$outputPathBase = $argv[2];
}
else
{
	$root = myContentStorage::getFSContentRootPath();
	$outputPathBase = "$root/content/clientlibs";
}

kFile::fullMkdir($outputPathBase);

//pull the generator config ini
$config = new Zend_Config_Ini(__DIR__ . '/../configurations/generator.ini', null, array('allowModifications' => true));
$config = KalturaPluginManager::mergeConfigs($config, 'generator', false);

$libsToGenerate = null;
if (strtolower($generateSingle) == 'all')
{
	$generateSingle = null;
}
elseif(!$generateSingle)
{
	$libsToGenerate = file(__DIR__ . '/../configurations/generator.defaults.ini');
	foreach($libsToGenerate as $key => &$default)
		$default = strtolower(trim($default, " \t\r\n"));
}

//if we got specific generator request, tes if this requested generator does exist
if ($generateSingle != null)
{
	$libsToGenerate = array_map('strtolower', array_intersect(explode(',', $generateSingle), array_keys($config->toArray())));
}

//get the API version
$apiVersion = KALTURA_API_VERSION;
//get the generation date in string (we'll use that for the client tgz file name)
$generatedDate = date('d-m-Y', time());
$schemaGenDateOverride = null;

$generatedClients = array(
	'generatedDate' => $generatedDate,
	'apiVersion' => $apiVersion,
);

// Loop through the config.ini and generate the client libraries -
foreach($config as $name => $item)
{
	// check if we need to introspect code to create schema or use the ready schema from a given url
	$useReadySchema = $item->get("schemaxml");
	
	//get the generator class name
	$generator = $item->get("generator");
	
	//check if this client should not be packaged as tar.gz file
	$shouldNotPackage = $item->get("nopackage");

	//check if we should create a folder for this client library files, or directly create files on main output folder
	$mainOutput = $item->get("nofolder");

	// check if generator is valid (not null and there is a class by this name)
	if ($generator === null)
		continue;
	if (!class_exists($generator))
		throw new Exception("Generator [".$generator."] not found");
	
	if($libsToGenerate && !in_array(strtolower($name), $libsToGenerate))
		continue;

	//check if this client should be internal or public (on the UI)
	$isInternal = $item->get("internal");
	
	if ($isInternal === null || ($useReadySchema != null && $useReadySchema != ''))
	{
		$params = array(
			'linkhref' => $item->get('linkhref'),
			'linktext' => $item->get('linktext'));
		$generatedClients[$name] = $params;
	}
	
	KalturaLog::info("Now generating: $name using $generator");
	
	// get the API list to include in this client generate
	$include = $item->get("include");
	$exclude = $item->get("exclude");
	$excludePaths = $item->get("excludepaths");
	// can only do either include or exclude
	if ($include !== null)
		$exclude = null;

	// get the list of Objects to include in this client generate	
	$additional = $item->get("additional");

	// get the list of Objects to ignore	
	$ignore = $item->get("ignore");
	
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
		if ($useReadySchema == null || $useReadySchema == '')
		{
			KalturaLog::info("Using code introspection to generate XML schema");
			$xmlGenerator = new XmlClientGenerator();
			$xmlGenerator->setIncludeOrExcludeList($include, $exclude, $excludePaths);
			$xmlGenerator->setIgnoreList($ignore);
			$xmlGenerator->setAdditionalList($additional);
			$xmlGenerator->generate();

			$files = $xmlGenerator->getOutputFiles();
			//save a temp schema to the disk to be used by the xml generator
			file_put_contents($tmpXmlFileName, $files["KalturaClient.xml"]);
		} else {
			KalturaLog::info("Downloading ready-made schema from: ".$useReadySchema);
			$contents = file_get_contents($useReadySchema);
			file_put_contents($tmpXmlFileName, $contents);
			//Get the schema version and last generated date -
			$schemaXml = new SimpleXMLElement(file_get_contents($tmpXmlFileName));
			$apiVersionOverride = $schemaXml->attributes()->apiVersion;
			$schemaGenDate = (int)$schemaXml->attributes()->generatedDate;
			$schemaGenDateOverride = date('d-m-Y', $schemaGenDate);
			KalturaLog::info('Generating from api version: '.$apiVersionOverride.', generated at: '.strftime("%a %d %b %H:%M:%S %Y", $schemaGenDate));
		}
		
		$instance = $reflectionClass->newInstance($tmpXmlFileName, $item);
		
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
		
		if (isset ($item->excludeSourcePaths))
		{
			$instance->setExcludeSourcePaths ($item->excludeSourcePaths);
		}
	}
	//if it's a native php based schema generator
	else if ($fromPhp)
	{
		$instance = $reflectionClass->newInstance();
		$instance->setIncludeOrExcludeList($include, $exclude, $excludePaths);
		$instance->setIgnoreList($ignore);
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
	
	$copyPath = null;
	if($item->get("copyPath"))
		$copyPath = KALTURA_ROOT_PATH . '/' . $item->get("copyPath");
	
	if ($mainOutput)
	{ 
		$outputPath = $outputPathBase;
	}
	else
	{
		$outputPath = "$outputPathBase/$name";
		$clearPath = null;
		if($item->get("clearPath"))
			$clearPath = KALTURA_ROOT_PATH . '/' . $item->get("clearPath");
		else
			$clearPath = $copyPath;
		
		if(!file_exists($clearPath))
			$clearPath = null;
			
		if($clearPath || file_exists($outputPath))
		{
			if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
			{
				$winOutputPath = realpath($outputPath);
				KalturaLog::info("Delete old files [$winOutputPath" . ($clearPath ? ", $clearPath" : "") . "]");
				passthru("rmdir /Q /S $winOutputPath $clearPath");
			}
			else
			{
				KalturaLog::info("Delete old files [$outputPath" . ($clearPath ? ", $clearPath" : "") . "]");
				passthru("rm -fr $outputPath $clearPath");
			}
		}
	}
		
	KalturaLog::info("Generate client library [$name]");
	$instance->generate();
	
	KalturaLog::info("Saving client library to [$outputPath]");
	
	$oldMask = umask();
	umask(0);
		
	$files = $instance->getOutputFiles();
	foreach($files as $file => $data)
	{
		$file = str_replace(array('/', '\\'), array(DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR), $file);
		$filePath = $outputPath . DIRECTORY_SEPARATOR . $file;
		$dirName = dirname($filePath);
		if (!file_exists($dirName))
			mkdir($dirName, 0777, true);

		file_put_contents($filePath, $data);
		
		if($copyPath)
		{
			$copyFilePath = $copyPath . DIRECTORY_SEPARATOR . $file;
			$dirName = dirname($copyFilePath);
			if (!file_exists($dirName))
				mkdir($dirName, 0777, true);
				
			copy($filePath, $copyFilePath);
		}

		if ($file == "KalturaClient.xml")
		{
			# save the schema also in a filename containing the generation date
			# KalturaClient.xml will always contain the most recent schema so that it can be served by api_schema.php
			$filePath = "$outputPath/KalturaClient_$generatedDate.xml";
			file_put_contents($filePath, $data);
		}
	}
	umask($oldMask);
	
	//delete the api services xml schema file
	if ($fromXml && file_exists($tmpXmlFileName))
		unlink($tmpXmlFileName);

	if (count($files) == 0)
	{
		//something went wrong in this generator?
		KalturaLog::info("No output files created [$name]");
	}
	else
	{
		//tar gzip the client library
		if (!$shouldNotPackage) 
			createPackage($outputPath, $name, $generatedDate, $schemaGenDateOverride);
	}
		
	KalturaLog::info("$name generated successfully");
}

//write the summary file (will be used by the generator UI)
file_put_contents($outputPathBase."/".$summaryFileName, serialize($generatedClients));

exit(0);


/**
 * Build a packaged tarball for the client library.
 * @param $outputPath 		The path the client library files are located at.
 * @param $generatorName	The name of the client library.
 */
function createPackage($outputPath, $generatorName, $generatedDate, $overrideGenDate = null)
{
	KalturaLog::info("Trying to package");
	$output = shell_exec("tar --version");
	if ($output === null)
	{
		KalturaLog::warning("Skipping packaging, \"tar\" command not found! On Windows, tar can be installed using Cygwin, and it should be added to the path");
	}
	else
	{
		if ($overrideGenDate == null) $overrideGenDate = $generatedDate;
		$fileName = "{$generatorName}_{$overrideGenDate}.tar.gz";
		$gzipOutputPath = "../".$fileName;
		$cmd = "tar -czf \"$gzipOutputPath\" ../".$generatorName;
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

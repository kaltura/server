<?php


/**
 * for running the script you need to provide path to ini file like:
 *  --ini=/path/to/config.ini
 *  
 * to get example code for kmc wrapper add:
 *  --include-code
 *
 * to dry-run the script add
 *  --no-create
 */
ini_set("memory_limit", "512M");
error_reporting(E_ALL);
$code = array();
$uiConfIds = array();

//$argv = array( 1=> "--ini=c:/uiConf/config.ini", 2 => "--no-create"); //used to teswt inside the zend studio

$arguments = uiConfDeployment::setArguments($argv);

$includeCode = $arguments['include-code'];
$skipAddUiconf = $arguments['no-create'];

uiConfDeployment::checkArguments($arguments);

//error_reporting(0);
$confObj = uiConfDeployment::init($arguments['ini']); // get and read the config file

uiConfDeployment::$baseTag = $confObj->general->component->name; // gets the application name for the default tags 
uiConfDeployment::$defaultTags = "autodeploy, ". uiConfDeployment::$baseTag . "_" . $confObj->general->component->version; // create the uiConf default tags (for ui confs of the application)

if($includeCode)
{
	$code = uiConfDeploymentCodeGenerator::generateCode();
}

//deploy all the ui confs
uiConfDeployment::deploy($confObj);

if($includeCode)
{
	foreach(uiConfDeployment::$tags_search as $tag)
	{
		$code[] = '$this->'.uiConfDeployment::$baseTag.'_uiconfs_'.$tag.' = $this->'.uiConfDeploymentCodeGenerator::SEARCH_BY_TAG_FUNCTION_NAME.'("'.uiConfDeployment::$baseTag.'_'.$tag.'");';
	}
	
	echo PHP_EOL.'// code for KMC wrapper'.PHP_EOL;
	$code[] = uiConfDeploymentCodeGenerator::addSearchConfByTag();
	echo implode(PHP_EOL, $code);
}

exit(0);

/**
 * 
 * Used to deploy the ui confs
 * @author Roni
 *
 */
class uiConfDeployment
{
	/**
	 * 
	 * The default tags for the ui confs
	 * @var string
	 */	
	public static $defaultTags = '';
	
	/**
	 * 
	 * The base tag for the config file
	 * @var string
	 */
	public static $baseTag = "";
	
	/**
	 * 
	 * The arguments for the uiConf deployment
	 * @var unknown_type
	 */
	public static $arguments = array();
	
	/**
	 * 
	 * The tag search array
	 * @var array<>
	 */
	public static $tags_search = array();
	
	/**
	 * 
	 * the partner id for the ui conf deployment (currentlly defaulted to 0)
	 * @var int
	 */
	public static $partnerId = 0;
	
	/**
	 * 
	 * the $subPartnerId for the ui conf deployment (currentlly defaulted to 0)
	 * @var int
	 */
	public static $subPartnerId = 0;
	
	/**
	 * 
	 * the creation mode for the ui conf deployment (currentlly defaulted to 3)
	 * @var int
	 */
	public static $creationMode = 3;
		
	/**
	 * 
	 * the use cdn for the ui conf deployment (currentlly defaulted to 1)
	 * @var int
	 */
	public static $useCdn = 1;
			
	/**
	 * 
	 * deploys the ui conf from the ini file
	 * @param Zend_Config_Ini $confObj
	 */
	public static function deploy(Zend_Config_Ini $confObj)
	{
		//Main Algorithm
		//Here we need to run on the entire config file
		//1. For each section in the config
		//	1.1. Fill all data like swf name, swf url and identifier.
		//	1.2. Foreach widget in this section
		//		1.2.1. Create the uiConf from the xml
		//		1.2.2. Foreach dependencies it has
		//			1.2.2.1. Find the uiCoinf id for this dependency and insert it in the right place
		//			1.2.2.2. save the new ui conf

		//Iterate through all sections (statics, general, kmc, kcw...)
		foreach ($confObj as $sectionName=> $sectionValue)
		{
			//if we are in the widgets section (like kmc, kcw, kse)
			if($sectionName != "general" && count($sectionValue->widgets))
			{
				//Set section values
				$baseSwfUrl = $sectionValue->swfPath;
				$swfName= $sectionValue->swfName;
				$objectType= $sectionValue->objectType;
				
				//For each widget (in the section)
				foreach ($sectionValue->widgets as $widgetName => $widgetValue)
				{
					//Set widget values
					uiConfDeployment::$tags_search[$widgetValue->usage] = $widgetValue->usage;
					$widgetIdentifier = $widgetValue->identifier;
					
					echo "creating uiconfs for widget $widgetName with default values ( $baseSwfUrl , $swfName , $objectType )" . PHP_EOL;
					echo "$widgetName , $baseSwfUrl , $swfName , $objectType".PHP_EOL;
					
					//Create the ui conf from the xml
					$uiConf = uiConfDeployment::populateUiconfFromConfig($widgetValue, $baseSwfUrl, $swfName, $objectType, uiConfDeployment::$arguments['disableUrlHashing']);
				
					if($uiConf) //if the ui conf was generated successfully 
					{
						//then we need to insert the ui conf to the DB (so we can get his id)
						$uiconf_id = uiConfDeployment::addUiConfThroughPropel($uiConf);
						
						if(isset($widgetValue->features))
						{
							uiConfDeployment::updateFeaturesFile($uiConf, $uiconf_id, $widgetValue->features_identifier);
						}
						
						//Add this id to the dependencies data array
						$uiConfIds[$widgetIdentifier] = $uiconf_id;

						//Then update him with the dependencies
						foreach($widgetValue->dependencies as $dependencyName => $dependencyValue)
						{
							if(isset($uiConfIds[$dependencyValue])) // if the ui conf id was set already then we can set the dependencies
							{
								$dependUiConfValue = $uiConfIds[$dependencyValue];
								
								uiConfDeployment::updateUIConfFile($uiConf, $dependUiConfValue, "@@{$dependencyValue}@@"); // set new value instead of the dependency
							}
							else
							{ 
								echo "Missing dependency: {$dependencyName} = {$dependencyValue} for widget: {$widgetName}" . PHP_EOL;
							}
						}
						
						
					}
					else
					{
						echo "failed to create uiconf object ($widgetName) due to missing values. check your config.ini".PHP_EOL;
					}
					
					echo PHP_EOL;
				}
			}
		}
	}
	
	/**
	 * 
	 * Sets the value to object if the value is not empty / null
	 * @param string $value
	 * @param string $object
	 */
	public static function setValueIfExists($value, &$object)
	{
		if($value)
		{
			$object = $value;
		}
	}
	
	/**
	 * 
	 * Used to initialize the ui conf deployment like a bootstarp fiel
	 * @param unknown_type $conf_file_path
	 */
	public static function init($conf_file_path)
	{
		require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."infra".DIRECTORY_SEPARATOR."bootstrap_base.php");
		require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."alpha".DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR."kConf.php");
		define("KALTURA_API_PATH", KALTURA_ROOT_PATH.DIRECTORY_SEPARATOR."api_v3");
		
		// Autoloader
		require_once(KALTURA_INFRA_PATH.DIRECTORY_SEPARATOR."KAutoloader.php");
		KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
		KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_API_PATH, "lib", "*"));
		KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_API_PATH, "services", "*"));
		KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "alpha", "plugins", "*")); // needed for testmeDoc
		KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "*"));
		KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "generator")); // needed for testmeDoc
		KAutoloader::setClassMapFilePath(kConf::get("cache_root_path") . '/deploy/classMap.cache');
		//KAutoloader::dumpExtra();
		KAutoloader::register();
		
		$dbConf = kConf::getDB();
		DbManager::setConfig($dbConf);
		DbManager::initialize();		
		
		date_default_timezone_set(kConf::get("date_default_timezone"));
		
//		try
//		{
			$confObj = new Zend_Config_Ini($conf_file_path);	
//		}
//		catch(Exception $ex)
//		{
//			echo 'Exiting on ERROR: '.$ex->getMessage().PHP_EOL;
//			exit(1);
//		}		
		return $confObj;
 	}
	
	/**
	 * 
	 * Reads the config file from the given path
	 * @param string $file_path
	 */
	public static function readConfFileFromPath($file_path)
	{
		global $arguments;
		
		if(!file_exists($file_path)) {
			if(!file_exists(dirname($arguments['ini'])))
			{
				return FALSE;
			}
			else
			{
				$file_path = dirname($arguments['ini']).DIRECTORY_SEPARATOR.$file_path;
			}
		}
		
		$file_content = file_get_contents($file_path);
		return $file_content;
	}
	
	/**
	 * 
	 * Not in use!
	 * @param int $num
	 */
	private function NOT_IN_USE_getUiconfObjtypeConstFromNumber($num)
	{
		$reflectionClass = new ReflectionClass('uiConf');
		$allConsts = $reflectionClass->getConstants();
		$consts = array();
		foreach($allConsts as $key => $value)
		{
			if(strpos($key, 'UI_CONF_TYPE') !== false && $value == $num)
				return $key;
		}
	}
		
	/**
	 * 
	 * Add a new uiConf to the DB using propel
	 * @param uiConf $pe_conf
	 */
	public static function addUiConfThroughPropel(uiConf $pe_conf)
	{
		global $skipAddUiconf;
		if($skipAddUiconf) return rand(1000,1200); // return just any number if the no-create flag is on 
		
//		try
//		{
			$pe_conf->save();
			
			// chmod parent directory to 777 to allow changes by the apache user
			$sync_key = $pe_conf->getSyncKey(uiConf::FILE_SYNC_UICONF_SUB_TYPE_DATA);
			$localPath = kFileSyncUtils::getLocalFilePathForKey($sync_key);
			@system('chmod 777 -R '.dirname($localPath));
//		}
//		catch(Exception $ex)
//		{
//			echo 'Exiting on ERROR: '.$ex->getMessage().PHP_EOL;
//			exit(1);
//		}

		return $pe_conf->getId();
	}
	
	/**
	 * 
	 * Populate the uiconf from the config
	 * @param Zend_Config_Ini $widget
	 * @param string $baseSwfUrl
	 * @param string $swfName
	 * @param int $objType
	 * @param bool $disableUrlHashing
	 */
	public static function populateUiconfFromConfig($widget, $baseSwfUrl, $swfName, $objType, $disableUrlHashing)
	{
		$uiconf = new uiConf();
		
		$confFileContents = uiConfDeployment::readConfFileFromPath($widget->conf_file);
		
		if(!$confFileContents)
		{
			echo "Unable to read xml file from: {$widget->conf_file}" . PHP_EOL;
		}
		
		if ($disableUrlHashing)
		{
			$confFileContents = str_replace('<Plugin id="kalturaMix"','<Plugin id="kalturaMix" disableUrlHashing="true" ',$confFileContents);
		}
		
		$uiconf->setConfFile($confFileContents);
		
		if($uiconf->getConfFile() === FALSE)
		{
			return FALSE; // conf file is a must, features is not.
		}
		
		if(isset($widget->features))
		{
			$uiconf->setConfFileFeatures(uiConfDeployment::readConfFileFromPath($widget->features));
		}
		
		if($uiconf->getConfFileFeatures() === FALSE) 
		{
			echo "missing features conf file for uiconf {$widget->name}".PHP_EOL; // conf file is a must, features is not.
		}
		
		//Set values to the ui conf 
		$uiconf->setPartnerId(uiConfDeployment::$partnerId);
		$uiconf->setSubpId(uiConfDeployment::$subPartnerId);
		$uiconf->setCreationMode(uiConfDeployment::$creationMode);
		$uiconf->setUseCdn(uiConfDeployment::$useCdn);
		$uiconf->setObjType($objType);
		
		$uiconf->setName($widget->name);
		$uiconf->setSwfUrl($baseSwfUrl.$widget->version.'/'.$swfName);
		$uiconf->setTags(uiConfDeployment::$defaultTags.', '.uiConfDeployment::$baseTag.'_'.$widget->usage);
		
		$uiconf->setWidth(@$widget->width);
		$uiconf->setHeight(@$widget->height);
		$uiconf->setConfVars(@$widget->conf_vars);

		$uiconf->setDisplayInSearch(mySearchUtils::DISPLAY_IN_SEARCH_KALTURA_NETWORK);
			
		return $uiconf;
	}
	
	/**
	 * 
	 * Replaces in the uiConf the given replacmentString with the newValue and saves the changes in the UI conf
	 * @param uiConf $uiconf
	 * @param string $newValue
	 * @param string $replacementString
	 */
	public static function updateUIConfFile(uiConf $uiconf, $newValue, $replacementString)
	{
		$conf_file = $uiconf->getConfFile(true);
		$conf_file = str_replace($replacementString, $newValue, $conf_file);
		
		$uiconf->setConfFile($conf_file);
		$uiconf->save();
	}

	/**
	 * 
	 * Updates the player id in the features file
	 * @param uiConf $uiconf
	 * @param string $uiconfId
	 * @param string $replacementString
	 */
	public static function updateFeaturesFile(uiConf $uiconf, $uiconfId, $replacementString)
	{
		$conf_file = $uiconf->getConfFile(true);
		$featuresFile = $uiconf->getConfFileFeatures(true);
		$newFeatures = str_replace($replacementString, $uiconfId, $featuresFile);
		$uiconf->setConfFile($conf_file);
		$uiconf->setConfFileFeatures($newFeatures);
		$uiconf->save();
	}
	
	/**
	 * 
	 * Prints the usage info for this script
	 * @param unknown_type $message
	 */
	public static function printUsage($message)
	{
		echo $message.PHP_EOL.PHP_EOL;
		echo 'php '.$_SERVER['SCRIPT_NAME']." --ini={path to ini file} [--no-create]\n\n";
		echo "    --ini: path to ui_conf deployment ini file\n";
		echo "    --include-code: path to ui_conf deployment ini file\n";
		echo "    --no-create: dry-run, do not really create the uiconfs\n";
		die;
	}

	/**
	 * 
	 * Gets the command line arguments and returns the arguments array
	 * @param array $argv
	 * @return array<> $arguments
	 */
	public static function setArguments(array $argv)
	{
		/** init arguments **/
		uiConfDeployment::$arguments = array();
		$arguments['include-code'] = false;
		$arguments['no-create'] = false;
		$arguments['ini']     = '';
		$arguments['disableUrlHashing'] = false;
	
		/** get inputs from arguments **/
		foreach($argv as $num => $value)
		{
			if($num == 0) continue;
			
			if(strpos($value, '--') === false) { uiConfDeployment::printUsage('wrong argument '.$value); }
			
		 	$arg_pair = explode('=', str_replace('--','',$value));
			$arg_name = $arg_pair[0];
			$arg_value = @$arg_pair[1];
			
			if(!isset($arguments[$arg_name])) { uiConfDeployment::printUsage('unknown argument '.$arg_name); }
			
			if(is_null($arg_value)) $arg_value = true;
			$arguments[$arg_name] = $arg_value;
		}
		
		uiConfDeployment::$arguments = $arguments;
		
		return $arguments;
	}
	
	/**
	 * 
	 * Checks that the argument are valid
	 * @param array $arguments
	 */
	public static function checkArguments(array $arguments)
	{
		//Checks if the ini argument was given
		if(!isset($arguments['ini']) || !($arguments['ini']) || is_null($arguments['ini'])) 
		{ 
			uiConfDeployment::printUsage('missing argument --ini'); 
		}
		
		//Check if ini file exists
		if(!file_exists($arguments['ini'])) 
		{ 
			uiConfDeployment::printUsage('config file not found '.$arguments['ini']); 
		}
	}
}

/**
 * 
 * Used to generate the ui conf deployment code
 * @author Roni
 *
 */
class uiConfDeploymentCodeGenerator
{
	const SEARCH_BY_TAG_FUNCTION_NAME = 'find_confs_by_usage_tag';
	
	/**
	 * 
	 * Generates the code for the code samples
	 * @return array<string>
	 */
	public static function generateCode()
	{
		$code[] = '$c = new Criteria();';
		$code[] = '$c->addAnd(UiConfPeer::PARTNER_ID, '.uiConfDeployment::$partnerId.');';
		$code[] = '$c->addAnd(UiConfPeer::TAGS, "%'.uiConfDeployment::$baseTag.'_".$this->kmc_'.uiConfDeployment::$baseTag.'_version."%", Criteria::LIKE);';
		$code[] = '$c->addAnd(UiConfPeer::TAGS, "%autodeploy%", Criteria::LIKE);';
		$code[] = '$this->confs = UiConfPeer::doSelect($c);';
		return $code;
	}
	
	public static function addSearchConfByTag()
	{
		$code[] = '';
		$code[] = 'function '.self::SEARCH_BY_TAG_FUNCTION_NAME.'($tag)';
		$code[] = '{';
		$code[] = '  $uiconfs = array();';
		$code[] = '  foreach($this->confs as $uiconf)';
		$code[] = '  {';
		$code[] = '    $tags = explode(",", $uiconf->getTags());';
		$code[] = '    $trimmed_tags = $this->TrimArray($tags);';
		$code[] = '    if(in_array($tag, $trimmed_tags)) $uiconfs[] = $uiconf;';
		$code[] = '  }';
		$code[] = '';
		$code[] = '  return $uiconfs;';
		$code[] = '}';
		
		$code[] = '';
		$code[] = 'function TrimArray($arr){';
		$code[] = '  if (!is_array($arr)){ return $arr; }';
		$code[] = '';
		$code[] = '  while (list($key, $value) = each($arr)){';
		$code[] = '    if (is_array($value)){';
		$code[] = '      $arr[$key] = TrimArray($value);';
		$code[] = '    }';
		$code[] = '    else {';
		$code[] = '      $arr[$key] = trim($value);';
		$code[] = '    }';
		$code[] = '  }';
		$code[] = '  return $arr;';
		$code[] = '}';
		
		$codeStr = implode(PHP_EOL, $code);
		return $codeStr;
	}
}

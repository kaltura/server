<?php
require_once (__DIR__ . "/../bootstrap.php");


/**
 * for running the script you need to provide path to ini file like:
 *  --ini={/path/to/config.ini}
 *
 * to get example code for kmc wrapper add:
 *  --include-code
 *
 * to dry-run the script add
 *  --no-create
 *
 * to define user (defaults to apache) or group (defaults to kaltura) for files ownership add
 *  --user={username}
 *  --group={groupname}
 */
ini_set("memory_limit", "512M");
error_reporting(E_ALL);
$code = array();
$uiConfIds = array();
$tokenValues = array();

//$argv = array( 1=> "--ini=c:/web/flash/kmc/v4.0.4/config.ini", 2 => "--no-create"); //used to teswt inside the zend studio

$arguments = uiConfDeployment::setArguments($argv);

$includeCode = $arguments['include-code'];
$skipAddUiconf = $arguments['no-create'];

//error_reporting(0);
$confObj = uiConfDeployment::init($arguments['ini']); // get and read the config file

uiConfDeployment::checkArguments($arguments);

uiConfDeployment::$baseTag = $confObj->general->component->name; // gets the application name for the default tags
uiConfDeployment::$defaultTags = "autodeploy, ". uiConfDeployment::$baseTag . "_" . $confObj->general->component->version; // create the uiConf default tags (for ui confs of the application)
uiConfDeployment::$partnerId = $arguments['partner'];

if($includeCode)
{
	$code = uiConfDeploymentCodeGenerator::generateCode();
}

uiConfDeployment::deprecateOldUiConfs(uiConfDeployment::$defaultTags);

//deploy all the ui confs
uiConfDeployment::deploy($confObj);

uiConfDeployment::setTemplatePartner();


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

echo "Deployed successfully\n";
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
	 * the partner for the ui conf deployment (currentlly defaulted to null)
	 * @var int
	 */
	public static $partner = null;
	
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
			//If we are in the widgets section (like kmc, kcw, kse)
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
					
					//Create the ui conf from the xml
					$uiConf = uiConfDeployment::populateUiconfFromConfig($widgetValue, $baseSwfUrl, $swfName, $objectType, uiConfDeployment::$arguments['disableUrlHashing']);
												
					if($uiConf) //If the ui conf was generated successfully
					{
						//Then we need to insert the ui conf to the DB (so we can get his id)
						$uiconf_id = uiConfDeployment::addUiConfThroughPropel($uiConf);

						KalturaLog::debug("creating uiconf [$uiconf_id] for widget $widgetName with default values ( $baseSwfUrl , $swfName , $objectType ) for partner " . self::$partnerId);
						KalturaLog::debug("$widgetName , $baseSwfUrl , $swfName , $objectType");
					
						if(isset($widgetValue->features))
						{
							uiConfDeployment::updateFeaturesFile($uiConf, $uiconf_id, $widgetValue->features_identifier);
						}
						
						//Add this id to the dependencies data array
						$uiConfIds[$widgetIdentifier] = $uiconf_id;
						
						//If the widget has dependencies
						if(isset($widgetValue->dependencies))
						{
							//Then update him with the dependencies
							foreach($widgetValue->dependencies as $dependencyName => $dependencyValue)
							{
								if(isset($uiConfIds[$dependencyValue])) // If the ui conf id was set already then we can set the dependencies
								{
									$dependUiConfValue = $uiConfIds[$dependencyValue];
									
									uiConfDeployment::updateUIConfFile($uiConf, $dependUiConfValue, "@@{$dependencyValue}@@"); // set new value instead of the dependency
									uiConfDeployment::updateFeaturesFile($uiConf, $dependUiConfValue, "@@{$dependencyValue}@@");
								}
								else
								{
									uiConfDeployment::updateFeaturesFile($uiConf, $dependencyValue, "@@{$dependencyName}@@");
									KalturaLog::debug("Missing dependency: {$dependencyName} = {$dependencyValue} for widget: {$widgetName}. Attempting to replace the token in uiconf features file");
								}
							}
						}
						
					}
					else
					{
						KalturaLog::debug("failed to create uiconf object ($widgetName) due to missing values. check your config.ini");
					}
				}
			}
		}
	}
	
	/**
	 *
	 * Deprectes old ui confs which have the same Tags.
	 * it replaces their tag from autodeploy to deprecated
	 * @param string $tag - the tag to depracate
	 */
	public static function deprecateOldUiConfs($tag)
	{
		//Selects all the ui confs with the given $newTag
		$con = Propel::getConnection();
		$oldConfCriteria = new Criteria();
		$oldConfCriteria->add(uiConfPeer::TAGS, "%{$tag}%", Criteria::LIKE);
		$oldConfCriteria->add(uiConfPeer::PARTNER_ID, self::$partnerId, Criteria::EQUAL);
		$oldConfCriteria->addSelectColumn(uiConfPeer::ID);
		$oldConfCriteria->addSelectColumn(uiConfPeer::TAGS);
		
		//Select ID, tags from ui_conf where tags like %$newTag%;
		$uiConfs = BasePeer::doSelect($oldConfCriteria, $con);

		$totalDepractedCount = 0;
		
		//For each uiconf:
		foreach ($uiConfs as $oldUiConf)
		{
			$newTag = $oldUiConf[1];
			$deprecatedTag = $newTag;
			$deprecatedTag = str_replace("autodeploy", "deprecated", $deprecatedTag);
		
			KalturaLog::debug("newTag is:         {$newTag} \nDeprecatedTag is : {$deprecatedTag} for partner ". self::$partnerId);
			
			$confCriteria = new Criteria();
			$confCriteria->add(uiConfPeer::ID, $oldUiConf[0]);
			
			$deprecatedConfValues = new Criteria();
			$deprecatedConfValues->add(uiConfPeer::TAGS, $deprecatedTag);
			
			//Update set tags = $deprecatedTag where ID = $oldUiConf->ID
			$deprecatedCount = BasePeer::doUpdate($oldConfCriteria, $deprecatedConfValues, $con);
			
			KalturaLog::debug("uiConf number {$oldUiConf[0]} was updated with the tag = {$deprecatedTag}");
			
			$totalDepractedCount += $deprecatedCount;
		}
		
		//Add the status check to the select factor
		KalturaLog::debug("{$totalDepractedCount} uiConfs were updated");

		$count = uiConfPeer::doCount($oldConfCriteria);
		
		if($count > 0)
		{
			KalturaLog::debug("Exiting, Tag: {$newTag} already found in the DB");
			exit;
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
		return new Zend_Config_Ini($conf_file_path);
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
	 * @param $sync_key
	 */
	private static function setPermissionAndOwner($sync_key)
	{
		$localPath = kFileSyncUtils::getLocalFilePathForKey($sync_key);
		$localPath = str_replace(array('/', '\\'), array(DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR), $localPath);

		$ret = null;
		chmod($localPath, 0640);

		if (strtoupper(substr(PHP_OS, 0, 3)) != 'WIN')
		{
			$user_group = uiConfDeployment::$arguments['user'] . ':' . uiConfDeployment::$arguments['group'];
			passthru("chown $user_group $localPath", $ret);
			if ($ret !== 0 && $ret !== 127)
			{
				KalturaLog::debug("chown [$user_group] failed on path [$localPath] returned value [$ret]");
				exit(1);
			}
		}
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
		if ($skipAddUiconf) return rand(1000, 1200); // return just any number if the no-create flag is on

		$pe_conf->save();

		if ($pe_conf->getConfFile())
		{
			$sync_key = $pe_conf->getSyncKey(uiConf::FILE_SYNC_UICONF_SUB_TYPE_DATA);
			self::setPermissionAndOwner($sync_key);
		}

		if ($pe_conf->getConfig())
		{
			$sync_key = $pe_conf->getSyncKey(uiConf::FILE_SYNC_UICONF_SUB_TYPE_CONFIG);
			self::setPermissionAndOwner($sync_key);
		}

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
		
		if($widget->conf_file)
		{
			$confFileContents = uiConfDeployment::readConfFileFromPath($widget->conf_file);
		
			if(!$confFileContents)
			{
				KalturaLog::debug("Unable to read xml file from: {$widget->conf_file}");
			}
		
			if ($disableUrlHashing)
			{
				$confFileContents = str_replace('<Plugin id="kalturaMix"','<Plugin id="kalturaMix" disableUrlHashing="true" ',$confFileContents);
			}
		
			$uiconf->setConfFile($confFileContents);
		
		}
		
		if ($widget->config)
			$uiconf->setConfig(@$widget->config);
		else if ($widget->config_file) {
			$configFileContents = uiConfDeployment::readConfFileFromPath($widget->config_file);
		
			if(!$configFileContents)
			{
				KalturaLog::debug("Unable to read json file from: {$widget->config_file}");
			}
		
			$uiconf->setConfig($configFileContents);
		}
		
		
		if($uiconf->getConfFile() === FALSE && $uiconf->getConfig() === FALSE)
		{
			return FALSE; // conf file or config is a must, features is not.
		}		
		
		if(isset($widget->features))
		{
			$uiconf->setConfFileFeatures(uiConfDeployment::readConfFileFromPath($widget->features));
		}
		
		if($uiconf->getConfFileFeatures() === FALSE)
		{
			KalturaLog::debug("missing features conf file for uiconf {$widget->name}"); // conf file is a must, features is not.
		}
		
		//Set values to the ui conf
		$uiconf->setPartnerId(uiConfDeployment::$partnerId);
		$uiconf->setSubpId(uiConfDeployment::$subPartnerId);
		$uiconf->setCreationMode(uiConfDeployment::$creationMode);
		$uiconf->setUseCdn(uiConfDeployment::$useCdn);
		$uiconf->setObjType($objType);
		
		$uiconf->setName($widget->name);
		$uiconf->setSwfUrl($baseSwfUrl.$widget->version.'/'.$swfName);

		if ($widget->html5_version)
			$uiconf->setHtml5Url("/html5/html5lib/".$widget->html5_version."/mwEmbedLoader.php");

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
		
		$sync_key = $uiconf->getSyncKey(uiConf::FILE_SYNC_UICONF_SUB_TYPE_DATA);
		$localPath = kFileSyncUtils::getLocalFilePathForKey($sync_key);
		$localPath = str_replace(array('/', '\\'), array(DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR), $localPath);
	
		chmod($localPath, 0640);
	
		if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN')
			return;
	
		$user_group = uiConfDeployment::$arguments['user'] . ':' . uiConfDeployment::$arguments['group'];
		passthru("chown $user_group $localPath", $ret);
		if($ret !== 0)
		{
			KalturaLog::debug("chown [$user_group] failed on path [$localPath]");
			exit(1);
		}
	}

	/**
	 *
	 * Updates the player id in the features file
	 * @param uiConf $uiconf
	 * @param string $uiconfId
	 * @param string $replacementString
	 */
	public static function updateFeaturesFile(uiConf $uiconf, $replacementString, $replacementToken)
	{
		$conf_file = $uiconf->getConfFile(true);
		$featuresFile = $uiconf->getConfFileFeatures(true);
		$newFeatures = str_replace($replacementToken, $replacementString, $featuresFile);
		if ($newFeatures != $featuresFile) {
			$uiconf->setConfFile($conf_file);
			$uiconf->setConfFileFeatures($newFeatures);
			$uiconf->save();
		}
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
		echo "    --ini={path}: path to ui_conf deployment ini file\n";
		echo "    --partner: The partner to deploty for (default is 0)\n";
		echo "    --include-code: path to ui_conf deployment ini file\n";
		echo "    --no-create: dry-run, do not really create the uiconfs\n";
		echo "    --user={username}: define user (defaults to apache) for files ownership add\n";
		echo "    --group={groupname}: define group (defaults to kaltura) for files ownership add\n";
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
		$arguments['partner'] = 0;
		$arguments['user'] = 'apache';
		$arguments['group'] = 'kaltura';
	
		/** get inputs from arguments **/
		foreach($argv as $num => $value)
		{
			if($num == 0) continue;
			
			if(strpos($value, '--') === false) { uiConfDeployment::printUsage('wrong argument '.$value); }
			
		 	$arg_pair = explode('=', str_replace('--','',$value));
			$arg_name = $arg_pair[0];
			$arg_value = @$arg_pair[1];
			
			if(!isset($arguments[$arg_name])) { uiConfDeployment::printUsage('unknown argument '.$arg_name); }
			
			if(is_null($arg_value))
				$arg_value = true;
				
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
		
		//Checks if the partner argument was given
		if(!isset($arguments['partner']) || !($arguments['partner']) || is_null($arguments['partner']))
		{
			KalturaLog::debug("--partner argument wasn't given. Using defualt partner 0");
		}
		else
		{
			self::$partner = PartnerPeer::retrieveByPK($arguments['partner']);
			if(!self::$partner)
			{
				die('no such partner.'.PHP_EOL);
			}
		}
		
		//Check if ini file exists
		if(!file_exists($arguments['ini']))
		{
			uiConfDeployment::printUsage('config file not found '.$arguments['ini']);
		}
	}
	
	/**
	 *
	 * set template partner
	 */
	public static function setTemplatePartner()
	{
		if (!is_null(self::$partner)){
			self::$partner->setTemplatePartnerId(self::$partner->getId());
			self::$partner->save();
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

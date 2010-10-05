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
error_reporting(0);
$code = array();
$kcw_for_editors = array();
$kdp_for_studio = array();
$exlude_tags_from_code = array('uploadforkae', 'uploadforkse',);

/** init arguments **/
$arguments = array();
$arguments['include-code'] = false;
$arguments['no-create'] = false;
$arguments['ini']     = '';

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

$includeCode = $arguments['include-code'];
$skipAddUiconf = $arguments['no-create'];

if(!isset($arguments['ini']) || !($arguments['ini']) || is_null($arguments['ini'])) { uiConfDeployment::printUsage('missing argument --ini'); }
if(!file_exists($arguments['ini'])) { uiConfDeployment::printUsage('config file not found '.$arguments['ini']); }

//error_reporting(0);
$confObj = uiConfDeployment::init($arguments['ini']);

$baseTag = $confObj->general->component->name;
$defaultTags = "autodeploy, {$baseTag}_{$confObj->general->component->version}";
$sections = explode(',', $confObj->general->component->required_widgets);

if($includeCode)
{
	$code[] = '$c = new Criteria();';
	$code[] = '$c->addAnd(UiConfPeer::PARTNER_ID, '.$confObj->statics->partner_id.');';
	$code[] = '$c->addAnd(UiConfPeer::TAGS, "%'.$baseTag.'_".$this->kmc_'.$baseTag.'_version."%", Criteria::LIKE);';
	$code[] = '$c->addAnd(UiConfPeer::TAGS, "%autodeploy%", Criteria::LIKE);';
	$code[] = '$this->confs = UiConfPeer::doSelect($c);';
}

$tags_search = array();
foreach($sections as $section)
{
	$sectionName	= trim($section);
	$sectionBase	= $sectionName.'s';
	$baseSwfUrl	= $confObj->$sectionName->$sectionBase->swfpath;
	$swfName	= $confObj->$sectionName->$sectionBase->swfname;
	$objType	= $confObj->$sectionName->$sectionBase->objtype;
	
	echo "creating uiconfs from section $sectionBase with default values ( $baseSwfUrl , $swfName , $objType )".PHP_EOL;
	$num = 1;
	while(isset($confObj->$sectionName->$sectionBase->{$sectionName.$num}))
	{
		echo "{$sectionName}{$num} , $baseSwfUrl , $swfName , $objType".PHP_EOL;
		$configObj = $confObj->$sectionName->$sectionBase->{$sectionName.$num};
		$tags_search[$configObj->usage] = $configObj->usage;
		$uiconf = uiConfDeployment::populateUiconfFromConfig($configObj, $baseSwfUrl, $swfName, $objType);
		if($uiconf)
		{
			$uiconf_id = uiConfDeployment::addUiConf($kclient, $uiconf);
			if($configObj->usage == 'uploadforkae' || $configObj->usage == 'uploadforkse')
			{
				$kcw_for_editors[$configObj->identifier] = $uiconf_id;
			}
			if($configObj->usage == 'template_uiconf_for_appstudio')
			{
				$kdp_for_studio[$configObj->identifier] = $uiconf_id;
			}
		}
		else
		{
			echo "failed to create uiconf object ({$sectionBase}: {$sectionName}{$num}) due to missing values. check your config.ini".PHP_EOL;
		}
		$num++;
	}
	
	echo PHP_EOL;
}


if($includeCode)
{
	foreach($tags_search as $tag)
	{
		if(in_array($tag, $exlude_tags_from_code)) continue;
		$code[] = '$this->'.$baseTag.'_uiconfs_'.$tag.' = $this->'.uiConfDeploymentCodeGenerator::SEARCH_BY_TAG_FUNCTION_NAME.'("'.$baseTag.'_'.$tag.'");';
	}
	echo PHP_EOL.'// code for KMC wrapper'.PHP_EOL;
	$code[] = uiConfDeploymentCodeGenerator::addSearchConfByTag();
	echo implode(PHP_EOL, $code);
}


class uiConfDeployment
{

	public static function init($conf_file_path)
	{
		require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."infra".DIRECTORY_SEPARATOR."bootstrap_base.php");
		//define("KALTURA_API_PATH", KALTURA_ROOT_PATH.DIRECTORY_SEPARATOR."api_v3");
		
		// Autoloader
		require_once(KALTURA_INFRA_PATH.DIRECTORY_SEPARATOR."KAutoloader.php");
		KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
		KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_API_PATH, "lib", "*"));
		KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_API_PATH, "services", "*"));
		KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "alpha", "plugins", "*")); // needed for testmeDoc
		KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "*"));
		KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "generator")); // needed for testmeDoc
		KAutoloader::setClassMapFilePath(KAutoloader::buildPath('..', "cache", "KalturaClassMap.cache"));
		//KAutoloader::dumpExtra();
		KAutoloader::register();
		
		$conf = parse_ini_file($conf_file_path, true);
		
		$confObj = new Zend_Config_Ini($conf_file_path);
		return $confObj;
	}
	
	public static function readConfFileFromPath($file_path, $is_features = false)
	{
		global $arguments;
		if($is_features) $file_path = str_replace('.xml', '.features.xml', $file_path);
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
	
	public static function addUiConf($kclient, $pe_conf)
	{
		global $skipAddUiconf;
		if($skipAddUiconf) return rand(1000,1200);
		try
		{
			$pe_conf->save();
		}
		catch(Exception $ex)
		{
			die('Exiting on ERROR: '.$ex->getMessage().PHP_EOL);
		}
		echo 'uiconf '.$player.' ID: '.@$pe_conf->getId().PHP_EOL;
		return $pe_conf->getId();
	}
	
	public static function populateUiconfFromConfig($confConfigObj, $baseSwfUrl, $swfName, $objType)
	{
		global $defaultTags, $baseTag, $confObj, $kcw_for_editors, $kdp_for_studio;
		$uiconf = new uiConf();
		$uiconf->setConfFile(uiConfDeployment::readConfFileFromPath($confConfigObj->conf_file));
		if($uiconf->getConfFile() === FALSE)
		{
			return FALSE; // conf file is a must, features is not.
		}
		
		$replace_tag = '';
		if($objType == uiConf::UI_CONF_TYPE_ADVANCED_EDITOR) $replace_tag = 'uIConfigId';
		if($objType == uiConf::UI_CONF_TYPE_EDITOR) $replace_tag = 'UIConfigId';
		if($replace_tag)
		{
			if(isset($confConfigObj->kcw_identifier) && isset($kcw_for_editors[$confConfigObj->kcw_identifier]))
			{
				$pattern = '/<'.$replace_tag.'>(.*)<\/'.$replace_tag.'>/';
				$replacement = "<$replace_tag>{$kcw_for_editors[$confConfigObj->kcw_identifier]}</$replace_tag>";
				$uiconf->setConfFile(preg_replace($pattern, $replacement, $uiconf->getConfFile()));
			}
		}
		if(isset($confConfigObj->usage) && $confConfigObj->usage == 'templates')
		{
			foreach($kdp_for_studio as $identifier => $confId)
			{
				$uiconf->setConfFile(str_replace('@@'.$identifier.'@@', $confId, $uiconf->getConfFile()));
			}
		}
		
		$uiconf->setConfFileFeatures(uiConfDeployment::readConfFileFromPath($confConfigObj->conf_file, true));
		if($uiconf->getConfFileFeatures() === FALSE) echo "missing features conf file for uiconf {$confConfigObj->name}".PHP_EOL; // conf file is a must, features is not.
		
		$uiconf->setPartnerId(0);
		$uiconf->setCreationMode(3);
		$uiconf->setUseCdn(1);
		$uiconf->setObjType($objType);
		
		$uiconf->setName($confConfigObj->name);
		$uiconf->setSwfUrl($baseSwfUrl.$confConfigObj->version.'/'.$swfName);
		$uiconf->setTags($defaultTags.', '.$baseTag.'_'.$confConfigObj->usage);
		
		$uiconf->setWidth(@$confConfigObj->width);
		$uiconf->setHeight(@$confConfigObj->height);
		$uiconf->setConfVars(@$confConfigObj->conf_vars);
		
		return $uiconf;
	}
	
	public static function printUsage($message)
	{
		echo $message.PHP_EOL.PHP_EOL;
		echo 'php '.$_SERVER['SCRIPT_NAME']." --ini={path to ini file} [--no-create]\n\n";
		echo "    --ini: path to ui_conf deployment ini file\n";
		echo "    --include-code: path to ui_conf deployment ini file\n";
		echo "    --no-create: dry-run, do not really create the uiconfs\n";
		die;
	}
}

class uiConfDeploymentCodeGenerator
{
	const SEARCH_BY_TAG_FUNCTION_NAME = 'find_confs_by_usage_tag';
	
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
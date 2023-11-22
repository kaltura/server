<?php
if($argc < 2)
{
	echo("Please specify defaults configuration files directory path");
	exit(-2);
}

$dirName = $argv[1];

if (!file_exists($dirName) && isTemplateFileSupported($dirName))
{
	echo("File [$dirName] support template file for token replacements\n");
	
	$dirName = str_replace('.ini', '.template.ini', $dirName);
	
	echo("New file is [$dirName]\n");
}

if(!file_exists($dirName))
{
	echo("Defaults configuration files directory [$dirName] is not a valid path\n");
	exit(-2);
}
$dirName = realpath($dirName);

$tokensKeysArray = array(
	'adminSecret=(@.*@)' => array('/(@.*@)/', 'getRandomPseudoBytes'),
	'secret=(@.*@)' =>  array('/(@.*@)/', 'getRandomPseudoBytes'),
	'url=(@LIVE_PACKAGER_URL@).*' => array('/@LIVE_PACKAGER_URL@/', 'getLivePackagerUrl'),
	'url=(@VOD_PACKAGER_URL@).*' => array('/@VOD_PACKAGER_URL@/', 'getVodPackagerUrl')
);

chdir(__DIR__);
require_once(__DIR__ . '/../../bootstrap.php');

myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_MASTER;

if(is_dir($dirName))
{
	handleDirectory($dirName);
}
elseif(is_file($dirName))
{
	handleFile($dirName);
}

function handleDirectory($dirName)
{
	$dir = dir($dirName);
	/* @var $dir Directory */
	
	$fileNames = array();
	$errors = array();
	while (false !== ($fileName = $dir->read()))
	{
		$filePath = realpath("$dirName/$fileName");
		if($fileName[0] == '.' || is_dir($filePath) || !preg_match('/^\d+\.\w+\.ini$/', $fileName))
			continue;
	
		KalturaLog::debug("Validate file [$filePath]");
		$objectConfigurations = parse_ini_file($filePath, true);
		if(!is_array($objectConfigurations))
			$errors[] = "Content file [$filePath] is not a valid ini file";
	
		$matches = null;
		if(preg_match_all('/@[A-Z_0-9]+@/', file_get_contents($filePath), $matches) > 0)
			$errors[] = "Content file [$filePath] contains place holders: " . implode("\n\t", $matches[0]);
	
		list($order, $objectType, $fileExtension) = explode('.', $fileName, 3);
		if($fileExtension == 'ini')
			$fileNames[] = $fileName;
	}
	$dir->close();
	if(count($errors))
	{
		KalturaLog::err(implode("\n\n", $errors));
		exit(-3);
	}
	
	sort($fileNames);
	KalturaLog::info("Handling files [" . print_r($fileNames, true) . "]");
	

	foreach($fileNames as $fileName)
	{
		handleFile(realpath("$dirName/$fileName"));
	}
}

function handleFile($filePath)
{
	global $tokensKeysArray;
	$con = Propel::getConnection(PartnerPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
	
	$fileName = basename($filePath);
	KalturaLog::info("Handling file [$fileName]");
	list($order, $objectType, $fileExtension) = explode('.', $fileName, 3);
	$objectConfigurations = parse_ini_file($filePath, true);

	$object = new $objectType();
	/* @var $object BaseObject */
	$peer = $object->getPeer();
	$peerClass = get_class($peer);
	
	$newObjectType = "Insert{$objectType}";
	if(!class_exists($newObjectType))
	{
		eval('
			class Insert' . $objectType . ' extends ' . $objectType . '
			{
				public function setId($v)
				{
					if(!$this->getId())
						parent::setId($v);
					
					return $this;
				}
				
				protected function doSave(PropelPDO $con,$skipReload = false)
				{
					$affectedRows = 0; // initialize var to track total num of affected rows
					if (!$this->alreadyInSave) {
						$this->alreadyInSave = true;
			
						$this->objectSaved = false;
						if ($this->isModified()) {
							if ($this->isNew()) {
								$pk = BasePeer::doInsert($this->buildCriteria(), $con);
								$affectedRows += 1;
								$this->setId($pk);  //[IMV] update autoincrement primary key
								$this->setNew(false);
								$this->objectSaved = true;
							} else {
								$affectedObjects = ' . $peerClass . '::doUpdate($this, $con);
								if($affectedObjects)
									$this->objectSaved = true;
									
								$affectedRows += $affectedObjects;
							}
			
							$this->resetModified(); // [HL] After being saved an object is no longer \'modified\'
						}
			
						$this->alreadyInSave = false;
					}
					return $affectedRows;
				}
			}
		');
	}

	$map = $peer->getTableMap();
	$primaryKeys = $map->getPrimaryKeys();

	foreach($objectConfigurations as $objectConfiguration)
	{
		$object = new $newObjectType();
		/* @var $object BaseObject */
		$pk = null;
		$pkField = null;
		// New logic allowing to use other parameters as uique identifers for updates
		$identifierParam = null;
		$identifierColumn = null;
		
		$setters = array();
		foreach($objectConfiguration as $attributeName => $value)
		{
			if($attributeName == 'id')
			{
				$pk = $value;
			}
			elseif ($attributeName == 'identifierParam')
			{
				$$attributeName = $value;
				continue;
			} 
			if (preg_match('/eval\((?P<evalString>.+)\)/', $value, $matches))
			{
				$evalString = $matches["evalString"];
				$evaluator = new kEvalStringField();
				$evaluator->setScope(new kScope());
				$evaluator->setCode($evalString);
				$value = $evaluator->getValue();
				KalturaLog::info("Evaluated property value: $value");
			}

			//If needed translate token into actual values
			translateValue($tokensKeysArray, $attributeName, $value);

			$setter = "set{$attributeName}";
			if(!is_callable(array($object, $setter)))
				throw new Exception("Attribute [$attributeName] not defined on object type [$objectType]");

			if(preg_match('/^@[^@]+$/', $value))
			{
				$valueFilePath = realpath(dirname($filePath) . '/' . substr($value, 1));
				if(!$valueFilePath || !is_file($valueFilePath))
					throw new Exception("Attribute [$attributeName] file path [$value] not found");

				$value = file_get_contents($valueFilePath);
			}
			
			if(preg_match('/^#[^#]+$/', $value))
			{
			    $value = kPluginableEnumsManager::genericApiToCore(substr($value, 1));
			}

			$setters[$setter] = $value;
		}

		$pkCriteria = null;
		$existingObject = null;
		if(!is_null($pk))
		{
			$pkCriteria = new Criteria();
			$pkCriteria->add(constant(get_class($peer) . '::ID'), $pk);
			$existingObject = $peer->doSelectOne($pkCriteria, $con);
			
		}
		elseif (!is_null($identifierParam))
		{
			// If we have some other form of identifier on the object
			$identifierColumn = $peer::translateFieldName($identifierParam, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME);
			$c = new Criteria();
			$c->add ($identifierColumn, $objectConfiguration[$identifierParam]);
			$existingObject = $peer->doSelectOne($c, $con);
		}
		
		if($existingObject)
		{
			KalturaLog::info ('existing objects will not be re-written');
			continue;
		}

		foreach($setters as $setter => $value)
			$object->$setter($value);

		$object->save();

		if(!is_null($pkCriteria))
			BasePeer::doUpdate($object->buildPkeyCriteria(), $pkCriteria, $con);
			
		kMemoryManager::clearMemory();
	}
}

/**
 * @param $key
 * @param $value
 * @return void
 */
function translateValue($tokensKeysArray, $key, &$value)
{
	$searchPhrase = "$key=$value";

	foreach($tokensKeysArray as $token => $valueFunctioniInfo)
	{
		if(preg_match("/$token/", $searchPhrase, $matches))
		{
			$value = preg_replace($valueFunctioniInfo[0], $valueFunctioniInfo[1](), $value, -1);
			return;
		}
	}
}

function getRandomPseudoBytes()
{
	return md5(KCryptoWrapper::random_pseudo_bytes(16));
}


function getLivePackagerUrl()
{
	$livePackagerUrl = kConf::get('live_packager_url');
	$protocol = parse_url($livePackagerUrl, PHP_URL_SCHEME);
	if(!$protocol)
	{
		$livePackagerUrl = "https://" . $livePackagerUrl;
	}

	return $livePackagerUrl;
}

function getVodPackagerUrl()
{
	$vodPackagerUrl = kConf::get('cdn_host_https');
	$protocol = parse_url($vodPackagerUrl, PHP_URL_SCHEME);
	if(!$protocol)
	{
		$vodPackagerUrl = "https://" . $vodPackagerUrl;
	}

	return $vodPackagerUrl;
}

function isTemplateFileSupported($dirName)
{
	echo("File [$dirName] does not exist - checking if filename support template file\n");
	
	// escaping the dot for preg_match to avoid using it as a single wildcard character
	$supportedRegexFilenameExpressions = array(
		'01\.Partner\.ini',
		'07\.DeliveryProfile\w+\.ini'
	);
	
	$fileName = basename($dirName);
	
	foreach ($supportedRegexFilenameExpressions as $supportedRegexFilenameExpression)
	{
		if (preg_match("/^$supportedRegexFilenameExpression$/", $fileName) === 1)
		{
			return true;
		}
	}
	
	return false;
}

KalturaLog::log('Done.');
exit(0);

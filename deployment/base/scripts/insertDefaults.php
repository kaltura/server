<?php
if($argc < 2)
{
	echo("Please specify defaults configuration files directory path");
	exit(-2);
}

$dirName = $argv[1];
if(!file_exists($dirName))
{
	echo("Defaults configuration files directory [$dirName] is not a valid path");
	exit(-2);
}
$dirName = realpath($dirName);

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
		if($fileName[0] == '.' || is_dir($filePath)|| !preg_match('/^\d+\.\w+?\.?\w+\.ini$/', $fileName) || preg_match('/.*\.template\.ini$/',$fileName)){
			continue;
		}
	
		KalturaLog::debug("Validate file [$filePath]");
		$objectConfigurations = parse_ini_file($filePath, true);
		if(!is_array($objectConfigurations))
			$errors[] = "Content file [$filePath] is not a valid ini file";
	
		$matches = null;
		if(preg_match_all('/@[A-Z_0-9]+@/', file_get_contents($filePath), $matches) > 0)
			$errors[] = "Content file [$filePath] contains place holders: " . implode("\n\t", $matches[0]);
	
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
	$con = Propel::getConnection(PartnerPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
	
	$fileName = basename($filePath);
	KalturaLog::info("Handling file [$fileName]");
	list($order, $objectType, $fileExtension) = explode('.', $fileName, 3);
	$objectConfigurations = parse_ini_file($filePath, true);

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
				
				protected function doSave(PropelPDO $con)
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
								$affectedObjects = ' . $objectType . 'Peer::doUpdate($this, $con);
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
	$object = new $newObjectType();
	/* @var $object BaseObject */

	$peer = $object->getPeer();
	$map = $peer->getTableMap();
	$primaryKeys = $map->getPrimaryKeys();

	foreach($objectConfigurations as $objectConfiguration)
	{
		$object = new $newObjectType();
		/* @var $object BaseObject */
		$pk = null;
		$setters = array();
		foreach($objectConfiguration as $attributeName => $value)
		{
			if($attributeName == 'id')
				$pk = $value;

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

			$setters[$setter] = $value;
		}

		$pkCriteria = null;
		if(!is_null($pk))
		{
			$pkCriteria = new Criteria();
			$pkCriteria->add(constant(get_class($peer) . '::ID'), $pk);
			$existingObject = $peer->doSelectOne($pkCriteria, $con);
			if($existingObject)
				$object = $existingObject;
		}

		foreach($setters as $setter => $value)
			$object->$setter($value);

		$object->save();

		if(!is_null($pkCriteria))
			BasePeer::doUpdate($object->buildPkeyCriteria(), $pkCriteria, $con);
			
		kMemoryManager::clearMemory();
	}
}

KalturaLog::log('Done.');
exit(0);

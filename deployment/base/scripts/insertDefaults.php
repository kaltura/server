<?php
if($argc < 2)
	die("Please specify defaults configuration files directory path");
	
$dirName = $argv[1];
if(!file_exists($dirName) || !is_dir($dirName))
	die("Defaults configuration files directory [$dirName] is a valid directory");
$dirName = realpath($dirName);

chdir(__DIR__);
require_once('../../bootstrap.php');
$con = Propel::getConnection(PartnerPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);

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
	die(implode("\n\n", $errors));
	
sort($fileNames);
	
foreach($fileNames as $fileName)
{
	list($order, $objectType, $fileExtension) = explode('.', $fileName, 3);
	$filePath = realpath("$dirName/$fileName");
	$objectConfigurations = parse_ini_file($filePath, true);

	$object = new $objectType();
	/* @var $object BaseObject */
	
	$peer = $object->getPeer();
	$map = $peer->getTableMap();
	$primaryKeys = $map->getPrimaryKeys();
	
	foreach($objectConfigurations as $objectConfiguration)
	{
		$object = new $objectType();
		/* @var $object BaseObject */
		$pkCriteria = new Criteria();
		$setters = array();
		foreach($objectConfiguration as $attributeName => $value)
		{
			try
			{
				$fieldName = strtoupper($peer->translateFieldName($attributeName, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_FIELDNAME));
				if(isset($primaryKeys[$fieldName]))
				{
					$columnName = $peer->translateFieldName($attributeName, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME);
					$pkCriteria->add($columnName, $value);
					continue;
				}
			}
			catch(PropelException $pe){}
			
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
		
		$existingObjects = $peer->doSelect($pkCriteria, $con);
		if(count($existingObjects))
		{
			$object = reset($existingObjects);
			$pkCriteria = null;
		}
			
		foreach($setters as $setter => $value)
			$object->$setter($value);
			
		$object->save();
		
		if($pkCriteria && count($pkCriteria->keys()))
			BasePeer::doUpdate($object->buildPkeyCriteria(), $pkCriteria, $con);
	}
}

KalturaLog::log('Done.');

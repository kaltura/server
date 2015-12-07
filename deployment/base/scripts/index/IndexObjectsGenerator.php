<?php

require(__DIR__ . '/IndexableField.php');
require(__DIR__ . '/IndexableObject.php');
require(__DIR__ . '/IndexableOptimization.php');

require_once(__DIR__ . '/../../../bootstrap.php');

class IndexObjectsGenerator  
{
	private $searchableObjects = array();	
	private $searchableFields = array();
	private $searchableIndices = array();
	private $indexFiles = array();
	
	public function generateIndexFiles($keys, $dirname)
	{
		foreach($keys as $key) {
			$this->handleSingleFile($key, $dirname);
		}
	}
	
	private function handleSingleFile($key, $path) {
		$path = $path . "//{$key}Index.php";
		
		$fp = fopen($path, 'w');
		if(!$fp)
		{
			KalturaLog::err("Failed to open file " . $path);
			exit(1);
		}
		
		print "\tGenerating Index objects for $key\n";
		$this->createFileHeader($fp, $key);
		$this->generateConstants($fp, $key);
		
		$this->generateSimpleFunction("getObjectName", $fp, $this->searchableObjects[$key]);
		$this->generateSimpleFunction("getObjectIndexName", $fp, $this->searchableObjects[$key]);
		$this->generateSimpleFunction("getSphinxIdField", $fp, $this->searchableObjects[$key]);
		$this->generateSimpleFunction("getPropelIdField", $fp, $this->searchableObjects[$key]);
		$this->generateSimpleFunction("getIdField", $fp, $this->searchableObjects[$key]);
		$this->generateSimpleFunction("getDefaultCriteriaFilter", $fp, $this->searchableObjects[$key]);
		
		$this->generateMapping("getIndexFieldsMap", $fp, $key,"fieldsMap");
		$this->generateMapping("getIndexFieldTypesMap", $fp, $key, "typesMap");
		$this->generateMapping("getIndexNullableList", $fp, $key, "nullableFields");
		$this->generateMapping("getIndexSearchableFieldsMap", $fp, $key, "searchableFieldsMap");
		$this->generateMapping("getSearchFieldsEscapeTypeList", $fp, $key, "searchEscapeTypes");
		$this->generateMapping("getIndexFieldsEscapeTypeList", $fp, $key, "indexEscapeTypes");
		$this->generateMapping("getIndexMatchableList", $fp, $key, "matchableFields");
		$this->generateMapping("getIndexOrderList", $fp, $key, "orderFields");
		$this->generateMapping("getIndexSkipFieldsList", $fp, $key, "skipFields");
		$this->generateMapping("getSphinxConditionsToKeep", $fp, $key, "conditionToKeep");
		
		$this->generateIndexMapping("getSphinxOptimizationMap", $fp, $key, "name");
		$this->generateIndexMapping("getSphinxOptimizationValues", $fp, $key, "getter");
		
		$this->getDoCount($fp, $this->searchableObjects[$key]->peerName);
		
		$this->createFileFooter($fp, $key);
		
		fclose($fp);
	}
	
	public function load($inputFile)
	{
		$objects = array();
		if (!file_exists ($inputFile))
		{
			KalturaLog::err ("input file ". $inputFile ." not found");
			exit(1);
		}
		
		$inputXml = file_get_contents($inputFile);
		$xml = new SimpleXMLElement($inputXml);
		foreach($xml->children() as $searchableObject) {
			$objectAttribtues = $searchableObject->attributes();
			$objName = $objectAttribtues["name"];
			
 			$this->parseObject("$objName", $objectAttribtues);
 			$this->searchableIndices["$objName"] = array();
 			
			foreach($searchableObject->children() as $type => $searchableField) {
				switch($type) {
					case "field":
						$this->parseField("$objName", $searchableField);
						break;
					case "index":
						$this->parseIndex("$objName", $searchableField);
						break;
				}
			}
			$objects[] = "$objName";
		}
		return $objects;
	}
	
	private function parseObject($objName, $objectAttribtues) {
		$object = new IndexableObject($objName);
		if(isset($objectAttribtues["indexId"]))
			$object->setIndexId($objectAttribtues["indexId"]);
		if(isset($objectAttribtues["objectId"]))
			$object->setObjectId($objectAttribtues["objectId"]);
		if(isset($objectAttribtues["id"]))
			$object->setId($objectAttribtues["id"]);
		
		if(isset($objectAttribtues["peerName"])) {
			$object->setPeerName($objectAttribtues["peerName"]);
		} else {
			$object->setPeerName($objName . "Peer");
		}
		
		if(isset($objectAttribtues["indexName"])) {
			$object->setIndexName($objectAttribtues["indexName"]);
		} else {
			$indexName = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $objName));
			$object->setIndexName($indexName);
		}
		
		$this->searchableObjects[$objName] = $object;
	}
	
	private function parseField($objName, $searchableField) 
	{
		$fieldAttribtues = $searchableField->attributes();
		$name = $fieldAttribtues["name"];
		$index = $fieldAttribtues["indexName"];
		$type = $fieldAttribtues["type"];
		$field = new IndexableField("$name", "$index", "$type");
		
		$field->setGetter(isset($fieldAttribtues["getter"]) ? $fieldAttribtues["getter"] :
				preg_replace('/_(.?)/e',"strtoupper('$1')","$name"));
		
		if(isset($fieldAttribtues["nullable"]))
			$field->setNullable($fieldAttribtues["nullable"] == "yes");
		
		if(isset($fieldAttribtues["orderable"]))
			$field->setOrderable($fieldAttribtues["orderable"] == "yes");
		
		if(isset($fieldAttribtues["searchableonly"]))
			$field->setSearchOnly($fieldAttribtues["searchableonly"] == "yes");
		
		if(isset($fieldAttribtues["skipField"]))
			$field->setSkipField($fieldAttribtues["skipField"] == "yes");
		
		if(isset($fieldAttribtues["matchable"]))
			$field->setMatchable($fieldAttribtues["matchable"] == "yes");
		
		if(isset($fieldAttribtues["indexEscapeType"]))
			$field->setIndexEscapeType($fieldAttribtues["indexEscapeType"]);
		
		if(isset($fieldAttribtues["searchEscapeType"]))
			$field->setSearchEscapeType($fieldAttribtues["searchEscapeType"]);
		
		if(isset($fieldAttribtues["keepCondition"]))
			$field->setKeepCondition($fieldAttribtues["keepCondition"] == "yes");
		
		if(isset($fieldAttribtues["sphinxStringAttribute"])) {
			$sphinxType = $fieldAttribtues["sphinxStringAttribute"];
			$field->setSphinxStringAttribute("$sphinxType");
		}
		
		$this->searchableFields[$objName]["$name"] = $field;
	}
	
	private function parseIndex($objName, $indexComplex)
	{
		$index = array();
		$fieldAttribtues = $indexComplex->attributes();
		$format = $fieldAttribtues["format"];
		$index[] = "\"$format\"";
		foreach($indexComplex->children() as $indexValue) {
			$idxValueAttr = $indexValue->attributes();
			$fieldName = $idxValueAttr["field"];
			$getter = array_key_exists("getter", $idxValueAttr) ? $idxValueAttr["getter"] :
				"get" . ucwords(preg_replace('/_(.?)/e',"strtoupper('$1')", $fieldName));
			if(strpos($fieldName, ".") === FALSE)
				$fieldName = $this->toPeerName($this->searchableObjects[$objName], $fieldName);
			
			$index[] = new IndexableOptimization('"' . $fieldName . '"', '"' . $getter . '"');
		}
		
		$this->searchableIndices[$objName][] = $index;
	}
	
	private function toPeerName($object, $field) {
		$indexName = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $object->name));
		return $indexName . "." . strtoupper($field);
	}
	
	private function createFileHeader($fp, $class) {
		$this->printToFile($fp, "<?php");
		$this->printToFile($fp, "");
		$this->printToFile($fp, "/**");
		$this->printToFile($fp, " * Auto-generated index class for {$class}");
		$this->printToFile($fp, "*/");
		$this->printToFile($fp, "class {$class}Index extends BaseIndexObject");
		$this->printToFile($fp, "{");
	}
	
	private function createFileFooter($fp, $class) {
		$this->printToFile($fp, "}");
		$this->printToFile($fp, "");
	}
	
	private function generateMapping($functionName, $fp, $class, $mapName) {
		$callback = array("IndexObjectsGenerator", $functionName);
		// Declare map
		$this->printToFile($fp, "protected static \${$mapName};",1);
		$this->printToFile($fp, "");
		
		// Generate function
		$this->printToFile($fp, "public static function {$functionName}()",1);
		$this->printToFile($fp, "{",1);
		$this->printToFile($fp, "if (!self::\${$mapName})",2);
		$this->printToFile($fp, "{",2);
		$this->printToFile($fp, "self::\${$mapName} = array(",3);
		
		foreach($this->searchableFields[$class] as $key => $value)
			call_user_func($callback, $fp, $this->searchableObjects[$class], $key , $value);
		
		$this->printToFile($fp, ");",3);
		$this->printToFile($fp, "}",2);
		$this->printToFile($fp, "return self::\${$mapName};",2);
		$this->printToFile($fp, "}",1);
		$this->printToFile($fp, "");
	}
	
	private function generateConstants($fp, $class) {
		foreach($this->searchableFields[$class] as $key => $value)
			if(($value->type == "json") || ($value->searchOnly))
				$this->printToFile($fp, "const " . strtoupper($key) . " = \"$key\";\n" ,1);
	}
	
	private function generateSimpleFunction($functionName, $fp, $object) {
		$callback = array("IndexObjectsGenerator", $functionName);
		$this->printToFile($fp, "public static function {$functionName}()",1);
		$this->printToFile($fp, "{",1);
		call_user_func($callback, $fp, $object);
		$this->printToFile($fp, "}",1);
		$this->printToFile($fp, "");
	}
	
	private function getObjectName($fp, $object) {
		$indexName = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $object->name));
		$this->printToFile($fp, "return '$indexName';",2);
	}
	
	private function getObjectIndexName($fp, $object) {
		$this->printToFile($fp, "return '" . $object->indexName . "';",2);
	}
	
	private function getIndexFieldsMap($fp, $object, $key , $value) {
		if(!$value->searchOnly)
			$this->printToFile($fp, "'" . $value->indexName . "' => '" . $value->getter . "',",4);
	}
	
	private function getIndexFieldTypesMap($fp, $obejct, $key , $value) {
		$type = null;
		switch($value->type) {
			case "string":
				$type = "IIndexable::FIELD_TYPE_STRING";
				break;
			case "int":
			case "bint":
				$type = "IIndexable::FIELD_TYPE_INTEGER";
				break;
			case "datetime":
				$type = "IIndexable::FIELD_TYPE_DATETIME";
				break;
			case "json":
				$type = "IIndexable::FIELD_TYPE_JSON";
				break;
		}
		
		if(!is_null($type))
			$this->printToFile($fp, "'" . $value->indexName . "' => " . $type . ",",4);
	}
	
	private function getIndexSearchableFieldsMap($fp, $object, $key , $value) {
		$objectField = $this->toPeerName($object, $key);
		$this->printToFile($fp, "'" .  $objectField . "' => '" . $value->indexName . "',",4);
	}
	
	private function getIndexOrderList($fp, $object, $key , $value) {
		$objectField = $this->toPeerName($object, $key);
		if($value->orderable)
			$this->printToFile($fp, "'" .  $objectField . "' => '" . $value->indexName . "',",4);
	}
	
	private function getIndexNullableList($fp, $object, $key , $value) {
		if($value->nullable)
			$this->printToFile($fp, "'" . $value->indexName . "',",4);
	}
	
	private function getIndexMatchableList($fp, $object, $key , $value) {
		if($value->matchable)
			$this->printToFile($fp, "\"" . $key . "\",",4);
	}
	
	private function getIndexSkipFieldsList($fp, $object, $key , $value) {
		if($value->skipField) 
			$this->printToFile($fp, "'" . $this->toPeerName($object, $key) . "',",4);
	}
	
	private function getIndexFieldsEscapeTypeList($fp, $object, $key , $value) {
		
		$val = $value->indexEscapeType;
		if(!is_null($val)) {
			
			$type = null;
			switch($val) {
				case 1:
					$type = 'SearchIndexFieldEscapeType::DEFAULT_ESCAPE';
					break;
				case 2:
					$type = 'SearchIndexFieldEscapeType::NO_ESCAPE';
					break;
				case 3:
					$type = 'SearchIndexFieldEscapeType::MD5_LOWER_CASE';
					break;
			}
			
			$objectField = $this->toPeerName($object, $key);
			$this->printToFile($fp, "'" . $objectField . "' => " . $type . "," ,4);
		}
	}
	
	private function getSearchFieldsEscapeTypeList($fp, $object, $key , $value) {
	
		$val = $value->searchEscapeType;
		if(!is_null($val)) {
				
			$type = null;
			switch($val) {
				case 1:
					$type = 'SearchIndexFieldEscapeType::DEFAULT_ESCAPE';
					break;
				case 2:
					$type = 'SearchIndexFieldEscapeType::NO_ESCAPE';
					break;
				case 3:
					$type = 'SearchIndexFieldEscapeType::MD5_LOWER_CASE';
					break;
			}
				
			$objectField = $this->toPeerName($object, $key);
			$this->printToFile($fp, "'" . $objectField . "' => " . $type . "," ,4);
		}
	}
	
	private function getSphinxConditionsToKeep($fp, $object, $key , $value) {
		if($value->keepCondition) 
			$this->printToFile($fp, "'" . $this->toPeerName($object, $key) . "',",4);
	}
	
	private function getSphinxIdField($fp, $object) {
		$this->printToFile($fp, "return '" . $object->indexId . "';",2);
	}
	
	private function getPropelIdField($fp, $object) {
		$this->printToFile($fp, "return " . $object->peerName . "::" . $object->objectId . ";",2);
	}
	
	private function getIdField($fp, $object) {
		if(is_null($object->id))
			$this->printToFile($fp, "return null;",2);
		else
			$this->printToFile($fp, "return " . $object->peerName . "::" . $object->id . ";",2);
	}
	
	private function getDefaultCriteriaFilter($fp, $object) {
		$this->printToFile($fp, "return " . $object->peerName . "::getCriteriaFilter();",2);
	}
	
	private function getDoCount($fp, $peerName) {
		$this->printToFile($fp, "public static function doCountOnPeer(Criteria \$c)",1);
		$this->printToFile($fp, "{",1);
		$this->printToFile($fp, "return {$peerName}::doCount(\$c);",2);
		$this->printToFile($fp, "}",1);
		$this->printToFile($fp, "");
	}
	
	private function printToFile($fp, $string, $tabs = 0) {
		fwrite($fp, str_repeat("\t",$tabs) . $string . "\n");
	}
	
	private function generateIndexMapping($function, $fp, $key, $field) {
		$this->printToFile($fp, "public static function $function()",1);
		$this->printToFile($fp, "{",1);
		$this->printToFile($fp, "return array(",2);
		foreach($this->searchableIndices[$key] as $indices) {
			$names = array();
			$names[] = array_shift($indices);
			foreach($indices as $index)
				$names[] = call_user_func(array($index, "get" . ucwords($field)));
			$this->printToFile($fp, "array(" . implode(",", $names) . "),",3);
		}
				
		$this->printToFile($fp, ");",2);
		$this->printToFile($fp, "}",1);
		$this->printToFile($fp, "");
	}
	
	public function generateConfigurationFile($structFile, $outputFile) {
		
		print "Generating Kaltura.conf file based on {$structFile} at {$outputFile}\n";
		
		$sphinxConfiguration = file_get_contents($structFile);
		
		foreach ($this->searchableObjects as $object) {
			$sphinxConfiguration = preg_replace("/@FIELDS_PLACEHOLDER-kaltura_{$object->indexName}@/", 
				$this->generateFields($object->name), $sphinxConfiguration, -1, $cnt);
			if($cnt != 1)
			{
				KalturaLog::err("Failed to generate kaltura conf for {$object->name}.");
				exit (1);
			}
		}
		
		if(preg_match("/@FIELDS_PLACEHOLDER-([\w]*)@/", $sphinxConfiguration,$matches))
		{
			KalturaLog::err("Not all kaltura conf sections were filled! Missing " . $matches[1]);
			exit (1);
		}
		file_put_contents($outputFile, $sphinxConfiguration);
	}
	
	private function generateFields($objectName) {
		$fieldsMap = array();
		foreach ($this->searchableFields[$objectName] as $field) {
			$types = $this->toSphinxType($field->type, $field->matchable, $field->sphinxStringAttribute);
 			foreach($types as $type)
 				$fieldsMap[] = $type . " = " . $field->indexName;
		}
		return implode("\n\t", $fieldsMap);
	}
	
	private function toSphinxType($type, $matchable, $sphinxStringAttr) {
		switch($type) {
			case "string":
				if($sphinxStringAttr == "both")
					return array("rt_field", "rt_attr_string");
				if($sphinxStringAttr == "string")
					return array("rt_attr_string");
				return array("rt_field");
			case "int":
				return array("rt_attr_uint");
			case "bint":
				return array("rt_attr_bigint");
			case "datetime":
				return array("rt_attr_timestamp");
			case "json":
				return array("rt_attr_json");
		}
		return array();
	}
	
}

function main($argv) 
{
	if(count($argv) < 4)
	{
		KalturaLog::err("Illegal command. use IndexObjectsGenerator <template> <updated-conf> <indexFile>=<generationPath>\n");
		exit(1);
	}
	$template = $argv[1];
	$confFile = $argv[2];
	
	$generator = new IndexObjectsGenerator();
	
	foreach($argv as $arg) {
		if(strpos($arg, "=") === false)
			continue;
		
		list($indexFile, $dirPath) = explode("=", $arg);
		KalturaLog::info("Handling Index file $indexFile");
		$keys = $generator->load($indexFile);
		$generator->generateIndexFiles($keys, $dirPath);
	}
	
	$generator->generateConfigurationFile($template, $confFile);
	
}

main($argv);
exit(0);

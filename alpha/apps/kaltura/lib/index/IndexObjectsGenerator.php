<?php

require('IndexableField.php');
require('IndexableObject.php');

class IndexObjectsGenerator  
{
	private $searchableObjects = array();	
	private $searchableFields = array();
	private $indexFiles = array();
	
	public function handle($file, $dirname)
	{
		$this->load($file);
		$keys = array_keys($this->searchableFields);
		foreach($keys as $key) {
			$this->handleSingleFile($key, $dirname);
		}
	}
	
	private function handleSingleFile($key, $path) {
		$path = $path . "/lib/model/index/{$key}Index.php";
		$fp = fopen($path, 'w');
		if(!$fp)
			die("Failed to open file " . $path);
		
		$this->createFileHeader($fp, $key);
		
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
		
		$this->getDoCount($fp, $this->searchableObjects[$key]->peerName);
		
		$this->createFileFooter($fp, $key);
		
		fclose($fp);
	}
	
	private function load($inputFile)
	{
		$xml = simplexml_load_file($inputFile);
		foreach($xml->children() as $objName => $searchableObject) {
 			$this->searchableFields["$objName"] = array();
 			
 			$this->parseObject("$objName", $searchableObject);
 			
			foreach($searchableObject->children() as $name => $searchableField) {
				$this->parseField("$objName", $name, $searchableField);
			}
		}
	}
	
	private function parseObject($objName, $searchableObject) {
		$object = new IndexableObject($objName);
		$objectAttribtues = $searchableObject->attributes();
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
		
		$this->searchableObjects[$objName] = $object;
	}
	
	private function parseField($objName, $name, $searchableField) 
	{
		$fieldAttribtues = $searchableField->attributes();
		$index = $fieldAttribtues["indexName"];
		$type = $fieldAttribtues["type"];
		$field = new IndexableField("$name", "$index", "$type");
		
		$field->setGetter(isset($fieldAttribtues["getter"]) ? $fieldAttribtues["getter"] :
				preg_replace('/_(.?)/e',"strtoupper('$1')",$name));
		
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
		
		$this->searchableFields[$objName]["$name"] = $field;
	}
	
	private function toFieldName($class, $field) {
		return $class . "." . strtoupper($field);
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
		$this->printToFile($fp, "public static function {$functionName}()",1);
		$this->printToFile($fp, "{",1);
		$this->printToFile($fp, "if (!self::\${$mapName})",2);
		$this->printToFile($fp, "{",2);
		$this->printToFile($fp, "self::\${$mapName} = array(",3);
		
		foreach($this->searchableFields[$class] as $key => $value)
			call_user_func($callback, $fp, $class, $key , $value);
		
		$this->printToFile($fp, ");",3);
		$this->printToFile($fp, "}",2);
		$this->printToFile($fp, "return self::\${$mapName};",2);
		$this->printToFile($fp, "}",1);
		$this->printToFile($fp, "");
	}
	
	private function generateSimpleFunction($functionName, $fp, $object) {
		$callback = array("IndexObjectsGenerator", $functionName);
		$this->printToFile($fp, "public static function {$functionName}()",1);
		$this->printToFile($fp, "{",1);
		call_user_func($callback, $fp, $object);
		$this->printToFile($fp, "}",1);
		$this->printToFile($fp, "");
	}
	
	private function getObjectIndexName($fp, $object) {
		$indexName = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $object->name));
		$this->printToFile($fp, "return '{$indexName}';",2);
	}
	
	private function getIndexFieldsMap($fp, $class, $key , $value) {
		if(!$value->searchOnly)
			$this->printToFile($fp, "'" . $value->indexName . "' => '" . $value->getter . "',",4);
	}
	
	private function getIndexFieldTypesMap($fp, $class, $key , $value) {
		$type = null;
		switch($value->type) {
			case "string":
				$type = "IIndexable::FIELD_TYPE_STRING";
				break;
			case "int":
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
	
	private function getIndexSearchableFieldsMap($fp, $class, $key , $value) {
		$objectField = $this->toFieldName($class, $key);
		$this->printToFile($fp, "'" .  $objectField . "' => '" . $value->indexName . "',",4);
	}
	
	private function getIndexOrderList($fp, $class, $key , $value) {
		$objectField = $this->toFieldName($class, $key);
		if($value->orderable)
			$this->printToFile($fp, "'" .  $objectField . "' => '" . $value->indexName . "',",4);
	}
	
	private function getIndexNullableList($fp, $class, $key , $value) {
		if($value->nullable)
			$this->printToFile($fp, "'" . $value->indexName . "',",4);
	}
	
	private function getIndexMatchableList($fp, $class, $key , $value) {
		if($value->matchable)
			$this->printToFile($fp, "\"" . $key . "\",",4);
	}
	
	private function getIndexSkipFieldsList($fp, $class, $key , $value) {
		if($value->skipField) 
			$this->printToFile($fp, "'" . $this->toFieldName($class, $key) . "',",4);
	}
	
	private function getIndexFieldsEscapeTypeList($fp, $class, $key , $value) {
		
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
			
			$objectField = $this->toFieldName($class, $key);
			$this->printToFile($fp, "'" . $objectField . "' => " . $type . "," ,4);
		}
	}
	
	private function getSearchFieldsEscapeTypeList($fp, $class, $key , $value) {
	
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
				
			$objectField = $this->toFieldName($class, $key);
			$this->printToFile($fp, "'" . $objectField . "' => " . $type . "," ,4);
		}
	}
	
	private function getSphinxConditionsToKeep($fp, $class, $key , $value) {
		if($value->keepCondition) 
			$this->printToFile($fp, "'" . $this->toFieldName($class, $key) . "',",4);
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
}

function main($argv) 
{
	if(count($argv) != 3)
		die("Illegal command. use IndexObjectsGenerator <indexFile> <generationPath>\n");
	print "Generating Index objects for index file : " . $argv[1] . "\n";
	$generator = new IndexObjectsGenerator();
	$generator->handle($argv[1], $argv[2]);
}

main($argv);
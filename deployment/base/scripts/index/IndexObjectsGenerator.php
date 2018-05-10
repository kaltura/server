<?php

require(__DIR__ . '/IndexableField.php');
require(__DIR__ . '/IndexableObject.php');
require(__DIR__ . '/IndexableOptimization.php');
require(__DIR__ . '/IndexGeneratorBase.php');

require_once(__DIR__ . '/../../../bootstrap.php');

class IndexObjectsGenerator extends IndexGeneratorBase
{
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
		$this->generateMapping("getIndexEnrichableList", $fp, $key, "enrichableFields");
		$this->generateMapping("getIndexSearchableFieldsMap", $fp, $key, "searchableFieldsMap");
		$this->generateMapping("getSearchFieldsEscapeTypeList", $fp, $key, "searchEscapeTypes");
		$this->generateMapping("getIndexFieldsEscapeTypeList", $fp, $key, "indexEscapeTypes");
		$this->generateMapping("getIndexMatchableList", $fp, $key, "matchableFields");
		$this->generateMapping("getIndexOrderList", $fp, $key, "orderFields");
		$this->generateMapping("getIndexSkipFieldsList", $fp, $key, "skipFields");
		$this->generateMapping("getSphinxConditionsToKeep", $fp, $key, "conditionToKeep");
		$this->generateMapping("getApiCompareAttributesMap", $fp, $key, "apiCompareAttributesMap");
		$this->generateMapping("getApiMatchAttributesMap", $fp, $key, "apiMatchAttributesMap");
		
		$this->generateIndexMapping("getSphinxOptimizationMap", $fp, $key, "name");
		$this->generateIndexMapping("getSphinxOptimizationValues", $fp, $key, "getter");
		
		$this->getDoCount($fp, $this->searchableObjects[$key]->peerName);
		
		$this->createFileFooter($fp, $key);
		
		fclose($fp);
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
	
	private function generateSimpleFunction($functionName, $fp, IndexableObject $object) {
		$callback = array("IndexObjectsGenerator", $functionName);
		$this->printToFile($fp, "public static function {$functionName}()",1);
		$this->printToFile($fp, "{",1);
		call_user_func($callback, $fp, $object);
		$this->printToFile($fp, "}",1);
		$this->printToFile($fp, "");
	}
	
	private function getObjectName($fp, IndexableObject $object) {
		$indexName = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $object->name));
		$this->printToFile($fp, "return '$indexName';",2);
	}
	
	private function getObjectIndexName($fp, IndexableObject $object) {
		$this->printToFile($fp, "return '" . $object->indexName . "';",2);
	}
	
	private function getIndexFieldsMap($fp, IndexableObject $object, $key, IndexableField $value) {
		if(!$value->searchOnly)
			$this->printToFile($fp, "'" . $value->indexName . "' => '" . $value->getter . "',",4);
	}
	
	private function getIndexFieldTypesMap($fp, $obejct, $key, IndexableField $value) {
		$type = null;
		switch($value->type) {
			case "string":
				$type = "IIndexable::FIELD_TYPE_STRING";
				break;
			case "int":
				$type = "IIndexable::FIELD_TYPE_UINT";
				break;				
			case "bint":
				$type = "IIndexable::FIELD_TYPE_INTEGER";
				break;
			case "datetime":
				$type = "IIndexable::FIELD_TYPE_DATETIME";
				break;
			case "json":
				$type = "IIndexable::FIELD_TYPE_JSON";
				break;

// 			float is support in sphinx 2.2.10, we don't use that version yet.
// 			case "float":
// 				$type = "IIndexable::FIELD_TYPE_FLOAT";
// 				break;
		}
		
		if(!is_null($type))
			$this->printToFile($fp, "'" . $value->indexName . "' => " . $type . ",",4);
	}
	
	private function getIndexSearchableFieldsMap($fp, IndexableObject $object, $key, IndexableField $value) {
		$objectField = $this->toPeerName($object, $key);
		$this->printToFile($fp, "'" .  $objectField . "' => '" . $value->indexName . "',",4);
	}
	
	private function getIndexOrderList($fp, IndexableObject $object, $key, IndexableField $value) {
		$objectField = $this->toPeerName($object, $key);
		if($value->orderable)
			$this->printToFile($fp, "'" .  $objectField . "' => '" . $value->indexName . "',",4);
	}
	
	private function getIndexNullableList($fp, IndexableObject $object, $key, IndexableField $value) {
		if($value->nullable)
			$this->printToFile($fp, "'" . $value->indexName . "',",4);
	}

	private function getIndexEnrichableList($fp, IndexableObject $object, $key, IndexableField $value) {
		if($value->enrichable)
			$this->printToFile($fp, "'" . $value->indexName . "',",4);
	}
	
	private function getIndexMatchableList($fp, IndexableObject $object, $key, IndexableField $value) {
		if($value->matchable)
			$this->printToFile($fp, "\"" . $key . "\",",4);
	}
	
	private function getIndexSkipFieldsList($fp, IndexableObject $object, $key, IndexableField $value) {
		if($value->skipField) 
			$this->printToFile($fp, "'" . $this->toPeerName($object, $key) . "',",4);
	}
	
	private function getIndexFieldsEscapeTypeList($fp, IndexableObject $object, $key, IndexableField $value) {
		
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
	
	private function getSearchFieldsEscapeTypeList($fp, IndexableObject $object, $key, IndexableField $value) {
	
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
	
	private function getSphinxConditionsToKeep($fp, IndexableObject $object, $key, IndexableField $value) {
		if($value->keepCondition) 
			$this->printToFile($fp, "'" . $this->toPeerName($object, $key) . "',",4);
	}

	private function getApiCompareAttributesMap($fp, IndexableObject $object, $key, IndexableField $value) {
		$numericTypes = array(
			'int', 
			'bint',
			'datetime',
				
// 			float is support in sphinx 2.2.10, we don't use that version yet.
// 			'float',
		);
		if($value->apiName && in_array($value->type, $numericTypes))
			$this->printToFile($fp, "'" .  $value->apiName . "' => '" . $value->indexName . "',",4);
	}

	private function getApiMatchAttributesMap($fp, IndexableObject $object, $key, IndexableField $value) {
		if($value->apiName && ($value->type == 'string' || $value->matchable))
			$this->printToFile($fp, "'" . $value->apiName . "' => '" . $value->indexName . "',",4);
	}
	
	private function getSphinxIdField($fp, IndexableObject $object) {
		$this->printToFile($fp, "return '" . $object->indexId . "';",2);
	}
	
	private function getPropelIdField($fp, IndexableObject $object) {
		$this->printToFile($fp, "return " . $object->peerName . "::" . $object->objectId . ";",2);
	}
	
	private function getIdField($fp, IndexableObject $object) {
		if(is_null($object->id))
			$this->printToFile($fp, "return null;",2);
		else
			$this->printToFile($fp, "return " . $object->peerName . "::" . $object->id . ";",2);
	}
	
	private function getDefaultCriteriaFilter($fp, IndexableObject $object) {
		$this->printToFile($fp, "return " . $object->peerName . "::getCriteriaFilter();",2);
	}
	
	private function getDoCount($fp, $peerName) {
		$this->printToFile($fp, "public static function doCountOnPeer(Criteria \$c)",1);
		$this->printToFile($fp, "{",1);
		$this->printToFile($fp, "return {$peerName}::doCount(\$c);",2);
		$this->printToFile($fp, "}",1);
		$this->printToFile($fp, "");
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

// 			float is support in sphinx 2.2.10, we don't use that version yet.
// 			case "float":
// 				return array("rt_attr_float");
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

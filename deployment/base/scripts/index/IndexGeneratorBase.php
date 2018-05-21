<?php

class IndexGeneratorBase
{
	protected $searchableObjects = array();
	protected $searchableFields = array();
	protected $searchableIndices = array();
	protected $searchableCacheInvalidationKeys = array();
	protected $indexFiles = array();
	
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
					case "":
						$this->parseCacheInvalidationKey("$objName", $searchableField);
						break;
				}
			}
			$objects[] = "$objName";
		}
		return $objects;
	}
	
	protected function parseObject($objName, $objectAttribtues) {
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

		if(isset($objectAttribtues["apiName"])) {
			$apiName = (string)$objectAttribtues["apiName"];
			$apiName = preg_replace('/_(.?)/e',"strtoupper('$1')", $apiName);
			$object->setApiName($apiName);
		}
		
		$this->searchableObjects[$objName] = $object;
	}
	
	protected function parseField($objName, SimpleXMLElement $searchableField)
	{
		$fieldAttributes = $searchableField->attributes();
		$name = $this->tryXpath($searchableField, $fieldAttributes["name"]);
		$index = $this->tryXpath($searchableField, $fieldAttributes["indexName"]);
		$type = $this->tryXpath($searchableField, $fieldAttributes["type"]);
		$field = new IndexableField("$name", "$index", "$type");
		
		$field->setGetter(isset($fieldAttributes["getter"]) ? $fieldAttributes["getter"] :
				preg_replace('/_(.?)/e',"strtoupper('$1')","$name"));

		if (!isset($fieldAttributes["getter"]))
			$fieldAttributes->addAttribute('getter', $field->getter); // so we could use the getter in xpath even if it was not explicitly defined

		if(isset($fieldAttributes["apiName"])) {
			$apiName = $this->tryXpath($searchableField, (string)$fieldAttributes["apiName"]);
			$apiName = preg_replace('/_(.?)/e',"strtoupper('$1')", $apiName);
			$field->setApiName($apiName);
		}

		if(isset($fieldAttributes["nullable"]))
			$field->setNullable($fieldAttributes["nullable"] == "yes");

		if(isset($fieldAttributes["orderable"]))
			$field->setOrderable($fieldAttributes["orderable"] == "yes");
		
		if(isset($fieldAttributes["searchableonly"]))
			$field->setSearchOnly($fieldAttributes["searchableonly"] == "yes");
		
		if(isset($fieldAttributes["skipField"]))
			$field->setSkipField($fieldAttributes["skipField"] == "yes");
		
		if(isset($fieldAttributes["matchable"]))
			$field->setMatchable($fieldAttributes["matchable"] == "yes");
		
		if(isset($fieldAttributes["indexEscapeType"]))
			$field->setIndexEscapeType($fieldAttributes["indexEscapeType"]);
		
		if(isset($fieldAttributes["searchEscapeType"]))
			$field->setSearchEscapeType($fieldAttributes["searchEscapeType"]);
		
		if(isset($fieldAttributes["keepCondition"]))
			$field->setKeepCondition($fieldAttributes["keepCondition"] == "yes");
		
		if(isset($fieldAttributes["sphinxStringAttribute"])) {
			$sphinxType = $fieldAttributes["sphinxStringAttribute"];
			$field->setSphinxStringAttribute("$sphinxType");
		}

		if(isset($fieldAttributes["enrichable"]))
			$field->setEnrichable($fieldAttributes["enrichable"] == "yes");

		$this->searchableFields[$objName]["$name"] = $field;
	}
	
	protected function parseIndex($objName, $indexComplex)
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

	protected function parseCacheInvalidationKey($objName, $indexComplex)
	{
		$index = array();
		$fieldAttribtues = $indexComplex->attributes();
		$format = $fieldAttribtues["format"];
		$index[] = "\"$format\"";
		foreach($indexComplex->children() as $indexValue)
		{
			$idxValueAttr = $indexValue->attributes();
			$fieldName = $idxValueAttr["field"];
			$getter = "get" . ucwords(preg_replace('/_(.?)/e', "strtoupper('$1')", $fieldName));
			$apiName = lcfirst(ucwords(preg_replace('/_(.?)/e', "strtoupper('$1')", $fieldName)));
			$index[] = new IndexableCacheInvalidationKey(strtoupper($fieldName), $getter, $objName . "Peer", $apiName);
		}

		$this->searchableCacheInvalidationKeys[$objName] = $index;
	}

	protected function tryXpath(SimpleXMLElement $element, $maybeXpath)
	{
		$xpathResults = @$element->xpath($maybeXpath);
		return is_array($xpathResults) && count($xpathResults) ? (string)$xpathResults[0] : $maybeXpath;
	}

	protected function toPeerName(IndexableObject $object, $field) {
		$indexName = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $object->name));
		return $indexName . "." . strtoupper($field);
	}
	
	protected function printToFile($fp, $string, $tabs = 0) {
		fwrite($fp, str_repeat("\t",$tabs) . $string . "\n");
	}
}

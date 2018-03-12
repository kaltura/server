<?php

class IndexGeneratorBase
{
	protected $searchableObjects = array();
	protected $searchableFields = array();
	protected $searchableIndices = array();
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
			$apiName = preg_replace_callback("/_(.?)/",
				function($matches)
				{
					foreach($matches as $match){
						return strtoupper(ltrim($match, "_"));
					}
				},
				$apiName
			);
			$object->setApiName($apiName);
		}
		
		$this->searchableObjects[$objName] = $object;
	}
	
	protected function parseField($objName, SimpleXMLElement $searchableField)
	{
		$fieldAttribtues = $searchableField->attributes();
		$name = $this->tryXpath($searchableField, $fieldAttribtues["name"]);
		$index = $this->tryXpath($searchableField, $fieldAttribtues["indexName"]);
		$type = $this->tryXpath($searchableField, $fieldAttribtues["type"]);
		$field = new IndexableField("$name", "$index", "$type");
		
		$field->setGetter(isset($fieldAttribtues["getter"]) ? $fieldAttribtues["getter"] :
				preg_replace_callback("/_(.?)/",
					function($matches)
					{
						foreach($matches as $match){
							return strtoupper(ltrim($match, "_"));
						}
					}, $name)
				);

		if (!isset($fieldAttribtues["getter"]))
			$fieldAttribtues->addAttribute('getter', $field->getter); // so we could use the getter in xpath even if it was not explicitly defined

		if(isset($fieldAttribtues["apiName"])) {
			$apiName = $this->tryXpath($searchableField, (string)$fieldAttribtues["apiName"]);
			$apiName = preg_replace_callback("/_(.?)/",
				function($matches)
				{
					foreach($matches as $match){
						return strtoupper(ltrim($match, "_"));
					}
				},
				$apiName
			);
			$field->setApiName($apiName);
		}

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
				"get" . ucwords(preg_replace_callback("/_(.?)/", function($matches) { foreach($matches as $match){ return strtoupper($match); }}, $fieldName));
			if(strpos($fieldName, ".") === FALSE)
				$fieldName = $this->toPeerName($this->searchableObjects[$objName], $fieldName);
			
			$index[] = new IndexableOptimization('"' . $fieldName . '"', '"' . $getter . '"');
		}
		
		$this->searchableIndices[$objName][] = $index;
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

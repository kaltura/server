<?php
require_once(dirname(__FILE__) . "/../../lib/KalturaClient.php");
require_once('config/config_rand_test.php');

class XSDEditor
{
	/**
	 * 
	 * Static function to delete an element by a provided ID.
	 * @param array $schemaArray
	 * @param string $deleteElementId
	 * @return array
	 */
	public static function deleteElement ($schemaArray)
	{
		$deleteIndex = rand (0, count($schemaArray)-1);
		
		echo "Delete element $deleteIndex\n";

		array_splice($schemaArray, $deleteIndex, 1);	
		
		return $schemaArray;
	}
	
	/**
	 * 
	 * Static function to reorder the XSD.
	 * @param string $schema
	 * @return array
	 */
	public static function reorderSchema ($schemaArray )
	{
		$firstSwap = rand (0, count($schemaArray)-1);
		
		$secondSwap = rand (0, count($schemaArray)-1);
		
		$temp = $schemaArray[$firstSwap];
		
		$schemaArray[$firstSwap] = $schemaArray[$secondSwap];
		
		$schemaArray[$secondSwap] = $temp;
		
		return $schemaArray;
	}
		
	
	/**
	 * 
	 * Static function to add an element to the schema
	 * @param array $schemaArray
	 * @param MetadataField $addElement
	 * @return array
	 */
	public static function addElement ($schemaArray)
	{

		
		$randType = rand (1,4);
		
		switch ($randType)
		{
			case 1:
				$addElement = new MetadataTextField(uniqid("id_"), uniqid("name_"), "1", "true");
				break;
			case 2:
				$addElement = new MetadataDateField(uniqid("id_"), uniqid("name_"), "1", "true");
				break;
			case 3:
				$addElement = new MetadataListField(uniqid("id_"), uniqid("name_"), MetadataField::UNBOUNDED, "true", Config::$newListVals);
				break;
			case 4:
				$addElement = new MetadataEntryListField(uniqid("id_"), uniqid("name_"), "1", "true");
				break;
		}
		
		$randIndex = rand (0, count($schemaArray));
		
		array_splice($schemaArray, $randIndex, 0, array($addElement));

		return $schemaArray;
	}
	
	/**
	 *
	 * @param array $schemaArray
	 * @param string $elementNameChangeTo
	 * @return array
	 */
	public static function changeFieldName ($schemaArray, $elementNameChangeTo)
	{
		$indexToChange = rand (0, count($schemaArray)-1);
		
		echo "Rename element $indexToChange\n";
		
		
		/* @var $schemaElement MetadataField */
		$schemaElement = $schemaArray[$indexToChange];
		$schemaElement->label = $elementNameChangeTo;
		
		return $schemaArray;
		
	}
	
	/**
	 * 
	 * @param array $schemaArray
	 * @param string $elementId
	 * @param array $newValues
	 * @return array
	 */
	public static function changeListValues ($schemaArray, $newValues)
	{
		
		$indexArr = array();
		
		
		foreach ($schemaArray as $index => $schemaElement)
		{
			if ($schemaElement instanceof  MetadataListField)
			{
				$indexArr[] = $index;
			}
		}
		
		if (!count($indexArr))
		{
			return $schemaArray;
		}
		
		$indexToChange = rand(0, count($indexArr)-1);
		
		$elementToChange = $schemaArray[$indexArr[$indexToChange]];
		
		/* @var $elementToChange MetadataListField */
		
		$option = rand (0,2);
		
		switch ($option)
		{
			case 0:
				$randIndex = rand (0, count($elementToChange->valueList)-1);
				array_splice($elementToChange->valueList, $randIndex, 0, uniqid('res_'));
				break;
			case 1:
				$randIndex = rand (0, count($elementToChange->valueList)-1);
				array_splice($elementToChange->valueList, $randIndex, 1);
				break;
			case 2:
				$elementToChange->valueList = $newValues;
				break;
		}
		
		return $schemaArray;
	}
	
	
	
	/**
	 * @param string $xml
	 * @param string $xslData
	 * @return bool:string false if failed, xml text if succeed
	 */
	public static function transformXmlData($xml, $xslData)
	{
		$from = new DOMDocument();
		$from->loadXML($xml);
		
		$xsl = new DOMDocument();
		$xsl->loadXML($xslData);
		
		$proc = new XSLTProcessor();
		
		try {
			$proc->importStyleSheet($xsl);
			
			$output = $proc->transformToXML($from);
		}
		catch (Exception $e)
		{
			
			return null;
		}
		
		return $output;
	}
	
	/**
	 * @param string $fromXsd old xsd string
	 * @param string $toXsd new xsd string
	 * @return bool|string true if no change required, or, xsl text if transform required
	 * @throws kXsdException
	 */
	public static function compareXsd($fromXsd, $toXsd)
	{
		$from = new DOMDocument();
		$from->loadXML($fromXsd);
		
		if(!$from || !$from->documentElement)
			return false;
			
		$to = new DOMDocument();
		$to->loadXML($toXsd);
		
		if(!$to || !$to->documentElement)
			return false;
			
		$xsl = kXsd::compareNode($from->documentElement, $to->documentElement);
	
		if(is_bool($xsl))
		{
			if($xsl)
				return true;
				
			return false;
		}
	
		$xsl = '<?xml version="1.0" encoding="ISO-8859-1"?>
	<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
		<xsl:output method="xml" version="1.0" encoding="iso-8859-1" indent="yes"/>
		<xsl:strip-space elements="*" />
		<xsl:template match="/">' . $xsl . '
		</xsl:template>
	</xsl:transform>
	';
		
		return $xsl;
	}
	
	
		
}

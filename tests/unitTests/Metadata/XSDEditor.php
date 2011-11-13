<?php
require_once(dirname(__FILE__) . "/../../lib/KalturaClient.php");
require_once('config/config_rand_test.php');

class XSDEditor
{
	public static $shouldTransform = false;

	/**
	 * 
	 * Static function to delete an element by a provided ID.
	 * @param array $schemaArray
	 * @param string $deleteElementId
	 * @return array
	 */
	public static function deleteElement ($schemaArray)
	{
		if (!count($schemaArray))
		{
			return $schemaArray;
		}

		$deleteIndex = rand (0, count($schemaArray)-1);
		
		echo "  Deleting element idx=$deleteIndex id={$schemaArray[$deleteIndex]->id}\n";

		array_splice($schemaArray, $deleteIndex, 1);
		
		self::$shouldTransform = true;
				
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
		if (count($schemaArray) < 2)
		{
			return $schemaArray;
		}
	
		$firstSwap = rand (0, count($schemaArray)-1);
		$secondSwap = rand (0, count($schemaArray)-1);
		if ($firstSwap == $secondSwap)
		{
			if ($firstSwap == 0)
			{
				$secondSwap = 1;
			}
			else
			{
				$secondSwap = $firstSwap - 1;
			}
		}
		
		echo "  Swapping elements idx1=$firstSwap id1={$schemaArray[$firstSwap]->id} idx2=$secondSwap id2={$schemaArray[$secondSwap]->id}\n";
		
		$temp = $schemaArray[$firstSwap];	
		$schemaArray[$firstSwap] = $schemaArray[$secondSwap];
		$schemaArray[$secondSwap] = $temp;
		
		self::$shouldTransform = true;
		
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
		$addElement = Config::getRandomField(uniqid("id_"), uniqid("name_"));
		
		$randIndex = rand (0, count($schemaArray));
		
		array_splice($schemaArray, $randIndex, 0, array($addElement));

		echo "  Adding element idx=$randIndex id={$addElement->id}\n";
		
		return $schemaArray;
	}
	
	/**
	 *
	 * @param array $schemaArray
	 * @return array
	 */
	public static function changeFieldName ($schemaArray)
	{
		if (!count($schemaArray))
		{
			return $schemaArray;
		}
		
		$indexToChange = rand (0, count($schemaArray)-1);
		
		/* @var $schemaElement MetadataField */
		$schemaElement = $schemaArray[$indexToChange];
		$schemaElement->name = uniqid("name_");
		
		echo "  Changing element name idx=$indexToChange id={$schemaElement->id}\n";
		
		self::$shouldTransform = true;
		
		return $schemaArray;
	}
	
	/**
	 * 
	 * @param array $schemaArray
	 * @param string $elementId
	 * @return array
	 */
	public static function changeListValues ($schemaArray)
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
		
		$indexToChange = $indexArr[rand(0, count($indexArr)-1)];
		$elementToChange = $schemaArray[$indexToChange];
		
		/* @var $elementToChange MetadataListField */
		
		$option = rand (0,2);
		
		switch ($option)
		{
			case 0:
				$randIndex = rand (0, count($elementToChange->valueList)-1);
				array_splice($elementToChange->valueList, $randIndex, 0, uniqid('res_'));
				echo "  Adding list value fieldIdx=$indexToChange fieldId={$elementToChange->id} valIdx=$randIndex\n";
				break;
			case 1:
				$randIndex = rand (0, count($elementToChange->valueList)-1);
				array_splice($elementToChange->valueList, $randIndex, 1);
				echo "  Removing list value fieldIdx=$indexToChange fieldId={$elementToChange->id} valIdx=$randIndex\n";
				self::$shouldTransform = true;
				break;
			case 2:
				$elementToChange->valueList = Config::getNewListVals();
				echo "  Replacing list values fieldIdx=$indexToChange fieldId={$elementToChange->id}\n";
				self::$shouldTransform = true;
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
		
		try
		{
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

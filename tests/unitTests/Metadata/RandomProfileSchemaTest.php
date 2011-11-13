<?php
require_once(dirname(__FILE__) . "/../../../api_v3/bootstrap.php");
require_once('config/config_rand_test.php');
require_once('XSDEditor.php');
require_once(dirname(__FILE__). "/../../../infra/general/kXsd.php");
require_once(dirname(__FILE__). "/../../../infra/log/KalturaLog.php");
require_once(dirname(__FILE__). "/../../../infra/bootstrap_base.php");


class RandomProfileSchemaTest extends PHPUnit_Framework_TestCase
{ 
	/**
	 * @var array
	 */
	private $schemaArray;
	/**
	 * @var string
	 */
	private $schema;
	
	/**
	 * 
	 * @var string
	 */
	private $transSchema;
	
	/**
	 * 
	 * @var string
	 */
	private $testXML;
	
	const DELETE_ACTION = 1;
	
	const EDIT_ACTION = 2;
	
	const ADD_ACTION = 3;
	
	const RENAME_ELEMENT = 4;
	
	const CHANGE_LIST_VALUES = 5;
	
	public function testRandomProfileSchema()
	{
		for ($i=0; $i<10000; $i++)		
		{
			echo "Starting $i\n";
			$this->runOnce();
			echo "Done $i\n";
		}
	}
	
	public function runOnce()
	{
		XSDEditor::$shouldTransform	 = false;
		
		$this->schemaArray = Config::schemaArray();
		
		$this->schema = $this->generateXSD($this->schemaArray);
		
		$this->testXML = $this->generateTestXML($this->schemaArray);
				
		$numOfActions = rand(1,4);

		for ($i=1; $i<=$numOfActions; $i++)
		{
			$this->randomizeAction();
		}
		
 		$this->transSchema = $this->generateXSD($this->schemaArray);
		
		$this->metadataTransformed();
	}
	
	private function randomizeAction ()
	{
		$randAct = rand(1, 5);
		
		switch ($randAct)
		{
			case self::DELETE_ACTION:
				$this->schemaArray = XSDEditor::deleteElement($this->schemaArray);
				break;
			case self::ADD_ACTION:
				$this->schemaArray = XSDEditor::addElement($this->schemaArray);
				break;
			case self::EDIT_ACTION:
				$this->schemaArray = XSDEditor::reorderSchema($this->schemaArray);
				break;				
			case self::RENAME_ELEMENT:
				$this->schemaArray = XSDEditor::changeFieldName($this->schemaArray);
				break;
			case self::CHANGE_LIST_VALUES:
				$this->schemaArray = XSDEditor::changeListValues($this->schemaArray);
				break;
		}
	}
	
	
	private function generateXSD (array $schemaArray)
	{
		$schema = '<xsd:schema xmlns:xsd="http://www.w3.org/2001/XMLSchema">
  		<xsd:element name="metadata">
    	<xsd:complexType>
      	<xsd:sequence>';
		
		
		foreach ($schemaArray as $element)
		{
			if (is_null($element) || !($element instanceof MetadataField ))
			{
				continue;
			}
			/* @var $element MetadataField */
			$schema .= $element->getXSD();
		}
		
		$schema.= '</xsd:sequence>
    </xsd:complexType>
  </xsd:element>
  <xsd:complexType name="textType">
    <xsd:simpleContent>
      <xsd:extension base="xsd:string"/>
    </xsd:simpleContent>
  </xsd:complexType>
  <xsd:complexType name="dateType">
    <xsd:simpleContent>
      <xsd:extension base="xsd:long"/>
    </xsd:simpleContent>
  </xsd:complexType>
  <xsd:complexType name="objectType">
    <xsd:simpleContent>
      <xsd:extension base="xsd:string"/>
    </xsd:simpleContent>
  </xsd:complexType>
  <xsd:simpleType name="listType">
    <xsd:restriction base="xsd:string"/>
  </xsd:simpleType>
</xsd:schema>';
		return $schema;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param array $schemaArray
	 * @return string
	 */
	private function generateTestXML ($schemaArray)
	{
		$testXML = "<metadata>\n";
		foreach ($schemaArray as $schemaElement)
		{
			/* @var $schemaElement MetadataField */
			$testXML.= "\t" . $schemaElement->getXML() . "\n";
		}
		$testXML .= "</metadata>\n";
		
		return $testXML;
	}
	
	private function metadataTransformed()
	{
		file_put_contents("source.xsd", $this->schema);
		file_put_contents("target.xsd", $this->transSchema);
		file_put_contents("source.xml", $this->testXML);
		
		$xsl = XSDEditor::compareXsd($this->schema, $this->transSchema);
		
		file_put_contents("transform.xsl",$xsl);
				
		if (!is_bool($xsl))
		{
			$this->assertTrue(XSDEditor::$shouldTransform, 'transform was done unnecessarily');
		
			$transXML = XSDEditor::transformXmlData($this->testXML, $xsl);
			
			file_put_contents("transform.xml",$transXML);
			
			$domXML = new DOMDocument();
			$domXML->loadXML($transXML);
			
			try 
			{				
				$result = $domXML->schemaValidateSource($this->transSchema);
			}
			catch (Exception $e)
			{
				$this->assertTrue(false, $e->getMessage());
			}
			
			if (!$result)
			{
				$this->assertTrue(false, 'failed to validate XML');
			}
		}
		else
		{
			$domXML = new DOMDocument();
			$domXML->loadXML($this->testXML);
			//Claiming that existing XML does not require change, validate against new schema....
			if  (!$domXML->schemaValidateSource($this->transSchema))
			{
				$this->assertTrue(false, 'failed to validate XML');
			}
		}
	}
}
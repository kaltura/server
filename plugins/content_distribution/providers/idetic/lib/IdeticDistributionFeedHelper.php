<?php
/**
 * @package plugins.youTubeDistribution
 * @subpackage lib
 */
class IdeticDistributionFeedHelper
{

	/**
	 * @var DOMDocument
	 */
	protected $doc;
	
	/**
	 * @var DOMXPath
	 */
	protected $xpath;
			
	/**
	 * @param $templateName
	 * @param $distributionProfile
	 */
	public function __construct($templateName)
	{
		$xmlTemplate = realpath(dirname(__FILE__) . '/../') . '/xml_templates/' . $templateName;
		$this->doc = new KDOMDocument();
		$this->doc->load($xmlTemplate);
		$this->xpath = new DOMXPath($this->doc);
	}
	
	public function getXmlString()
	{
		return $this->doc->saveXML();
	}
			
	/**
	 * @param string $xpath
	 * @param string $value
	 */
	public function setNodeValue($xpath, $value)
	{
		$node = $this->xpath->query($xpath)->item(0);
		if (!is_null($node))
		{
			// if CDATA inside, set the value of CDATA
			if ($node->childNodes->length > 0 && $node->childNodes->item(0)->nodeType == XML_CDATA_SECTION_NODE)
				$node->childNodes->item(0)->nodeValue = $value;
			else
				$node->nodeValue = $value;
		}
	}
	
	/**
	 * @param string $xpath
	 * @param string $value
	 */
	public function getNodeValue($xpath)
	{
		$node = $this->xpath->query($xpath)->item(0);
		if (!is_null($node))
			return $node->nodeValue;
		else
			return null;
	}
	
	
	public function setStartTime($value)
	{
		kXml::setNodeValue($this->xpath,'/ProgramDescription/ProgramLocationTable/ProgramLocation/OnDemandProgram/StartOfAvailability', $value);
	}
	
	public function setEndTime($value)
	{
		kXml::setNodeValue($this->xpath,'/ProgramDescription/ProgramLocationTable/ProgramLocation/OnDemandProgram/EndOfAvailability', $value);
	}
	
	public function setTitle($value)
	{
		kXml::setNodeValue($this->xpath,'/ProgramDescription/ProgramInformationTable/ProgramInformation/BasicDescription/Title', $value);
	}
	
	public function setShortTitle($value)
	{
		kXml::setNodeValue($this->xpath,'/ProgramDescription/ProgramInformationTable/ProgramInformation/BasicDescription/ShortTitle', $value);
	}
	
	public function setSynopsis($value)
	{
		kXml::setNodeValue($this->xpath,'/ProgramDescription/ProgramInformationTable/ProgramInformation/BasicDescription/Synopsis', $value);
	}
	
	public function setKeyword($value)
	{
		kXml::setNodeValue($this->xpath,'/ProgramDescription/ProgramInformationTable/ProgramInformation/BasicDescription/Keyword', $value);
	}
	
	public function setGenre($value)
	{
		kXml::setNodeValue($this->xpath,'/ProgramDescription/ProgramInformationTable/ProgramInformation/BasicDescription/Genre', $value);
	}
	
	public function setSlot($value)
	{
		kXml::setNodeValue($this->xpath,'/ProgramDescription/ProgramInformationTable/ProgramInformation/BasicDescription/Slot', $value);
	}
	
	public function setFolder($value)
	{
		kXml::setNodeValue($this->xpath,'/ProgramDescription/ProgramInformationTable/ProgramInformation/BasicDescription/Folder', $value);
	}
		
	public function setIndirectUploadUrl($value)
	{
		kXml::setNodeValue($this->xpath,'/ProgramDescription/ProgramLocationTable/ProgramLocation/OnDemandProgram/IndirectUploadURL', $value);
	}
	
	public function setThumbnail($value)
	{
		kXml::setNodeValue($this->xpath,'/ProgramDescription/ProgramLocationTable/ProgramLocation/OnDemandProgram/thumbnail', $value);
	}
	
	public function setChecksum($value)
	{
		//TODO: veirfy that make sense
		kXml::setNodeValue($this->xpath,'/ProgramDescription/ProgramLocationTable/ProgramLocation/OnDemandProgram/Checksum', $value);
	}
			
}
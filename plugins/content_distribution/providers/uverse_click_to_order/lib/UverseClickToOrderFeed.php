<?php
/**
 * @package plugins.uverseClickToOrderDistribution
 * @subpackage lib
 */
class UverseClickToOrderFeed
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
	 * @var DOMElement
	 */
	protected $item;

	/**
	 * @var DOMElement
	 */
	protected $category;
	
	/**
	 * @var UverseClickToOrderDistributionProfile
	 */
	protected $distributionProfile;
	
	/**
	 * @param $templateName
	 * @param $distributionProfile
	 */
	public function __construct($templateName)
	{
		$xmlTemplate = realpath(dirname(__FILE__) . '/../') . '/xml_templates/' . $templateName;
		$this->doc = new DOMDocument();
		$this->doc->formatOutput = true;
		$this->doc->preserveWhiteSpace = false;
		$this->doc->load($xmlTemplate);
		
		$this->xpath = new DOMXPath($this->doc);
			
		// item node template
		$node = $this->xpath->query('/root/category/item')->item(0);		
		$this->item = $node->cloneNode(true);
		$node->parentNode->removeChild($node);

		// category node template
		$node = $this->xpath->query('/root/category')->item(0);		
		$this->category = $node->cloneNode(true);
		$node->parentNode->removeChild($node);
	}
	
	/**
	 * @param string $xpath
	 * @param string $value
	 */
	public function setNodeValue($xpath, $value, DOMNode $contextnode = null)
	{
		if ($contextnode)
			$node = $this->xpath->query($xpath, $contextnode)->item(0);
		else 
			$node = $this->xpath->query($xpath)->item(0);
		if (!is_null($node))
		{
			// if CDATA inside, set the value of CDATA
			if ($node->childNodes->length > 0 && $node->childNodes->item(0)->nodeType == XML_CDATA_SECTION_NODE)
				$node->childNodes->item(0)->nodeValue = htmlentities($value);
			else
				$node->nodeValue = htmlentities($value);
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
	
	/**
	 * @param UverseClickToOrderDistributionProfile $profile
	 */
	public function setDistributionProfile(UverseClickToOrderDistributionProfile $profile)
	{
		$this->distributionProfile = $profile;
	}
	
	public function addCategory($categoryName, $categoryFile)
	{	
		$parentNode = $this->xpath->query('/root')->item(0);
		$categoryNode = $this->category->cloneNode(true);
		$parentNode->appendChild($categoryNode);
		$this->setNodeValue('@name', $categoryName, $categoryNode);	
		$this->setNodeValue('@file', $categoryFile, $categoryNode);
		
		return $categoryNode;
	}
	
	public function addItem(array $values, $categoryNode, $thumbnailFile, $flavorFile)
	{		
		$item = $this->item->cloneNode(true);
		$categoryNode->appendChild($item);
		
		$this->setNodeValue('@title', $values[UverseClickToOrderDistributionField::ITEM_TITLE], $item);
		$this->setNodeValue('@content_type', $values[UverseClickToOrderDistributionField::ITEM_CONTENT_TYPE], $item);
		$this->setNodeValue('@file', $thumbnailFile, $item);
		$this->setNodeValue('@destination', $values[UverseClickToOrderDistributionField::ITEM_DESTINATION], $item);
		$this->setNodeValue('@vidfile', $flavorFile, $item);
		$this->setNodeValue('@ccvidfile', $values[UverseClickToOrderDistributionField::ITEM_CCVIDFILE], $item);
		$this->setNodeValue('content', $values[UverseClickToOrderDistributionField::ITEM_CONTENT], $item);
		$this->setNodeValue('directions', $values[UverseClickToOrderDistributionField::ITEM_DIRECTIONS], $item);
	}
	
	public function setBackgroudImage($widedBackgroundImageUrl = null, $standardBackgroundImageUrl = null)
	{				
		if ($widedBackgroundImageUrl)
		{
			$this->setNodeValue('background_image/@wide', $widedBackgroundImageUrl);
		}
		if ($standardBackgroundImageUrl)
		{	
			$this->setNodeValue('background_image/@standard', $standardBackgroundImageUrl);
		}
	}
	
	public function getXml()
	{
		return $this->doc->saveXML();
	}
	
}
<?php
/**
 * @package plugins.uverseClickToOrderDistribution
 * @subpackage lib
 */
class UverseClickToOrderFeed
{
    const VIDFILE_CONTENT_TYPE = 'video';
    
    
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
		$this->doc = new KDOMDocument('1.0', 'UTF-8');
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
		kXml::setNodeValue($this->xpath, $xpath, $value, $contextnode);
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
		kXml::setNodeValue($this->xpath,'@name', $categoryName, $categoryNode);	
		kXml::setNodeValue($this->xpath,'@file', $categoryFile, $categoryNode);
		
		return $categoryNode;
	}
	
	public function addItem(array $values, $categoryNode, $thumbnailFile, $flavorFile)
	{		
		$item = $this->item->cloneNode(true);
		$categoryNode->appendChild($item);
		
		kXml::setNodeValue($this->xpath,'@title', $values[UverseClickToOrderDistributionField::ITEM_TITLE], $item);
		kXml::setNodeValue($this->xpath,'@content_type', $values[UverseClickToOrderDistributionField::ITEM_CONTENT_TYPE], $item);
		kXml::setNodeValue($this->xpath,'@file', $thumbnailFile, $item);
		kXml::setNodeValue($this->xpath,'@destination', $values[UverseClickToOrderDistributionField::ITEM_DESTINATION], $item);
		if (strtolower($values[UverseClickToOrderDistributionField::ITEM_CONTENT_TYPE]) == self::VIDFILE_CONTENT_TYPE) {
		    kXml::setNodeValue($this->xpath,'@vidfile', $flavorFile, $item);
		}
		else {
		    $item->removeAttribute('vidfile');
		}		
		kXml::setNodeValue($this->xpath,'@ccvidfile', $values[UverseClickToOrderDistributionField::ITEM_CCVIDFILE], $item);
		kXml::setNodeValue($this->xpath,'content', $values[UverseClickToOrderDistributionField::ITEM_CONTENT], $item);
		kXml::setNodeValue($this->xpath,'directions', $values[UverseClickToOrderDistributionField::ITEM_DIRECTIONS], $item);
	}
	
	public function setBackgroudImage($widedBackgroundImageUrl = null, $standardBackgroundImageUrl = null)
	{				
		if ($widedBackgroundImageUrl)
		{
			kXml::setNodeValue($this->xpath,'background_image/@wide', $widedBackgroundImageUrl);
		}
		if ($standardBackgroundImageUrl)
		{	
			kXml::setNodeValue($this->xpath,'background_image/@standard', $standardBackgroundImageUrl);
		}
	}
	
	public function getXml()
	{
		return $this->doc->saveXML($dom->documentElement);
	}
	
}
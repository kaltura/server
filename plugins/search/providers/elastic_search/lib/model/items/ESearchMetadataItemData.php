<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
class ESearchMetadataItemData extends ESearchItemData
{

	/**
	 * @var string
	 */
	protected $xpath;

	/**
	 * @var int
	 */
	protected $metadataProfileId;

	/**
	 * @var int
	 */
	protected $metadataFieldId;

	/**
	 * @var string
	 */
	protected $valueText;

	/**
	 * @var string
	 */
	protected $valueInt;

	public function getType()
	{
		return ESearchItemDataType::METADATA;
	}

	/**
	 * @return string
	 */
	public function getXpath()
	{
		return $this->xpath;
	}

	/**
	 * @param string $xpath
	 */
	public function setXpath($xpath)
	{
		$this->xpath = $xpath;
	}

	/**
	 * @return int
	 */
	public function getMetadataProfileId()
	{
		return $this->metadataProfileId;
	}

	/**
	 * @param int $metadataProfileId
	 */
	public function setMetadataProfileId($metadataProfileId)
	{
		$this->metadataProfileId = $metadataProfileId;
	}

	/**
	 * @return int
	 */
	public function getMetadataFieldId()
	{
		return $this->metadataFieldId;
	}

	/**
	 * @param int $metadataFieldId
	 */
	public function setMetadataFieldId($metadataFieldId)
	{
		$this->metadataFieldId = $metadataFieldId;
	}

	/**
	 * @return string
	 */
	public function getValueText()
	{
		return $this->valueText;
	}

	/**
	 * @param string $valueText
	 */
	public function setValueText($valueText)
	{
		$this->valueText = $valueText;
	}

	/**
	 * @return int
	 */
	public function getValueInt()
	{
		return $this->valueInt;
	}

	/**
	 * @param int $valueInt
	 */
	public function setValueInt($valueInt)
	{
		$this->valueInt = $valueInt;
	}

	public function loadFromElasticHits($objectResult)
	{
		$this->setXpath($objectResult['_source']['xpath']);
		$this->setMetadataProfileId($objectResult['_source']['metadata_profile_id']);
		$this->setMetadataFieldId($objectResult['_source']['metadata_field_id']);
		if(isset($objectResult['_source']['value_text']))
			$this->setValueText(implode(',',$objectResult['_source']['value_text']));

		if(isset($objectResult['_source']['value_int']))
			$this->setValueInt($objectResult['_source']['value_int']);

		if(isset($objectResult['highlight']))
			$this->setHighlight($objectResult['highlight']);
	}
	
}
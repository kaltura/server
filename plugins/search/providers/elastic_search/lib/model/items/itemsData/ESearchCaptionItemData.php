<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
class ESearchCaptionItemData extends ESearchItemData
{
	/**
	 * @var string
	 */
	protected $line;

	/**
	 * @var int
	 */
	protected $startsAt;

	/**
	 * @var int
	 */
	protected $endsAt;

	/**
	 * @var string
	 */
	protected $language;

	/**
	 * @var string
	 */
	protected $captionAssetId;

	/**
	 * @var string
	 */
	protected $label;

	public function getType()
	{
		return ESearchItemDataType::CAPTION;
	}

	/**
	 * @return string
	 */
	public function getLine()
	{
		return $this->line;
	}

	/**
	 * @param string $line
	 */
	public function setLine($line)
	{
		$this->line = $line;
	}

	/**
	 * @return int
	 */
	public function getStartsAt()
	{
		return $this->startsAt;
	}

	/**
	 * @param int $startsAt
	 */
	public function setStartsAt($startsAt)
	{
		$this->startsAt = $startsAt;
	}

	/**
	 * @return int
	 */
	public function getEndsAt()
	{
		return $this->endsAt;
	}

	/**
	 * @param int $endsAt
	 */
	public function setEndsAt($endsAt)
	{
		$this->endsAt = $endsAt;
	}

	/**
	 * @return string
	 */
	public function getLanguage()
	{
		return $this->language;
	}

	/**
	 * @param string $language
	 */
	public function setLanguage($language)
	{
		$this->language = $language;
	}

	/**
	 * @return string
	 */
	public function getCaptionAssetId()
	{
		return $this->captionAssetId;
	}

	/**
	 * @param string $captionAssetId
	 */
	public function setCaptionAssetId($captionAssetId)
	{
		$this->captionAssetId = $captionAssetId;
	}

	/**
	 * @return string
	 */
	public function getLabel()
	{
		return $this->label;
	}

	/**
	 * @param string $label
	 */
	public function setLabel($label)
	{
		$this->label = $label;
	}

	public function loadFromElasticHits($objectResult)
	{
		$this->setLine($objectResult['_source']['content']);
		$this->setStartsAt($objectResult['_source']['start_time']);
		$this->setEndsAt($objectResult['_source']['end_time']);
		$this->setLanguage($objectResult['_source']['language']);
		$this->setCaptionAssetId($objectResult['_source']['caption_asset_id']);
		if(isset($objectResult['_source']['label']))
			$this->setLabel($objectResult['_source']['label']);
		if(isset($objectResult['highlight']))
			$this->setHighlight($objectResult['highlight']);
	}
}
<?php

/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
class ESearchAttachmentItemData extends ESearchItemData
{
	/**
	 * @var int
	 */
	protected $pageNumber;

	/**
	 * @var string
	 */
	protected $content;

	/**
	 * @var string
	 */
	protected $fileName;

	/**
	 * @var string
	 */
	protected $assetId;

	/**
	 * @var string
	 */
	protected $assetType;

	/**
	 * @var string
	 */
	protected $assetSubType;

	/**
	 * @var string
	 */
	protected $tags;

	/**
	 * @var int
	 */
	protected $accuracy;


	public function getType()
	{
		return ESearchItemDataType::ATTACHMENTS;
	}

	/**
	 * @return int
	 */
	public function getPageNumber()
	{
		return $this->pageNumber;
	}

	/**
	 * @param int $pageNumber
	 */
	public function setPageNumber($pageNumber)
	{
		$this->pageNumber = $pageNumber;
	}

	/**
	 * @return string
	 */
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * @param string $content
	 */
	public function setContent($content)
	{
		$this->content = $content;
	}

	/**
	 * @return string
	 */
	public function getFileName()
	{
		return $this->fileName;
	}

	/**
	 * @param string $fileName
	 */
	public function setFileName($fileName)
	{
		$this->fileName = $fileName;
	}

	/**
	 * @return string
	 */
	public function getAssetId()
	{
		return $this->assetId;
	}

	/**
	 * @param string $assetId
	 */
	public function setAssetId($assetId)
	{
		$this->assetId = $assetId;
	}

	/**
	 * @return string
	 */
	public function getAssetType()
	{
		return $this->assetType;
	}

	/**
	 * @param string $assetType
	 */
	public function setAssetType($assetType)
	{
		$this->assetType = $assetType;
	}

	/**
	 * @return string
	 */
	public function getAssetSubtype()
	{
		return $this->assetSubType;
	}

	/**
	 * @param string $assetSubtype
	 */
	public function setAssetSubType($assetSubtype)
	{
		$this->assetSubType = $assetSubtype;
	}

	/**
	 * @return string
	 */
	public function getTags()
	{
		return $this->tags;
	}

	/**
	 * @param string $assetSubtype
	 */
	public function setTags($tags)
	{
		$this->tags = $tags;
	}

	/**
	 * @return int
	 */
	public function getAccuracy()
	{
		return $this->accuracy;
	}

	/**
	 * @param int $accuracy
	 */
	public function setAccuracy($accuracy)
	{
		$this->accuracy = $accuracy;
	}


	public function loadFromElasticHits($objectResult)
	{
		$this->setContent($objectResult['_source']['content']);
		$this->setAssetType($objectResult['_source']['asset_type']);;
		$this->setAssetSubType($objectResult['_source']['asset_sub_type']);
		$this->setPageNumber($objectResult['_source']['page_number']);
		$this->setFileName($objectResult['_source']['file_name']);
		$this->setAssetId($objectResult['_source']['asset_id']);
		if (isset($objectResult['_source']['accuracy']))
			$this->setAccuracy($objectResult['_source']['accuracy']);
		if (isset($objectResult['_source']['tags']))
			$this->setTags($objectResult['_source']['tags']);

	}
}

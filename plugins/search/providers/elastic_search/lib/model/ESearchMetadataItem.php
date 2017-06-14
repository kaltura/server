<?php

class ESearchMetadataItem extends ESearchItem
{

	/**
	 * @var string
	 */
	protected $searchTerm;

	/**
	 * @var string;
	 */
	protected $xpath;

	/**
	 * @var int;
	 */
	protected $metadataProfileId;


	/**
	 * @return string
	 */
	public function getSearchTerm()
	{
		return $this->searchTerm;
	}

	/**
	 * @param string $searchTerm
	 */
	public function setSearchTerm($searchTerm)
	{
		$this->searchTerm = $searchTerm;
	}

	public function createSubQuery()
	{
		return $this->getSearchTerm();
	}

	public function getType()
	{
		return 'metadata';
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



}
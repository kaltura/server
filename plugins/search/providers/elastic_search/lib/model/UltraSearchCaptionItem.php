<?php

class UltraSearchCaptionItem extends UltraSearchItem
{

	/**
	 * @var string
	 */
	protected $searchTerm;

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

	public function createSearchQuery()
	{
		return kUltraQueryManager::createCaptionSearchQuery($this);
	}
}
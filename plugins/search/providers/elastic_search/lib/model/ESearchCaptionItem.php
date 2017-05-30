<?php

class ESearchCaptionItem extends ESearchItem
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

	public function createSubQuery()
	{
		return $this->getSearchTerm();
//		return kEQueryManager::createCaptionSearchQuery($this);
	}

	public function getType()
	{
		return 'caption';
	}


}
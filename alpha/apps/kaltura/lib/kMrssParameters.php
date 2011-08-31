<?php

class kMrssParameters
{
	//////////////// Persistent attributes ////////////////
	
	/**
	 * 
	 * An array of xpaths of mrss fields that point to an entry which should be extended
	 * @var array
	 */
	private $itemXpathsToExtend;
	
	//////////////// Dynamic attributes ////////////////
	
	private $link;
	
	private $filterByFlavorParams;
	
	private $includePlayerTag;
	
	private $playerUiconfId;
	
	/**
	 * @return the $itemXpathsToExtend
	 */
	public function getItemXpathsToExtend() {
		return $this->itemXpathsToExtend;
	}

	/**
	 * @param array $itemXpathsToExtend
	 */
	public function setItemXpathsToExtend($itemXpathsToExtend) {
		if (is_array($itemXpathsToExtend)) {
			$this->itemXpathsToExtend = $itemXpathsToExtend;			
		}
	}

	public function getLink()
	{
		return $this->link;
	}

	public function setLink($link)
	{
		$this->link = $link;
	}
	
	public function getFilterByFlavorParams()
	{
		return $this->filterByFlavorParams;
	}

	public function setFilterByFlavorParams($filterByFlavorParams)
	{
		$this->filterByFlavorParams = $filterByFlavorParams;
	}
	
	public function getIncludePlayerTag()
	{
		return $this->includePlayerTag;
	}

	public function setIncludePlayerTag($includePlayerTag)
	{
		$this->includePlayerTag = $includePlayerTag;
	}
	
	public function getPlayerUiconfId()
	{
		return $this->playerUiconfId;
	}
	
	public function setPlayerUiconfId($playerUiconfId)
	{
		$this->playerUiconfId = $playerUiconfId;
	}
}

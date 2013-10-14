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
	
	private $storageId;
	
	//Statuses for asset retrieval by default will retrieve only ready assets 
	private $statuses = array(flavorAsset::ASSET_STATUS_READY);
	
	private $encoding;
	
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
	
	public function getStorageId()
	{
		return $this->storageId;
	}
	
	public function setStorageId($storageId)
	{
		$this->storageId = $storageId;
	}
	
	public function getStatuses()
	{
		return $this->statuses;
	}
	
	public function setStatuses(array $statuses)
	{
		$this->statuses = $statuses;
	}
	
	/**
	 * @return string $encoding
	 */
	public function getEncoding()
	{
		return $this->encoding;
	}

	/**
	 * @param string $encoding
	 */
	public function setEncoding($encoding)
	{
		$this->encoding = $encoding;
	}
}

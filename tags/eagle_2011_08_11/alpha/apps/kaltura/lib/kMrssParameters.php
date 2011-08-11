<?php

class kMrssParameters
{
	/**
	 * 
	 * An array of xpaths of mrss fields that point to an entry which should be extended
	 * @var array
	 */
	private $itemXpathsToExtend;
	
	
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

	
	
}
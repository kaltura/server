<?php
/**
 * @package plugins.FeedDropFolder
 * @subpackage model
 */
class ApFeedDropFolder extends FeedDropFolder
{
	
	/**
	 * @return array
	 */
	public function getItemsToExpand()
	{
		return $this->getFromCustomData('itemsToExpand');
	}
	
	/**
	 * @param array $itemsToExpand
	 */
	public function setItemsToExpand($itemsToExpand)
	{
		$this->putInCustomData('itemsToExpand', $itemsToExpand);
	}
	
	/**
	 * @return the $apApiKey
	 */
	public function getApApiKey()
	{
		return $this->getFromCustomData('apApiKey');
	}
	
	/**
	 * @param int $itemHandlingLimit
	 */
	public function setApApiKey($apApiKey)
	{
		$this->putInCustomData('apApiKey', $apApiKey);
	}
}

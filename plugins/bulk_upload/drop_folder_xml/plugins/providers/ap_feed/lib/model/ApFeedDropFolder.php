<?php
/**
 * @package plugins.FeedDropFolder
 * @subpackage model
 */
class ApFeedDropFolder extends FeedDropFolder
{
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

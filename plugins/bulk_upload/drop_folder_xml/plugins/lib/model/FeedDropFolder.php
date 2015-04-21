<?php
/**
 * @package plugins.FeedDropFolder
 * @subpackage model
 */
class FeedDropFolder extends RemoteDropFolder 
{
	/**
	 * @return the $itemHandlingLimit
	 */
	public function getItemHandlingLimit() {
		return $this->getFromCustomData('itemHandlingLimit');
	}

	/**
	 * @return FeedItemInfo
	 */
	public function getFeedItemInfo() {
		return $this->getFromCustomData('feedItemInfo', null);
	}

	/**
	 * @param int $itemHandlingLimit
	 */
	public function setItemHandlingLimit($itemHandlingLimit) {
		$this->putInCustomData('itemHandlingLimit', $itemHandlingLimit);
	}

	/**
	 * @param FeedItemInfo $v
	 */
	public function setFeedItemInfo($v) {
		$this->putInCustomData('feedItemInfo', $v);
	}

	/* (non-PHPdoc)
	 * @see RemoteDropFolder::getRemoteFileTransferMgrType()
	 */
	protected function getRemoteFileTransferMgrType() {
		return kFileTransferMgrType::HTTP;
	}

	/* (non-PHPdoc)
	 * @see RemoteDropFolder::getImportJobData()
	 */
	public function getImportJobData() {
		//No need to implement - no import job will be created for this type of folder
	}

	/* (non-PHPdoc)
	 * @see RemoteDropFolder::getFolderUrl()
	 */
	public function getFolderUrl() {
		return $this->getPath();
		
	}

	
}
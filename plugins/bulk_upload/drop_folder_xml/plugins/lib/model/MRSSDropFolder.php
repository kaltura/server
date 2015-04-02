<?php
/**
 * @package plugins.dropFolder
 * @subpackage model
 */
class MRSSDropFolder extends RemoteDropFolder
{
	protected $mrssUrl;
	
	/**
	 * @return the $mrssUrl
	 */
	public function getMrssUrl() {
		return $this->mrssUrl;
	}

	/**
	 * @param field_type $mrssUrl
	 */
	public function setMrssUrl($mrssUrl) {
		$this->mrssUrl = $mrssUrl;
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
		return new kDropFolderImportJobData();
		
	}

	/* (non-PHPdoc)
	 * @see RemoteDropFolder::getFolderUrl()
	 */
	public function getFolderUrl() {
		return $this->getMrssUrl();		
	}

	
}
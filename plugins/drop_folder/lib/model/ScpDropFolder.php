<?php

/**
* @package plugins.dropFolder
* @subpackage model
*/
class ScpDropFolder extends SshDropFolder
{
    // ------------------------------------------
	// -- File Transfer Manager -----------------
	// ------------------------------------------
    
    /**
	 * @return kFileTransferMgr
	 */
	public function getFileTransferManager()
	{
	    return kFileTransferMgr::getInstance(kFileTransferMgrType::SCP);
	}
}
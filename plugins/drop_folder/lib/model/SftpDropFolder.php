<?php

/**
* @package plugins.dropFolder
* @subpackage model
*/
class SftpDropFolder extends SshDropFolder
{
    
	public function getFolderUrl()
	{
	    $url = 'sftp://';
	    if ($this->getSshUsername()) {
	        $url .= $this->getSshUsername();
	        if ($this->getSshPassword()) {
	            $url .= ':'.$this->getSshPassword();
	        }
	        $url .= '@';
	    }
	    $url .= $this->getSshHost();
	    $url .= '/'.$this->getPath();
	    return $url;
	}
	
	protected function getRemoteFileTransferMgrType()
	{
		if($this->getType() == DropFolderType::SFTP)
	    	return kFileTransferMgrType::SFTP;
	    if($this->getType() == DropFolderType::SFTP_CMD)
	    	return kFileTransferMgrType::SFTP_CMD;
		if($this->getType() == DropFolderType::SFTP_SEC_LIB)
	    	return kFileTransferMgrType::SFTP_SEC_LIB;	    	
	}
        
}
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
	    return kFileTransferMgrType::SFTP;
	}
        
}
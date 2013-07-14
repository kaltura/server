<?php

/**
* @package plugins.dropFolder
* @subpackage model
*/
class SftpDropFolder extends SshDropFolder
{   
	const  DEFAULT_SFTP_PORT = 22;
	
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
   	    if($this->getSshPort() && $this->getSshPort() != self::DEFAULT_SFTP_PORT)
	    	$url.=':'.$this->getSshPort();
	    
	    $url .= '/'.$this->getPath();
	    return $url;
	}
		
	protected function getRemoteFileTransferMgrType()
	{
	    return kFileTransferMgrType::SFTP;
	}
        
}
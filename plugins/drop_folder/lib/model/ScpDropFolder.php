<?php

/**
* @package plugins.dropFolder
* @subpackage model
*/
class ScpDropFolder extends SshDropFolder
{
	public function getFolderUrl()
	{
	    $url = 'scp://';
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
	    return kFileTransferMgrType::SCP;
	}
}
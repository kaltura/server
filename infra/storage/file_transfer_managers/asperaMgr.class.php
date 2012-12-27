<?php
/**
 * Extends the 'sftpMgr' class and implementing doPutFile doGetFile using Aspera.
 * For additional comments please look at the 'sftpMgr' class.
 * 
 * @package infra
 * @subpackage Storage
 */
class asperaMgr extends sftpMgr
{
	
	// upload a file to the server ising Aspera connection (ftp_mode is irrelevant)
	protected function doPutFile ($remote_file , $local_file)
	{
		$remote_file = ltrim($remote_file,'/');
		$cmd= $this->getCmdPrefix();
		$cmd.=" $local_file $this->user@$this->server:$remote_file";
		return $this->executeCmd($cmd);
	}
		
	// upload a file to the server ising Aspera connection (ftp_mode is irrelevant)
	protected function doGetFile ($remote_file, $local_file = null)
	{	
		$remote_file = ltrim($remote_file,'/');
		$cmd= $this->getCmdPrefix();
		$cmd.=" $this->user@$this->server:$remote_file $local_file";
		return $this->executeCmd($cmd);
	}
	
	private function getCmdPrefix(){
		$cmd = '';
		if ($this->privKeyFile){
			if ($this->passphrase)
				$cmd = "(echo $this->passphrase) | ascp ";
			else  
				$cmd = "ascp ";
		}
		else 
			$cmd = "(echo $this->pass) | ascp ";
		//creating folders on remote server
		$cmd.= " -d ";
		$cmd.=" -P $this->port ";
		if ($this->privKeyFile)
			$cmd.=" -i $this->privKeyFile ";
		return $cmd;
		
	}
	
	private function executeCmd($cmd){
		KalturaLog::debug('Executing command: '.$cmd);
		$return_value = null;
		$beginTime = time();
		system($cmd, $return_value);
		$duration = (time() - $beginTime)/1000;
		KalturaLog::debug("Execution took [$duration]sec with value [$return_value]");
		if ($return_value == 0)
			return true;
		return false;
	}
}

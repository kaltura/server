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
	protected function doPutFile ($remote_file , $local_file , $ftp_mode, $http_field_name = null, $http_file_name = null)
	{
		$remote_file = ltrim($remote_file,'/');
		$cmd="(echo $this->pass) | ascp";
		$cmd.=" -P $this->port";
		if ( $this->privKeyFile)
			$cmd.=" -i $this->privKeyFile";
		$cmd.=" $local_file $this->user@$this->server:$remote_file";
		KalturaLog::debug('Put file using command: '.$cmd);
		$return_value = null;
		$beginTime = time();
		system ( $cmd, $return_value );
		$duration = (time() - $beginTime)/1000;
		KalturaLog::debug("upload took [$duration]sec with value [$return_value]");
		return $return_value;
	}
		
	// upload a file to the server ising Aspera connection (ftp_mode is irrelevant)
	protected function doGetFile ($remote_file, $local_file, $ftp_mode)
	{	
		$remote_file = ltrim($remote_file,'/');
		$cmd="(echo $this->pass) | ascp";
		$cmd.=" -P $this->port";
		if ( $this->privKeyFile)
			$cmd.=" -i $this->privKeyFile";
		$cmd.=" $this->user@$this->server:$remote_file $local_file";
		KalturaLog::debug('Get file using command: '.$cmd);
		$return_value = null;
		$beginTime = time();
		system($cmd, $return_value);
		$duration = (time() - $beginTime)/1000;
		KalturaLog::debug("Download took [$duration]sec with value [$return_value]");
		return $return_value;
	}
	
}

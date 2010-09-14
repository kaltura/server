<?php
/**
 * Implements the IKaltuarLogger interface used by the KalturaClient for logging purposes and proxies the message to the KalturaLog 
 */
class Kaltura_ClientLoggingProxy implements IKalturaLogger
{
	public function log($msg)
	{
		KalturaLog::debug($msg);
	}
}
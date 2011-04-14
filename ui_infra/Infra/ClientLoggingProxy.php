<?php
/**
 * Implements the IKaltuarLogger interface used by the KalturaClient for logging purposes and proxies the message to the KalturaLog 
 */
class Infra_ClientLoggingProxy implements Kaltura_Client_ILogger
{
	public function log($msg)
	{
		KalturaLog::debug($msg);
	}
}
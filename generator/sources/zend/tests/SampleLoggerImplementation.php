<?php

class SampleLoggerImplementation implements Kaltura_Client_ILogger
{
	public function log($msg)
	{
		if (php_sapi_name() == 'cli')
			echo $msg.PHP_EOL;
		else
			echo $msg.'<br />';
	}
}

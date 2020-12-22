<?php

class kExecWrapper
{
	public static function exec($command, &$output, &$return_var)
	{
		KalturaLog::log("Executing [$command]");

		$startTime = microtime(true);
		$res = exec($command, $output, $return_var);
		KalturaMonitorClient::monitorExec($command, $startTime);
		return $res;
	}

	public static function system($command, &$return_var)
	{
		KalturaLog::log("Executing [$command]");

		$startTime = microtime(true);
		$res = system($command, $return_var);
		KalturaMonitorClient::monitorExec($command, $startTime);
		return $res;
	}

	public static function shell_exec($command)
	{
		KalturaLog::log("Executing [$command]");

		$startTime = microtime(true);
		$res = shell_exec($command);
		KalturaMonitorClient::monitorExec($command, $startTime);
		return $res;
	}

	public static function passthru($command)
	{
		KalturaLog::log("Executing [$command]");

		$startTime = microtime(true);
		$res = passthru($command);
		KalturaMonitorClient::monitorExec($command, $startTime);
		return $res;
	}
}

<?php

class kExecWrapper
{
	const CHUNK_SIZE = 1048576; // 1024 X 1024
	const CANT_ACCESS_FILE_ERROR_CODE = 2;
	const COMMAND_INVOKE_CANNOT_EXECUTE = 126;
	
	public static function exec($command, &$output, &$return_var)
	{
		KalturaLog::log("Executing exec [$command]");

		$startTime = microtime(true);
		$res = exec($command, $output, $return_var);
		KalturaMonitorClient::monitorExec($command, $startTime, $res === false ? 'ERROR' : '');
		return $res;
	}

	public static function system($command, &$return_var = null)
	{
		KalturaLog::log("Executing system [$command]");

		$startTime = microtime(true);
		$res = system($command, $return_var);
		KalturaMonitorClient::monitorExec($command, $startTime, $res === false ? 'ERROR' : '');
		return $res;
	}

	public static function shell_exec($command)
	{
		KalturaLog::log("Executing shell_exec [$command]");

		$startTime = microtime(true);
		$res = shell_exec($command);
		KalturaMonitorClient::monitorExec($command, $startTime);
		return $res;
	}

	public static function passthru($command)
	{
		KalturaLog::log("Executing passthru [$command]");

		$startTime = microtime(true);
		$res = passthru($command);
		KalturaMonitorClient::monitorExec($command, $startTime);
		return $res;
	}
	
	public static function runWrapped($command, $filePath)
	{
		KalturaLog::log("Executing runWrapped [$command] on file [$filePath]");
		
		$startTime = microtime(true);
		list($return_value, $output, $errorDescription)  = self::runWrappedCommand($command, $filePath);
		KalturaMonitorClient::monitorExec($command . " " . $filePath, $startTime, $return_value ? 'EXEC_' . $return_value : '');
		return array($return_value, $output, $errorDescription);
	}
	
	protected static function runWrappedCommand($command, $filePath)
	{
		$descriptorSpec = array(
			0 => array("pipe", "r"),    // stdin is a pipe that the child will read from
			1 => array("pipe", "w"),    // stdout is a pipe that the child will write to
			2 => array("pipe", "w/")    // stderr is a file to write to
		);
		
		$resolvedFilePath = kFile::realPath($filePath);
		$fd = self::openFile($resolvedFilePath);
		if(!$fd)
		{
			return array(self::CANT_ACCESS_FILE_ERROR_CODE, "", "Failed to open file [{$resolvedFilePath}]");
		}
		
		KalturaLog::info("Executing: [$command]");
		$process = proc_open( $command, $descriptorSpec, $pipes, "/tmp");
		if (!is_resource($process))
		{
			return array(self::COMMAND_INVOKE_CANNOT_EXECUTE, "", "Process returned by proc_open is not a valid resource");
		}
		
		if (function_exists('stream_set_chunk_size'))
		{
			stream_set_chunk_size($fd, self::CHUNK_SIZE);
		}
		
		//Set piped streams to be none blocking
		foreach ($pipes as $pipe)
		{
			stream_set_blocking($pipe, false);
		}
		
		$procOut = $procErr = "";
		$readBytes = $writeOffset = 0;
		$exitCode = $closedIn = $closedOut = $closedErr = false;
		
		while(!$closedOut && !$closedErr)
		{
			$processedBytes = false;
			$proc_status = proc_get_status($process);
			$isRunning = $proc_status['running'] == 1;
			$exitCode = ($exitCode === false && !$isRunning) ? $proc_status['exitcode'] : $exitCode;
			
			if (!$closedIn)
			{
				if ($writeOffset == $readBytes && !feof($fd))
				{
					$data = fread($fd, self::CHUNK_SIZE);
					$readBytes = strlen($data);
					$writeOffset = 0;
				}
				
				if ($writeOffset == $readBytes)
				{
					if (feof($fd))
					{
						fclose($pipes[0]);
						$closedIn = true;
					}
				}
				else
				{
					$res = fwrite($pipes[0], substr($data, $writeOffset));
					if($res === false)
					{
						fclose($pipes[0]);
						$closedIn = true;
					}
					elseif($res > 0)
					{
						$writeOffset += $res;
						$processedBytes = true;
					}
				}
			}
			
			if (!$closedOut)
			{
				$chunk = fread($pipes[1], self::CHUNK_SIZE);
				if ($chunk === false || $chunk == "" && !$isRunning)
				{
					fclose($pipes[1]);
					$closedOut = true;
				}
				elseif (strlen($chunk))
				{
					$processedBytes = true;
					$procOut .= $chunk;
				}
			}
			
			if (!$closedErr)
			{
				$chunk = fread($pipes[2], self::CHUNK_SIZE);
				if ($chunk === false || $chunk == "" && !$isRunning)
				{
					fclose($pipes[2]);
					$closedErr = true;
				}
				elseif (strlen($chunk))
				{
					$processedBytes = true;
					$procErr .= $chunk;
				}
			}
			
			if (!$processedBytes)
			{
				usleep(1e5);
			}
		}
		
		//When sigchild is used the return status from pclose and proc_close() cannot be retrieved.
		//To avoid -1 always being return we will check if stderr output is empty to return valid return_value
		$proc_status = proc_get_status($process);
		$exitCode = ($exitCode === false) ? $proc_status['exitcode'] : $exitCode;
		return array($exitCode, $procOut, $procErr);
	}
	
	protected static function openFile($filePath)
	{
		stream_wrapper_restore('http');
		stream_wrapper_restore('https');
		
		$fd = fopen($filePath, "rb");
		
		stream_wrapper_unregister('https');
		stream_wrapper_unregister('http');
		
		return $fd;
	}
}

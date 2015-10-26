<?php
/**
 * @package plugins.symantecScanEngine
 * @subpackage lib
 */
class SymantecScanDirectEngine extends SymantecScanEngine
{
	const SCAN_ENGINE_HOST = "127.0.0.1";
	const SCAN_ENGINE_PORT = 1344;

	protected $sleepBetweenScanRetries = 2;
	protected $maxScanRetries = 15;
	
	/**
	 * This function should be used to let the engine take specific configurations from the batch job parameters.
	 * For example - command line of the relevant binary file.
	 * @param unknown_type $paramsObject Object containing job parameters
	 */
	public function config($paramsObject)
	{
		if (isset($paramsObject->sleepBetweenScanRetries))
		{
			$this->sleepBetweenScanRetries = $paramsObject->sleepBetweenScanRetries;
		}
		
		if (isset($paramsObject->maxScanRetries))
		{
			$this->maxScanRetries = $paramsObject->maxScanRetries;
		}
		
		return true;
	}
	
	protected function sendCommandToScanEngine($command)
	{
		$engineSocket = socket_create(AF_INET, SOCK_STREAM, 0);
		if (!$engineSocket)
		{
			$errorCode = socket_last_error();
			KalturaLog::err("Failed to create socket: [$errorCode] " . socket_strerror($errorCode));
			return false;
		}

		if (!socket_connect($engineSocket, self::SCAN_ENGINE_HOST, self::SCAN_ENGINE_PORT))
		{
			$errorCode = socket_last_error($engineSocket);
			KalturaLog::err("Failed to connect to scan engine: [$errorCode] " . socket_strerror($errorCode));
			socket_close($engineSocket);
			return false;
		}

		$commandLen = strlen($command);
		if ($commandLen != socket_send($engineSocket, $command, $commandLen, 0))
		{
			$errorCode = socket_last_error($engineSocket);
			KalturaLog::err("Failed to send data to socket server: [$errorCode] " . socket_strerror($errorCode));
			socket_close($engineSocket);
			return false;
		}

		$recvBuffer = '';
		for (;;)
		{
			$recvChunk = '';
			$rcvdBytes = socket_recv($engineSocket, $recvChunk, 0x1000, 0);
			if ($rcvdBytes === false)
			{
				$errorCode = socket_last_error($engineSocket);
				KalturaLog::err("Failed to recv data from socket server: [$errorCode] " . socket_strerror($errorCode));
				break;
			}
			
			if ($rcvdBytes == 0)
			{
				break;
			}

			$recvBuffer .= $recvChunk;
		}

		socket_close($engineSocket);	
		return $recvBuffer;
	}
	
	/**
	 * Will execute the virus scan for the given file path and return the output from virus scanner program
	 * and the error description
	 * @param string $filePath
	 * @param boolean $cleanIfInfected
	 * @param string $errorDescription
	 */
	public function execute($filePath, $cleanIfInfected, &$output, &$errorDescription)
	{
		if (!file_exists($filePath))
		{
			$errorDescription = 'Source file does not exists ['.$filePath.']';
			return KalturaVirusScanJobResult::SCAN_ERROR;
		}
		
		$scanPolicy = $cleanIfInfected ? 'scanrepair' : 'scan';
		
		$scanCommand = 
"FILEMOD icap://" . self::SCAN_ENGINE_HOST . ":" . self::SCAN_ENGINE_PORT . "/SYMCScanResp-AV?action={$scanPolicy} ICAP/1.0
Host: 127.0.0.1:1344
X-Filepath: {$filePath}
Connection: close
Encapsulated: null-body=0

";
		
		for ($scanAttempts = 0; $scanAttempts < $this->maxScanRetries; sleep($this->sleepBetweenScanRetries))
		{
			$response = $this->sendCommandToScanEngine($scanCommand);
			if ($response === false)
			{
				continue;		// don't count this as an attempt, since the command wasn't sent to the server
			}
		
			KalturaLog::info("Buffer received from scan engine: $response");

			if (!kString::beginsWith($response, 'ICAP/1.0 '))
			{
				KalturaLog::err("Response does not start with ICAP/1.0");
				$scanAttempts++;
				continue;
			}
			
			$statusCode = explode(' ', $response);
			$statusCode = $statusCode[1];
			$statusCode = substr($statusCode, 0, 3);
			switch ($statusCode)
			{
			case '200':
			case '204':
				return KalturaVirusScanJobResult::FILE_IS_CLEAN;
				
			case '201':
				return KalturaVirusScanJobResult::FILE_WAS_CLEANED;

			case '205':
			case '403':
				return KalturaVirusScanJobResult::FILE_INFECTED;
				
			case '502':
				$errorDescription = 'Scan engine failed to access source file ['.$filePath.']';
				return KalturaVirusScanJobResult::SCAN_ERROR;		// no reason to retry
				
			case '539':
			case '558':
				$errorDescription = 'No virus scan license';
				return KalturaVirusScanJobResult::SCAN_ERROR;		// no reason to retry			
				
			default:	// incl: 500 - internal error
				KalturaLog::err("Got invalid scan status $statusCode");
				$scanAttempts++;				
				continue;
			}
		}
		
		return KalturaVirusScanJobResult::SCAN_ERROR;
	}
}

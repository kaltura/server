<?php
require_once( __DIR__ . '/WebexClientException.class.php');
require_once( __DIR__ . '/WebexFile.class.php');

/**
 * @package External
 * @subpackage WSDL
 */
class WebexClient extends nusoap_client
{
	const PARAM_TYPE_TIMESTAMP = 'xsd:dateTime';
	
	protected $httpResponseBoundary = null;
	protected $downloadDirectory = null;
	protected $downloadFilePath = array();
	protected $downloadFileResource = null;
	protected $downloadFileDescriptor = null;
	protected $returnBody = null;
	protected $returnHeaders = null;
	
	function __construct($wsdlUrl)
	{
		$GLOBALS['_transient']['static']['nusoap_base']['globalDebugLevel'] = 0;
		
		parent::__construct($wsdlUrl, 'wsdl');
		
		$this->setUseCURL(true);
		$this->setDownloadDirectory(sys_get_temp_dir());
	}

	public function setDownloadDirectory($downloadDirectory)
	{
		if(!is_dir($downloadDirectory))
			throw new WebexClientException("WebexClient error setting download directory, path [$downloadDirectory] is not a valid directory.");
			
		$this->downloadDirectory = $downloadDirectory;
	}

	public function setVerbose($verbose)
	{
		if($verbose)
		{
			$this->setDebugLevel(9);
			$this->setCurlOption(CURLOPT_VERBOSE, true);
		}
		else
		{
			$this->setDebugLevel(0);
			$this->setCurlOption(CURLOPT_VERBOSE, false);
		}
	}

	function appendDebug($string)
	{
		if ($this->debugLevel > 0)
			echo $string;

		return parent::appendDebug($string);
	}

	function parseParam($value, $type = null)
	{
		if($type == self::PARAM_TYPE_TIMESTAMP)
		{
			if(is_null($value))
				return null;
				
			return timestamp_to_iso8601($value);
		}
			
		if(is_null($value))
			return 'Null';
			
		return $value;
	}

	/**
	 * @return WebexFile
	 **/
	protected function download($operation, array $params = array())
	{
		$this->setCurlOption(CURLOPT_TIMEOUT, 60 * 60 * 24);
		$this->setCurlOption(CURLOPT_HEADER, false);
		$this->setCurlOption(CURLOPT_BINARYTRANSFER, true);
		
		$this->setCurlOption(CURLOPT_HEADERFUNCTION, array($this, 'curlHeaderCallback'));
		$this->setCurlOption(CURLOPT_WRITEFUNCTION, array($this, 'curlBodyCallback'));

		$this->returnHeaders = array();
		$this->call($operation, $params);
		$result = $this->parseResponse($this->returnHeaders, $this->returnBody);
		if (isset($result['faultstring']) && $result['faultstring'])
			throw new WebexClientException("WebexClient error calling operation: [".$this->operation."], error: ".$result['faultstring']);
		
		list($fileName, $expectedSize, $rest) = explode("\n", $this->downloadFileDescriptor, 3);
		
		$fileSize = filesize($this->downloadFilePath);
		if($expectedSize != $fileSize)
			throw new WebexClientException("WebexClient error calling operation: [".$this->operation."], error: Expected file size [$expectedSize] actual file size [$fileSize]");
			
		$file = new WebexFile();
		$file->setSize($fileSize);
		$file->setName($fileName);
		$file->setPath($this->downloadFilePath);
		
		return $file;
	}

	protected function doCall($operation, array $params = array(), $type = null)
	{
		$result = $this->call($operation, $params);
		$this->throwError();
		
		if($type)
			return new $type($result);
			
		return $result;
	}
	
	protected function throwError()
	{
		if ($this->getError())
			throw new WebexClientException("WebexClient error calling operation: [".$this->operation."], error: ".$this->getError());
	}

	public function curlHeaderCallback($ch, $data) 
	{ 
		$matches = null;
	    if (preg_match('/^Content-Type: .*boundary="([^"]+)"/i', $data, $matches)) 
	    {
	    	$this->httpResponseBoundary = $matches[1];
	    	$this->downloadFilePath = null;
			$this->downloadFileResource = null;
			$this->returnBody = '';
			$this->downloadFileDescriptor = null;
	    }
	
		$headerLines = explode("\r\n", $data);
		foreach($headerLines as $headerLine)
		{
			$headerParts = explode(':', $headerLine, 2);
			if(!isset($headerParts[1]))
				continue;
				
			$this->returnHeaders[strtolower(trim($headerParts[0]))] = trim($headerParts[1]);
		}
		
	    return strlen($data); 
	}
	
	public function curlBodyCallback($ch, $data) 
	{
		$delimiter = "\r\n--";
		$parts = explode($delimiter, $data);
		foreach($parts as $part)
			$this->handleContent($part);
			
	    return strlen($data); 
	}
	
	protected function handleContent($data) 
	{
		if(strpos($data, $this->httpResponseBoundary) === 0)
		{
			$data = substr($data, strlen($this->httpResponseBoundary));
			$parts = explode("\r\n\r\n", $data);
			
			if($this->downloadFileResource)
			{
				fclose($this->downloadFileResource);
				$this->downloadFileResource = null;
			}
				
			if(isset($parts[0]) && $parts[0] == "--\r\n")
				return;
				
			$headerLines = explode("\r\n", $parts[0]);
			$matches = null;
			foreach($headerLines as $headerLine)
			{
				if(strlen($this->returnBody) && preg_match('/^Content-Id:\s*<([^>]+)>$/', $headerLine, $matches))
				{
					if(is_null($this->downloadFileDescriptor))
					{
						$this->downloadFileDescriptor = '';
					}
					else 
					{
						$this->downloadFilePath = $this->downloadDirectory . DIRECTORY_SEPARATOR . $matches[1] . '.tmp';
						$this->downloadFileResource = fopen($this->downloadFilePath, 'wb');
					}
				}
			}
			
			if(!isset($parts[1]))
				return;
				
			$data = $parts[1];
		}
		
		if($this->downloadFileResource)
		{
			fwrite($this->downloadFileResource, $data);
		}
		elseif(is_null($this->downloadFileDescriptor))
		{
			$this->returnBody .= $data;
		}
		else
		{
			$this->downloadFileDescriptor .= $data;
		}
	}
}

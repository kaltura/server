<?php
require_once(__DIR__ . '/ActivitiClientException.php');
require_once(__DIR__ . '/ActivitiResponseType.php');

abstract class ActivitiClientBase
{
	protected $url = null;
	protected $debug = false;
	protected $protocol = 'http';
	protected $host = 'localhost';
	protected $port = 8080;
	protected $username = null;
	protected $password = null;
	protected $timeout = 240;
	
	private function buildUrl()
	{
		$this->url = $this->protocol . '://' . $this->host . ($this->port != 80 ? ":$this->port" : '') . '/activiti-rest/service/';
	}
	
	public function setCredentials($username, $password)
	{
		$this->username = $username;
		$this->password = $password;
		
		$this->buildUrl();
	}
	
	public function setUrl($host, $port = 8080, $protocol = 'http')
	{
		$this->host = $host;
		$this->port = $port;
		$this->protocol = $protocol;
		
		$this->buildUrl();
	}
	
	public function setDebug($debug)
	{
		$this->debug = $debug;
	}
	
	public function setTimeout($timeout)
	{
		$this->timeout = $timeout;
	}
	
	/**
	 * do an activiti request and return array
	 *
	 * @param string $url
	 * @param string $method GET POST PUT DELETE etc...
	 * @param array $data
	 * @return array
	 */
	protected function doRequest($url, $method, $data, $contentType = null, $filePath = null)
	{
		$url = $this->url . $url;
		
		if($this->debug)
		{
			echo "URL: $url\n";
			echo "Data: " . print_r($data, true) . "\n";
		}
		
		$c = curl_init();

		curl_setopt($c, CURLOPT_VERBOSE, $this->debug); 
		
		if($this->username && $this->password)
		{
			curl_setopt($c, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
			curl_setopt($c, CURLOPT_USERPWD, "$this->username:$this->password");
		}
		 
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($c, CURLOPT_USERAGENT, "tan-tan.activiti-api");
		curl_setopt($c, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($c, CURLOPT_HEADER, true);
		curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);

		if(is_array($data))
		{
			foreach($data as $key => $value)
			{
				if(is_bool($value))
					$data[$key] = $value ? 'true' : 'false';
			}
		}
					
		$headers = array(
			"X-HTTP-Method-Override: $method", 
			"Accept: application/json",
			"Content-type: application/json",
		);
		curl_setopt($c, CURLOPT_HTTPHEADER, $headers);
		
		switch($method)
		{
			case 'GET':
				curl_setopt($c, CURLOPT_HTTPGET, true);
				if(count($data))
					$url .= '?' . http_build_query($data);
				break;
				
			case 'POST':
				curl_setopt($c, CURLOPT_POST, true);
				
				if(count($data))
				{					
					$content = json_encode($data);
					if($this->debug)
					{
						echo "JSON: $content\n";
					}
					curl_setopt($c, CURLOPT_POSTFIELDS, $content);
				}
				break;
				
			case 'PUT':
				curl_setopt($c, CURLOPT_CUSTOMREQUEST, 'PUT');
				curl_setopt($c, CURLOPT_PUT, true);
				
				if(count($data))
				{		
					$content = json_encode($data);
					if($this->debug)
					{
						echo "JSON: $content\n";
					}
//					curl_setopt($c, CURLOPT_POSTFIELDS, $content);
				
					$fileName = tempnam(sys_get_temp_dir(), 'activitiPut');
					file_put_contents($fileName, $content);
	 
					$f = fopen($fileName, 'rb');
					curl_setopt($c, CURLOPT_INFILE, $f);
					curl_setopt($c, CURLOPT_INFILESIZE, strlen($content));
				}
				
				break;
		}

		curl_setopt($c, CURLOPT_URL, $url);
		curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);

		$response = curl_exec($c);
		
		curl_close($c);

		if($this->debug)
			echo "Response:\n$response\n";
		
		return $response;
	}

	public function request($url, $method, $data, $expectedHttpCodes, $errorHttpCodes, $returnType = null, $isArray = false)
	{
		$response = $this->doRequest($url, $method, $data);
		return $this->parseResponse($url, $response, $returnType, $isArray, $expectedHttpCodes, $errorHttpCodes);
	}
	
	public function parseResponse($url, $response, $returnType, $isArray, $expectedHttpCodes, $errorHttpCodes)
	{
		// parse response
		$header = false;
		$content = array();
		$status = null;
			
		foreach(explode("\r\n", $response) as $line)
		{
			if (strpos($line, 'HTTP/1.1') === 0)
			{
				$lineParts = explode(' ', $line);
				$status = intval($lineParts[1]);
				$header = true;
			}
			else if ($line == '') 
			{
				$header = false;
			}
			else if ($header) 
			{
				$line = explode(': ', $line);
				switch($line[0]) 
				{
					case 'Status': 
						$status = intval(substr($line[1], 0, 3));
						break;
				}
			} 
			else 
			{
				$content[] = $line;
			}
		}

		if(is_null($status))
		{
			throw new ActivitiClientException("No valid response accepted, URL [$url]", ActivitiClientException::NO_VALID_RESPONSE);
		}
		
		if(in_array($status, $expectedHttpCodes))
		{
			$response = implode("\n", $content);
			if(!trim($response))
				return;
				
			$response = json_decode($response);
			if($returnType)
			{
				if($isArray)
				{
					return ActivitiResponseObject::fromArray($response, $returnType);
				}
				return new $returnType($response);
			}
			return;	
		}
		
		if($response && json_decode(implode("\n", $content)))
		{
			$error = json_decode(implode("\n", $content));
			if(isset($error->statusCode) && isset($error->errorMessage))
			{
				throw new ActivitiClientException("Status [{$error->statusCode}], URL [$url]: {$error->errorMessage}", ActivitiClientException::INVALID_HTTP_CODE);
			}
		}

		if(isset($errorHttpCodes[$status]))
		{
			throw new ActivitiClientException("Status [$status], URL [$url]: " . $errorHttpCodes[$status], ActivitiClientException::INVALID_HTTP_CODE);
		}

		if(ActivitiResponseType::isExpectedCode($status))
		{
			throw new ActivitiClientException("Status [$status], URL [$url]: " . ActivitiResponseType::getCodeDescription($status), ActivitiClientException::INVALID_HTTP_CODE);
		}
		
		throw new ActivitiClientException("Unexpected status [$status], URL [$url]", ActivitiClientException::INVALID_HTTP_CODE);
	}
}

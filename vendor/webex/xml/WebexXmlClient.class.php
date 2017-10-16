<?php
require_once(__DIR__ . '/WebexXmlSecurityContext.class.php');
require_once(__DIR__ . '/WebexXmlResponse.class.php');
require_once(__DIR__ . '/WebexXmlRequest.class.php');
require_once(__DIR__ . '/WebexXmlException.class.php');

class WebexXmlClient
{
	/**
	 * URL to webex XML end point
	 * @var string
	 */
	protected $url;
	
	/**
	 * @var boolean
	 */
	protected $verbose;
	
	/**
	 * @var WebexXmlSecurityContext
	 */
	protected $securityContext;
	
	public function __construct($url, WebexXmlSecurityContext $securityContext)
	{
		$this->url = $url;
		$this->validateNoBackup();
		$this->securityContext = $securityContext;
	}

	protected function isRunningOnBackupSite()
	{
		$url = "{$this->url}/webex/gsbstatus.php";
		return (trim(@file_get_contents($url)) == 'TRUE');
	}

	private function validateNoBackup()
	{
		if($this->isRunningOnBackupSite())
		{
			throw new WebexXmlException ('Cannot run on backup.');
		}
	}

	/**
	 * @param boolean $verbose
	 */
	public function setVerbose($verbose)
	{
		$this->verbose = $verbose;
	}

	/**
	 * @param WebexXmlRequestBodyContent $requestBodyContent
	 * @throws WebexXmlException
	 * @return WebexXmlResponseBodyContent
	 */
	public function send(WebexXmlRequestBodyContent $requestBodyContent)
	{
		$this->validateNoBackup();
		$request = new WebexXmlRequest($this->securityContext, $requestBodyContent);
		$response = $this->doSend($request);
		
		if($response->getHeader()->getResponse()->getResult() != WebexXmlHeaderResponse::RESULT_SUCCESS)
			throw new WebexXmlException("Status: " . $response->getHeader()->getResponse()->getResult() . ", Reason: " . $response->getHeader()->getResponse()->getReason(), $response->getHeader()->getResponse()->getExceptionID());
			
		return $response->getBody()->getBodyContent();
	}
	
	/**
	 * @param WebexXmlSecurityContext $securityContext
	 * @param string $contentType
	 * @return WebexXmlResponse
	 */
	protected function doSend(WebexXmlRequest $request)
	{	
		$xml = strval($request);
		
		if($this->verbose)
			echo "Request: \n$xml\n";
			
		$ch = curl_init($this->url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_VERBOSE, $this->verbose);
		
		$response = curl_exec($ch);
		if(!$response)
		{
			$exception = new WebexXmlException(curl_error($ch));
			curl_close($ch);
			throw $exception;
		}
		curl_close($ch);
		
		// strip namespaces
		$response = preg_replace('/<(\\/?)[^:]+:([^\s>]+)/', '<$1$2', $response);
		
		if($this->verbose)
			echo "Response: \n$response\n";
		
		return new WebexXmlResponse(new SimpleXMLElement($response), $request->getContentType());
	}
}

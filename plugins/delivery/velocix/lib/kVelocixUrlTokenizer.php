<?php
class kVelocixUrlTokenizer extends kUrlTokenizer
{

	protected $protocol = "http";
	protected $streamName;
	protected $hdsPaths;
	protected $paramName;
	protected $authPrefix;
	
	/**
	 * @param string $url
	 * @return string
	 */
	public function tokenizeSingleUrl($path)
	{
		if (strstr($path, $this->authPrefix) === false)
			return $path;
		$token = $this->getToken($path);
		return $path."?$this->paramName=$token";
	}

	private function getToken($path) {
		$path_parts = pathinfo($path);
		$path  = preg_replace('/'.$path_parts['basename'].'/', '*', $path);
		// work out the expiry in Unix epoch seconds
		$t_expiry = time() + $this->window;
		// URL encode the parameters
		$message = "pathURI=" . rawurlencode($path);
		$paths = explode(",", $this->hdsPaths);
		
		if ($this->protocol == 'hds' && count($paths))
		{
			foreach ($paths as $path){
				$path =  preg_replace('/@STREAM_NAME@/', $this->streamName, $path);
				$message.= "&pathURI=" . rawurlencode($path);
			}
		}
		$message .= "&expiry=" . rawurlencode($t_expiry);
		$message .= "&random=" . uniqid();
		// Get the HMAC in hex using the default hash function (SHA-256)
		$hmac = hash_hmac("sha256", $message, $this->key, false);
		// Concatenate the HMAC to the end of the path and Base64 encode.
		$encoded = base64_encode("{$message},{$hmac}");
		return $encoded;
	}
	
	/**
	 * @return the $protocol
	 */
	public function getProtocol() {
		return $this->protocol;
	}

	/**
	 * @return the $streamName
	 */
	public function getStreamName() {
		return $this->streamName;
	}

	/**
	 * @return the $hdsPaths
	 */
	public function getHdsPaths() {
		return $this->hdsPaths;
	}

	/**
	 * @return the $paramName
	 */
	public function getParamName() {
		return $this->paramName;
	}

	/**
	 * @param field_type $protocol
	 */
	public function setProtocol($protocol) {
		$this->protocol = $protocol;
	}

	/**
	 * @param field_type $streamName
	 */
	public function setStreamName($streamName) {
		$this->streamName = $streamName;
	}

	/**
	 * @param field_type $hdsPaths
	 */
	public function setHdsPaths($hdsPaths) {
		$this->hdsPaths = $hdsPaths;
	}

	/**
	 * @param field_type $tokenParamName
	 */
	public function setParamName($paramName) {
		$this->paramName = $paramName;
	}
	
	/**
	 * @return the $authPrefix
	 */
	public function getAuthPrefix() {
		return $this->authPrefix;
	}

	/**
	 * @param field_type $authPrefix
	 */
	public function setAuthPrefix($authPrefix) {
		$this->authPrefix = $authPrefix;
	}


		
	
}
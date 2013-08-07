<?php
class kVelocixUrlTokenizer extends kUrlTokenizer
{

	protected $window;
	protected $secret;
	protected $protocol;
	protected $streamName;
	protected $hdsPaths;
	protected $tokenParamName;
	
	/**
	 * @param int $window
	 * @param string $secret
	 * @param array $protocol
	 */
	public function __construct($window, $secret, $protocol, $streamName, $hdsPaths, $tokenParamName)
	{
		$this->window = $window;
		$this->secret = $secret;
		$this->protocol = $protocol == 'applehttp' ? 'hls' : $protocol;
		$this->streamName = $streamName;
		$this->hdsPaths = $hdsPaths;
		$this->tokenParamName = $tokenParamName;
	}
	
	/**
	 * @param string $url
	 * @return string
	 */
	public function tokenizeSingleUrl($path)
	{
		$token = $this->getToken($path);
		return $path."?$this->tokenParamName=$token";
	}

	private function getToken($path) {
		$path_parts = pathinfo($path);
		$path  = preg_replace('/'.$path_parts['basename'].'/', '*', $path);
		// work out the expiry in Unix epoch seconds
		$t_expiry = time() + $this->window;
		// URL encode the parameters
		$message = "pathURI=" . rawurlencode($path);
		if ($this->protocol == 'hds')
		{
			foreach ($this->hdsPaths as $path){
				$path =  preg_replace('/@STREAM_NAME@/', $this->streamName, $path);
				$message.= "&pathURI=" . rawurlencode($path);
			}
		}
		$message .= "&expiry=" . rawurlencode($t_expiry);
		$message .= "&random=" . uniqid();
		// Get the HMAC in hex using the default hash function (SHA-256)
		$hmac = hash_hmac("sha256", $message, $this->secret, false);
		// Concatenate the HMAC to the end of the path and Base64 encode.
		$encoded = base64_encode("{$message},{$hmac}");
		return $encoded;
	}
		
	
}
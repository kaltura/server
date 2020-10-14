<?php
/**
 *
 * @package Core
 * @subpackage model
 */

class kKalturaUrlRecognizer extends kUrlRecognizer
{

	/**
	 * @var string
	 */
	protected $secret;

	/**
	 * @return string
	 */
	public function getSecret()
	{
		return $this->secret;
	}

	/**
	 * @param string $secret
	 */
	public function setSecret($secret)
	{
		$this->secret = $secret;
	}

	public function isRecognized($requestOrigin)
	{
		$uri = $_SERVER["REQUEST_URI"];
		$uriParts = explode('/sig/', $uri);
		if(count($uriParts) !== 2)
		{
			return false;
		}
		$endingParts = explode('/', $uriParts[1]);
		if(count($endingParts) > 2)
		{
			return false;
		}

		$requestSignature =  $endingParts[0];
		$pathEnding = '';
		if(count($endingParts) == 2)
		{
			$pathEnding = '/' . $endingParts[1];
		}

		$nonSignedUri = $uriParts[0] . $pathEnding;
		$calculatedSignature = urlencode(base64_encode(hash_hmac('sha256', $nonSignedUri, $this->secret)));

		if($calculatedSignature !== $requestSignature)
		{
			return false;
		}

		return true;
	}

}
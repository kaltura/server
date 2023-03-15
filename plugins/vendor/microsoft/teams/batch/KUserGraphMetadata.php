<?php


class KUserGraphMetadata
{
	const CSRF_CIPHER_METHOD = "AES-256-CBC";
	const MICROSOFT_GRAPH_BEARER_TOKEN = 'microsoftGraphBearerToken';
	const MICROSOFT_GRAPH_REFRESH_TOKEN = 'microsoftGraphRefreshToken';
	const MICROSOFT_GRAPH_TOKEN_EXPIRY = 'microsoftGraphTokenExpiry';
	/**
	 * @var string
	 */
	public $deltaToken;

	/**
	 * @var string
	 */
	public $authToken;

	/**
	 * @var string
	 */
	public $refreshToken;

	/**
	 * @var time
	 */
	public $authTokenExpiry;

	/**
	 * @var time
	 */
	public $lastAccessed;

	/**
	 * @var string
	 */
	public $recordingType;

	/**
	 * @var string
	 */
	public $encryptionKey;

	public function __construct($userMetadataXml, $encryptionKey)
	{
		$metadataObject = new SimpleXMLElement($userMetadataXml);
		if (empty($metadataObject->xpath('.//GraphAuthData')[0]))
		{
			throw new Exception('Cannot instantiate user graph data - auth data missing');
		}

		$this->encryptionKey = $encryptionKey;
		$decryptedValue = unserialize($this->decryptAuthInfo(strval($metadataObject->xpath('.//GraphAuthData')[0])));
		if (empty($decryptedValue[self::MICROSOFT_GRAPH_BEARER_TOKEN]) ||
			empty($decryptedValue[self::MICROSOFT_GRAPH_REFRESH_TOKEN]) ||
			empty($decryptedValue[self::MICROSOFT_GRAPH_TOKEN_EXPIRY]))
		{
			throw new Exception('Cannot instantiate user graph data - auth data missing');
		}

		$this->authToken = $decryptedValue[self::MICROSOFT_GRAPH_BEARER_TOKEN];
		$this->refreshToken = $decryptedValue[self::MICROSOFT_GRAPH_REFRESH_TOKEN];
		$this->authTokenExpiry = $decryptedValue[self::MICROSOFT_GRAPH_TOKEN_EXPIRY];

		$this->deltaToken = !empty($metadataObject->xpath('.//DeltaToken')[0]) ? strval($metadataObject->xpath('.//DeltaToken')[0]) : null;
		$this->lastAccessed = !empty($metadataObject->xpath('.//LastAccessed')[0]) ? intval($metadataObject->xpath('.//LastAccessed')[0]) : null;
		$this->recordingType = !empty($metadataObject->xpath('.//RecordingType')[0]) ? intval($metadataObject->xpath('.//RecordingType')[0]) : null;
	}

	public function getXmlFormatted()
	{
		$metadataObject = new SimpleXMLElement('<metadata/>');
		$metadataObject->addChild('GraphAuthData', $this->encryptAuthInfo());
		$metadataObject->addChild('DeltaToken', $this->deltaToken);
		$metadataObject->addChild('LastAccessed', $this->lastAccessed);
		$metadataObject->addChild('RecordingType', $this->recordingType);
		$metadataObject->addChild('HasOptIn', 'True');

		return $metadataObject->saveXML();
	}

	protected function encryptAuthInfo()
	{
		$message = serialize(array(
			self::MICROSOFT_GRAPH_BEARER_TOKEN => $this->authToken,
			self::MICROSOFT_GRAPH_REFRESH_TOKEN => $this->refreshToken,
			self::MICROSOFT_GRAPH_TOKEN_EXPIRY => $this->authTokenExpiry
		));

		$iv = openssl_random_pseudo_bytes(16);
		$value = openssl_encrypt(
			$message,
			self::CSRF_CIPHER_METHOD, $this->encryptionKey, 0, $iv
		);
		if ($value === false) {
			throw new Exception('Could not encrypt the data.');
		}
		$mac = self::hash($iv = base64_encode($iv), $value, $this->encryptionKey);
		$json = json_encode(compact('iv', 'value', 'mac'));

		return base64_encode($json);
	}

	protected function decryptAuthInfo($encryptedValue)
	{
		$payload = $this->getJsonPayload($encryptedValue, $this->encryptionKey);
		$iv = base64_decode($payload['iv']);
		return openssl_decrypt($payload['value'], self::CSRF_CIPHER_METHOD, $this->encryptionKey, 0, $iv);
	}

	protected function getJsonPayload($payload, $key)
	{
		$payload = json_decode(base64_decode($payload), true);
		if (! self::validPayload($payload)) {
			throw new Exception('The payload is invalid.');
		}
		if (! self::validMac($payload, $key)) {
			throw new Exception('The MAC is invalid.');
		}
		return $payload;
	}

	/**
	 * Calculate the hash of the given payload.
	 *
	 * @param  array  $payload
	 * @param  string  $bytes
	 * @param  string  $key
	 * @return string
	 */
	protected function calculateMac($payload, $bytes, $key)
	{
		return hash_hmac(
			'sha256', self::hash($payload['iv'], $payload['value'], $key), $bytes, true
		);
	}

	/**
	 * Verify that the encryption MAC is valid.
	 *
	 * @param  array  $payload
	 * @param  string  $key
	 * @return bool
	 */
	protected function validMac($payload, $key)
	{
		$calculated = self::calculateMac($payload, $bytes = openssl_random_pseudo_bytes(16), $key);
		return hash_equals(
			hash_hmac('sha256', $payload['mac'], $bytes, true), $calculated
		);
	}

	/**
	 * Verify that the encryption payload is valid.
	 *
	 * @param  mixed  $payload
	 * @return bool
	 */
	protected function validPayload($payload)
	{
		return is_array($payload) && isset($payload['iv'], $payload['value'], $payload['mac']);
	}

	protected static function hash($iv, $value, $key)
	{
		return hash_hmac('sha256', $iv.$value, $key);
	}
}
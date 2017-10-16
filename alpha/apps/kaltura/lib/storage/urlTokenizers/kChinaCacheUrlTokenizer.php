<?php
class kChinaCacheUrlTokenizer extends kUrlTokenizer
{
	const SHA1 = 1;
	
	const SHA256 = 2;
	/**
	 * @var int
	 */
	protected $algorithmId;
	
	/**
	 * @var int
	 */
	protected $keyId;
	
	/**
	 * @param string $url
	 * @param string $urlPrefix
	 * @return string
	 */
	public function tokenizeSingleUrl($url, $urlPrefix = null)
	{
		return $this->tokenizeUrl($url);
	}
	
	/**
	 * @param string $url
	 * @param string $baseUrl
	 * @param string $fileExtension
	 * @return string
	 */
	public function tokenizeUrl($url)
	{
		$expiryTime = time() + $this->window;

		$hashit = dirname($url) . "/";
		$hashData = $hashit . $expiryTime;
		
		if($this->algorithmId == self::SHA1)
		{
			$token = urlencode(base64_encode(hash_hmac("sha1", $hashData, $this->key, true)));
		}
		elseif ($this->algorithmId == self::SHA256)
		{
			$token = urlencode(base64_encode(hash_hmac("sha256", $hashData, $this->key, true)));
		}
		else {
			KalturaLog::info("Illegal algorithm ID value!");
			return;
		}
		
		if (strpos($url, '?') !== false)
			$s = '&';
		else
			$s = '?';
		
		return $url.$s.'ACL=' . urlencode($hashit) . '&P1='.$expiryTime.'&P2='.$this->keyId . "&P3=" . $this->algorithmId . "&P4=" . $token;
	}
	
	/**
	 * @return int
	 */
	public function getAlgorithmId() {
		return $this->algorithmId;
	}

	/**
	 * @param int $v
	 */
	public function setAlgorithmId($v) {
		$this->algorithmId = $v;
	}
	
	/**
	 * @return int
	 */
	public function getKeyId() {
		return $this->keyId;
	}

	/**
	 * @param int $v
	 */
	public function setKeyId($v) {
		$this->keyId = $v;
	}
}
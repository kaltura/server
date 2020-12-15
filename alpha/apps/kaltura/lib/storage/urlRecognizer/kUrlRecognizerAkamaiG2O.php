<?php

/**
 *
 * @package Core
 * @subpackage model
 */ 
class kUrlRecognizerAkamaiG2O extends kUrlRecognizer
{
	/**
	 * @var string
	 */
	protected $headerData;
	
	/**
	 * @var string
	 */
	protected $headerSign;
	
	/**
	 * @var int
	 */
	protected $timeout;
	
	/**
	 * @var string
	 */
	protected $salt;
	
	/**
	 * @return the $headerData
	 */
	public function getHeaderData() {
		return $this->headerData;
	}

	/**
	 * @return the $headerSign
	 */
	public function getHeaderSign() {
		return $this->headerSign;
	}

	/**
	 * @return the $timeout
	 */
	public function getTimeout() {
		return $this->timeout;
	}

	/**
	 * @return the $salt
	 */
	public function getSalt() {
		return $this->salt;
	}

	/**
	 * @param string $headerData
	 */
	public function setHeaderData($headerData) {
		$this->headerData = $headerData;
	}

	/**
	 * @param string $headerSign
	 */
	public function setHeaderSign($headerSign) {
		$this->headerSign = $headerSign;
	}

	/**
	 * @param number $timeout
	 */
	public function setTimeout($timeout) {
		$this->timeout = $timeout;
	}

	/**
	 * @param string $salt
	 */
	public function setSalt($salt) {
		$this->salt = $salt;
	}

	public function isRecognized($requestOrigin) {
		$url = $_SERVER["SCRIPT_URL"];
		$authData = @$_SERVER[$this->headerData];
		$authSign = @$_SERVER[$this->headerSign];

		list($version, $ghostIp, $clientIp, $time, $uniqueId, $nonce) = explode(",", $authData);
		if ($this->timeout) {
			// Compare the absolute value of the difference between the current time
			// and the "token" time.
			if (abs(time() - $time) > $this->timeout ) {
				return self::NOT_RECOGNIZED;
			}
		}

		$newSign = base64_encode(md5($this->timeout . $authData . $url, true));
		if ($newSign == $authSign) {
			return self::RECOGNIZED_OK;
		}
		
		return self::NOT_RECOGNIZED;
	}
	
}

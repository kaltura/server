<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kPlaybackRestriction {

	/**
	 * @var string
	 */
	private $code;

	/**
	 * @var string
	 */
	private $message;

	public function __construct($code = null, $message  = null)
	{
		$this->code = $code;
		$this->message = $message;
	}

	/**
	 * @return string
	 */
	public function getMessage()
	{
		return $this->message;
	}

	/**
	 * @return string
	 */
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * @param string
	 */
	public function setMessage($message)
	{
		return $this->message = $message;
	}

	/**
	 * @param string
	 */
	public function setCode($code)
	{
		return $this->code = $code;
	}

}
<?php

class kAuthentication
{
	/**
	 * @var string
	 */
	protected $qrCode;

	/**
	 * @return string
	 */
	public function getQrCode()
	{
		return $this->qrCode;
	}

	/**
	 * @param string $QrCode
	 */
	public function setQrCode($QrCode)
	{
		$this->qrCode = $QrCode;
	}
}
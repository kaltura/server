<?php


class kFormatField
{
	/**
	 * @var string
	 */
	public $format;

	/**
	 * @return string
	 */
	public function getFormat() {
		return $this->format;
	}

	/**
	 * @param string $format
	 */
	public function setFormat($format) {
		$this->format = $format;
	}
}

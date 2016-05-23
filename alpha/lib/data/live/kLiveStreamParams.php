<?php

/**
 * Live stream recording entry configuration object
 *
 * @package Core
 * @subpackage model
 *
 */
class kLiveStreamParams
{
	/**
	 * @var int
	 */
	protected $bitrate;

	/**
	 * @var string
	 */
	protected $flavorId;

	/**
	 * @var int
	 */
	protected $width;

	/**
	 * @var int
	 */
	protected $height;

	/**
	 * @var string
	 */
	protected $codec;

	/**
	 * @return the $bitrate
	 */
	public function getBitrate() { return $this->bitrate; }
	/**
	 * @param int $bitrate
	 */
	public function setBitrate($bitrate) { $this->bitrate = $bitrate; }

	/**
	 * @return the $flavorId
	 */
	public function getFlavorId() { return $this->flavorId; }

	/**
	 * @param string $flavorId
	 */
	public function setFlavorId($flavorId) { $this->flavorId = $flavorId; }

	/**
	 * @return the $width
	 */
	public function getWidth() { return $this->width; }

	/**
	 * @param int $width
	 */
	public function setWidth($width) { $this->width = $width; }

	/**
	 * @return the $height
	 */
	public function getHeight() { return $this->height; }

	/**
	 * @param int $height
	 */
	public function setHeight($height) { $this->height = $height; }

	/**
	 * @return the $codec
	 */
	public function getCode() { return $this->codec; }

	/**
	 * @param string $codec
	 */
	public function setCode($codec) { $this->codec = $codec; }
}
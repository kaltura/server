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
	 * @var int
	 */
	protected $frameRate;
	
	/**
	 * @var float
	 */
	protected $keyFrameInterval;
	
	/**
	 * @var string
	 */
	protected $language;

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
	public function getCodec() { return $this->codec; }

	/**
	 * @param string $codec
	 */
	public function setCodec($codec) { $this->codec = $codec; }
	
	/**
	 * @return the $frameRate
	 */
	public function getFrameRate() { return $this->frameRate; }
	
	/**
	 * @param int $frameRate
	 */
	public function setFrameRate($frameRate) { $this->frameRate = $frameRate; }
	
	/**
	 * @return the $keyFrameInterval
	 */
	public function getKeyFrameInterval() { return $this->keyFrameInterval; }
	
	/**
	 * @param float $keyFrameInterval
	 */
	public function setKeyFrameInterval($keyFrameInterval) { $this->keyFrameInterval = $keyFrameInterval; }
	
	/**
	 * @return the $language
	 */
	public function getLanguage() { return $this->language; }
	/**
	 * @param string $language
	 */
	public function setLanguage($language) { $this->language = $language; }
}
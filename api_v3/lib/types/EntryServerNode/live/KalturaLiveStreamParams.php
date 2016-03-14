<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaLiveStreamParams extends KalturaObject {

	/**
	 * Bit rate of the stream. (i.e. 900)
	 * @var int
	 */
	public $bitrate;

	/**
	 * flavor asset id
	 * @var string
	 */
	public $flavorId;

	/**
	 * Stream's width
	 * @var int
	 */
	public $width;

	/**
	 * Stream's height
	 * @var int
	 */
	public $height;

	/**
	 * Live stream's codec
	 * @var string
	 */
	public $codec;

}
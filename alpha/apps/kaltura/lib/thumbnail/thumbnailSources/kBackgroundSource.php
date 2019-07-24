<?php
/**
 * @package core
 * @subpackage thumbnail.thumbnailSources
 */

class kBackgroundSourceSource extends kThumbnailSource
{
	const DEFAULT_FORMAT = 'png';

	protected $imagick;

	public function  __construct($width, $height, $backgroundColor, $format = self::DEFAULT_FORMAT)
	{
		$this->imagick = new imagick();
		$this->imagick->newImage($width, $height, $backgroundColor, $format);
	}

	public function getImage()
	{
		return $this->imagick;
	}
}
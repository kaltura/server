<?php

/**
 * @package infra
 * @subpackage Media
 */
class kThumbnailDescriptor
{
	private $deltaFromOrigAspectRatio;
	private $width;
	private $height;
	private $imageFilePath;
	private $isDefault;

	public function kThumbnailDescriptor($requiredAspectRatio, $thumbWidth, $thumbHeight, $thumbPath, $isDefault)
	{
		$thumbAspectRatio = $thumbWidth / $thumbHeight;
	
		$this->deltaFromOrigAspectRatio = abs( $requiredAspectRatio - $thumbAspectRatio );
		$this->width = $thumbWidth;
		$this->height = $thumbHeight;
		$this->imageFilePath = $thumbPath;
		$this->isDefault = $isDefault ? 1 : 0;
	}
	
	public function getDeltaFromOrigAspectRatio() { return $this->deltaFromOrigAspectRatio; }
	public function getWidth() { return $this->width; }
	public function getHeight() { return $this->height; }
	public function getImageFilePath() { return $this->imageFilePath; }
	public function getIsDefault() { return $this->isDefault; }
}

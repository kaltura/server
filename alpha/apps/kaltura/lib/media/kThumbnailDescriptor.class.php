<?php

/**
 * @package infra
 * @subpackage Media
 */
class kThumbnailDescriptor
{
	private static $requiredWidth;
	private static $requiredHeight;
	private static $requiredAspectRatio;
	private static $initialized = false;

	private $deltaFromOrigAspectRatio;
	private $width;
	private $height;
	private $imageFilePath;
	private $isDefault;

	public static function initDimensions( $requiredWidth, $requiredHeight )
	{
		self::$requiredWidth = $requiredWidth;
		self::$requiredHeight = $requiredHeight;
		self::$requiredAspectRatio = $requiredWidth / $requiredHeight;
		self::$initialized = true;
	}

	public static function fromParams( $thumbWidth, $thumbHeight, $thumbPath = null, $isDefault = false )
	{
		if ( ! self::$initialized )
		{
			throw new kCoreException("kThumbnailDescriptor was not yet initialized");
		}

		$descriptor = new kThumbnailDescriptor();

		$thumbAspectRatio = $thumbWidth / $thumbHeight;

		$descriptor->deltaFromOrigAspectRatio = abs( self::$requiredAspectRatio - $thumbAspectRatio );
		$descriptor->width = $thumbWidth;
		$descriptor->height = $thumbHeight;
		$descriptor->imageFilePath = $thumbPath;
		$descriptor->isDefault = $isDefault ? 1 : 0;

		return $descriptor;
	}

	public static function fromThumbAsset( $thumbAsset )
	{
		$fileSyncKey = $thumbAsset->getSyncKey( asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET );

		$thumbPath = kFileSyncUtils::getReadyLocalFilePathForKey( $fileSyncKey );

		$thumbWidth = $thumbAsset->getWidth();
		$thumbHeight = $thumbAsset->getHeight();

		return self::fromParams( $thumbWidth, $thumbHeight, $thumbPath, false );
	}

	public static function getRequiredWidth() { return self::$requiredWidth; }
	public static function getRequiredHeight() { return self::$requiredHeight; }
	public static function getRequiredAspectRatio() { return self::$requiredAspectRatio; }
	
	public function getDeltaFromOrigAspectRatio() { return $this->deltaFromOrigAspectRatio; }
	public function getWidth() { return $this->width; }
	public function getHeight() { return $this->height; }
	public function getImageFilePath() { return $this->imageFilePath; }
	public function getIsDefault() { return $this->isDefault; }
}

<?php
/**
 * @package plugins.thumbnail
 * @subpackage model.sourceAction
 */

class vidSecAction extends sourceAction
{
	protected $second;
	protected $newWidth;
	protected $newHeight;

	protected $parameterAlias = array(
		"sec" => kThumbnailParameterName::SECOND,
		"s" => kThumbnailParameterName::SECOND,
		"w" => kThumbnailParameterName::WIDTH,
		"h" => kThumbnailParameterName::HEIGHT,
	);

	protected function extractActionParameters()
	{
		$this->second = $this->getFloatActionParameter(kThumbnailParameterName::SECOND, 0);
		$this->newWidth = $this->getIntActionParameter(kThumbnailParameterName::WIDTH);
		$this->newHeight = $this->getIntActionParameter(kThumbnailParameterName::HEIGHT);
	}

	protected function validateInput()
	{
		if(!is_a($this->source, "entrySource"))
		{
			throw new KalturaAPIException(KalturaThumbnailErrors::BAD_QUERY, "Vid sec can only work on entry source");
		}

		if(!is_numeric($this->second) || $this->second < 0)
		{
			throw new KalturaAPIException(KalturaThumbnailErrors::BAD_QUERY, "Vid sec second cant be negative");
		}

		if($this->source->getEntryMediaType() != entry::ENTRY_MEDIA_TYPE_VIDEO)
		{
			throw new KalturaAPIException(KalturaThumbnailErrors::MUST_HAVE_VIDEO_SOURCE);
		}

		$this->validateDimensions();
		$this->validatePermissions();
	}

	protected function validateDimensions()
	{
		if($this->newWidth && (!is_numeric($this->newWidth) || $this->newWidth < self::MIN_DIMENSION || $this->newWidth > self::MAX_DIMENSION))
		{
			throw new KalturaAPIException(KalturaThumbnailErrors::BAD_QUERY, 'width must be between 0 and 10000');
		}

		if($this->newHeight && (!is_numeric($this->newHeight) || $this->newHeight < self::MIN_DIMENSION || $this->newHeight > self::MAX_DIMENSION))
		{
			throw new KalturaAPIException(KalturaThumbnailErrors::BAD_QUERY, 'height must be between 0 and 10000');
		}
	}

	protected function validatePermissions()
	{
		thumbnailSecurityHelper::verifyEntryAccess($this->source->getEntry());

	}

	protected function getTempThumbnailPath()
	{
		$dc = kDataCenterMgr::getCurrentDc();
		$filePath = $dc["id"].'_'.kString::generateStringId();
		return sys_get_temp_dir().DIRECTORY_SEPARATOR . $filePath;
	}

	protected function doAction()
	{
		$destPath = $this->getTempThumbnailPath();
		$entry = $this->source->getEntry();
		$success = myEntryUtils::captureThumbUsingPackager($entry, $destPath, $this->second, $flavorAssetId, $this->newWidth, $this->newHeight);
		if(!$success)
		{
			throw new KalturaAPIException(KalturaThumbnailErrors::VID_SEC_FAILED);
		}

		return new fileSource($destPath . KThumbnailCapture::TEMP_FILE_POSTFIX);
	}
}
<?php
/**
 * @package plugins.thumbnail
 * @subpackage model.sourceAction
 */

abstract class kVidAction extends kSourceAction
{
	protected static $action_name = "abstract action";
	protected $second;
	protected $newWidth;
	protected $newHeight;

	protected function initParameterAlias()
	{
		$kVidAlias = array(
			"w" => kThumbnailParameterName::WIDTH,
			"h" => kThumbnailParameterName::HEIGHT,
		);
		$this->parameterAlias = array_merge($this->parameterAlias, $kVidAlias);
	}

	protected function extractActionParameters()
	{
		$this->newWidth = $this->getIntActionParameter(kThumbnailParameterName::WIDTH);
		$this->newHeight = $this->getIntActionParameter(kThumbnailParameterName::HEIGHT);
	}

	protected function validateInput()
	{
		if(!is_a($this->source, "kEntrySource"))
		{
			$data = array("errorString" => self::$action_name . "can only work on entry source");
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::BAD_QUERY, $data);
		}

		if($this->source->getEntryMediaType() != entry::ENTRY_MEDIA_TYPE_VIDEO)
		{
			throw new kThumbnailException(kThumbnailException::MUST_HAVE_VIDEO_SOURCE, kThumbnailException::MUST_HAVE_VIDEO_SOURCE);
		}

		$this->validateDimensions();
		$this->validatePermissions();
	}

	protected function validateDimensions()
	{
		if($this->newWidth && (!is_numeric($this->newWidth) || $this->newWidth < self::MIN_DIMENSION || $this->newWidth > self::MAX_DIMENSION))
		{
			$data = array("errorString" => 'width must be between 0 and 10000');
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::BAD_QUERY, $data);
		}

		if($this->newHeight && (!is_numeric($this->newHeight) || $this->newHeight < self::MIN_DIMENSION || $this->newHeight > self::MAX_DIMENSION))
		{
			$data = array("errorString" => 'height must be between 0 and 10000');
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::BAD_QUERY, $data);
		}
	}

	protected function validatePermissions()
	{
		kThumbnailSecurityHelper::verifyEntryAccess($this->source->getEntry());
	}

	protected function getTempThumbnailPath()
	{
		$dc = kDataCenterMgr::getCurrentDc();
		$filePath = $dc["id"].'_'.kString::generateStringId();
		return sys_get_temp_dir().DIRECTORY_SEPARATOR . $filePath;
	}
}
<?php
/**
 * @package plugins.thumbnail
 * @subpackage model.sourceAction
 */

class kVidSecAction extends kVidAction
{
	protected static $action_name = "vid sec action";
	protected $second;

	protected function initParameterAlias()
	{
		parent::initParameterAlias();
		$kVidSecAlias = array(
			"sec" => kThumbnailParameterName::SECOND,
			"s" => kThumbnailParameterName::SECOND,
		);
		$this->parameterAlias = array_merge($this->parameterAlias, $kVidSecAlias);
	}

	protected function extractActionParameters()
	{
		parent::extractActionParameters();
		$this->second = $this->getFloatActionParameter(kThumbnailParameterName::SECOND, 0);
	}

	protected function validateInput()
	{
		parent::validateInput();
		if(!is_numeric($this->second) || $this->second < 0)
		{
			$data = array("errorString" => "Second cant be negative");
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::BAD_QUERY, $data);
		}
	}

	protected function doAction()
	{
		$destPath = $this->getTempThumbnailPath();
		$entry = $this->source->getEntry();
		$success = myEntryUtils::captureThumbUsingPackager($entry, $destPath, $this->second, $flavorAssetId, $this->newWidth, $this->newHeight);
		if(!$success)
		{
			$data = array("errorString" => self::$action_name . " failed");
			throw new kThumbnailException(kThumbnailException::ACTION_FAILED, kThumbnailException::ACTION_FAILED, $data);
		}

		return new kFileSource(KThumbnailCapture::getCapturePath($destPath));
	}
}
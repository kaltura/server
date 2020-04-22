<?php
/**
 * @package core
 * @subpackage thumbnail.sourceAction
 */

class kVidSecAction extends kVidAction
{
	protected static $action_name = "vid sec action";
	protected $second;

	protected function initParameterAlias()
	{
		parent::initParameterAlias();
		$kVidSecAlias = array(
			'sec' => kThumbnailParameterName::SECOND,
			's' => kThumbnailParameterName::SECOND,
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
			$data = array(kThumbnailErrorMessages::ERROR_STRING => kThumbnailErrorMessages::SECOND);
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::BAD_QUERY, $data);
		}
	}

	protected function doAction()
	{
		$destPath = $this->getTempThumbnailPath();
		$entry = $this->source->getEntry();
		$success = myPackagerUtils::captureThumbUsingPackager($entry, $destPath, $this->second, $flavorAssetId, $this->newWidth, $this->newHeight);
		if(!$success)
		{
			$data = array(kThumbnailErrorMessages::ERROR_STRING => self::$action_name . kThumbnailErrorMessages::FAILED);
			throw new kThumbnailException(kThumbnailException::ACTION_FAILED, kThumbnailException::ACTION_FAILED, $data);
		}

		return new kFileSource(KThumbnailCapture::getCapturePath($destPath));
	}
}
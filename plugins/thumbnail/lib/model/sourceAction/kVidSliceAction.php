<?php
/**
 * @package plugins.thumbnail
 * @subpackage model.sourceAction
 */

class kVidSliceAction extends kVidStripAction
{
	protected $sliceNumber;

	protected function initParameterAlias()
	{
		parent::initParameterAlias();
		$kVidSliceAlias = array(
			'sn' => kThumbnailParameterName::SLICE_NUMBER,
		);
		$this->parameterAlias = array_merge($this->parameterAlias, $kVidSliceAlias);
	}

	protected function extractActionParameters()
	{
		parent::extractActionParameters();
		$this->sliceNumber = $this->getIntActionParameter(kThumbnailParameterName::SLICE_NUMBER);
	}

	protected function validateInput()
	{
		parent::validateInput();

		if (!$this->sliceNumber || $this->sliceNumber < 1 || $this->sliceNumber > $this->numberOfSlices)
		{
			$data = array(kThumbnailErrorMessages::ERROR_STRING => kThumbnailErrorMessages::SLICE_NUMBER);
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::BAD_QUERY, $data);
		}
	}

	protected function doAction()
	{
		$interval = $this->calculateInterval();
		$destPath = $this->getTempThumbnailPath();
		$second = $this->startSec + ($interval * $this->sliceNumber);
		$this->captureThumb($this->source->getEntry(), $destPath, $second);
		$source = new kFileSource(KThumbnailCapture::getCapturePath($destPath));
		if($this->rotation)
		{
			$source->getImage()->rotateImage(kRotateAction::DEFAULT_BG, $this->rotation);
		}

		return $source;
	}
}
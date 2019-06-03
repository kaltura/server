<?php
/**
 * @package plugins.thumbnail
 * @subpackage model.sourceAction
 */

class kVidStripAction extends kVidAction
{
	const DEFAULT_BGC = "none";
	protected $numberOfSlices;
	protected $startSec;
	protected $endSec;
	protected $newWidth;
	protected $newHeight;
	protected $videoLength;

	protected function initParameterAlias()
	{
		parent::initParameterAlias();
		$kVidStripAlias = array(
			"w" => kThumbnailParameterName::WIDTH,
			"h" => kThumbnailParameterName::HEIGHT,
			"numberofslices" => kThumbnailParameterName::NUMBER_OF_SLICES,
			"nos" => kThumbnailParameterName::NUMBER_OF_SLICES,
			"startsec" => kThumbnailParameterName::START_SEC,
			"ss" => kThumbnailParameterName::START_SEC,
			"endsec" => kThumbnailParameterName::END_SEC,
			"es" => kThumbnailParameterName::END_SEC,
		);
		$this->parameterAlias = array_merge($this->parameterAlias, $kVidStripAlias);
	}

	protected function extractActionParameters()
	{
		$this->initParameterAlias();
		$this->numberOfSlices = $this->getIntActionParameter(kThumbnailParameterName::NUMBER_OF_SLICES);
		$this->startSec = $this->getFloatActionParameter(kThumbnailParameterName::START_SEC, 0);
		$this->endSec = $this->getFloatActionParameter(kThumbnailParameterName::END_SEC);
		$this->newWidth = $this->getIntActionParameter(kThumbnailParameterName::WIDTH);
		$this->newHeight = $this->getIntActionParameter(kThumbnailParameterName::HEIGHT);
	}

	protected function validateInput()
	{
		parent::validateInput();

		if(!$this->numberOfSlices || $this->numberOfSlices < 1)
		{
			$data = array("errorString" => "number of slices must have positive");
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::BAD_QUERY, $data);
		}

		if($this->startSec < 0)
		{
			$data = array("errorString" => "start sec must have a positive");
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::BAD_QUERY, $data);
		}

		if($this->endSec && $this->endSec <= $this->startSec)
		{
			$data = array("errorString" => "end sec must be greater then start sec");
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::BAD_QUERY, $data);
		}
	}

	protected function doAction()
	{
		$sizeInitialized = false;
		$width = 0;
		$strip = null;
		$interval = $this->calculateInterval();
		for($i = 0; $i < $this->numberOfSlices; $i++)
		{
			$destPath = $this->getTempThumbnailPath();
			$second = $this->startSec + ($interval * $i);
			$success = myEntryUtils::captureThumbUsingPackager($this->source->getEntry(), $destPath, $second, $flavorAssetId, $this->newWidth, $this->newHeight);
			if(!$success)
			{
				$data = array("errorString" => "Vid strip failed");
				throw new kThumbnailException(kThumbnailException::ACTION_FAILED, kThumbnailException::ACTION_FAILED, $data);
			}

			$sliceToAdd = new Imagick($destPath . KThumbnailCapture::TEMP_FILE_POSTFIX);
			if(!$sizeInitialized)
			{
				$width = $sliceToAdd->getImageWidth();
				$strip = $sliceToAdd;
				$strip->setImageExtent($width * $this->numberOfSlices, $sliceToAdd->getImageHeight());
				$sizeInitialized = true;
			}
			else
			{
				$strip = $this->concatImages($strip, $sliceToAdd, $i * $width);
			}
		}

		return new kImagickSource($strip);
	}

	/**
	 * @param Imagick $strip
	 * @param Imagick $sliceToAdd
	 * @param int $x
	 * @return Imagick
	 * @throws kThumbnailException
	 */
	protected function concatImages($strip, $sliceToAdd, $x)
	{
		if(!$strip->compositeImage($sliceToAdd, imagick::COMPOSITE_DEFAULT, $x, 0))
		{
			$data = array("errorString" => 'Failed to compose image');
			throw new kThumbnailException(kThumbnailException::ACTION_FAILED, kThumbnailException::ACTION_FAILED, $data);
		}

		return $strip;
	}

	protected function calculateInterval()
	{
		/** @var entry $entry*/
		$entry = $this->source->getEntry();
		$videoLength = $entry->getLengthInMsecs();
		if(!$this->endSec)
		{
			$this->endSec = $videoLength / 1000;
		}
		else if($this->endSec * 1000 > $videoLength)
		{
			$data = array("errorString" => 'end sec cant be greater then the video length');
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::BAD_QUERY, $data);
		}

		$interval = ($this->endSec - $this->startSec) / $this->numberOfSlices;
		return $interval;
	}
}
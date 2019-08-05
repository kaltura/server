<?php
/**
 * @package core
 * @subpackage thumbnail
 */

class kThumbnailActionAdapter
{
	const UNSET_PARAMETER_ZERO_BASED = 0;
	const UNSET_PARAMETER = -1;

	/**
	 * @param entry $entry
	 * @param $version
	 * @param int $width
	 * @param int $height
	 * @param int $type
	 * @param string $bgColor
	 * @param int $src_w
	 * @param int $src_h
	 * @param float $vid_sec
	 * @param int $vid_slice
	 * @param int $vid_slices
	 * @param float $start_sec
	 * @param float $end_sec
	 * @param bool $stripProfiles
	 * @param float $quality
	 * @param string $format
	 * @param float $density
	 * @return kImageTransformation
	 */
	public static function getImageTransformation($entry, $version, $width, $height, $type, $bgColor, $src_w, $src_h,
												  $vid_sec, $vid_slice, $vid_slices, $start_sec, $end_sec, $stripProfiles, $quality, $format, $density)
	{
		$step = new kImageTransformationStep();
		self::createEntrySource($entry, $step);
		self::handleSourceActions($vid_sec, $vid_slice, $vid_slices, $start_sec, $end_sec, $step);
		self::prepareInput($bgColor, $src_h, $src_w, $entry);
		switch($type)
		{
			case 1:
				self::handleResize($width, $height, true, $step);
				break;
			case 2:
				if($width && $height)
				{
					self::handleResizeWithPadding($width, $height, $bgColor, $src_w, $src_h, $step);
				}
				else
				{
					self::handleResize($width, $height, false, $step);
				}

				break;
			case 3:
				self::handleCrop($width, $height, kCropGravityPoint::CENTER, $step);
				break;
			case 4:
				self::handleCrop($width, $height, kCropGravityPoint::TOP, $step);
				break;
			case 5:
				self::handleResize($width, $height, false, $step);
				break;
			case 6:
				break;
		}

		self::handleStrip($stripProfiles, $step);
		self::handleImageOutputAction($quality, $density, $format, $step);
		$transformation = new kImageTransformation();
		$transformation->addImageTransformationStep($step);
		return $transformation;
	}

	/**
	 * @param $gravityPoint
	 * @param kImageTransformationStep $step
	 */
	protected static function handleCrop($gravityPoint, $step)
	{
		$cropAction = new kCropAction();
		$cropAction->setActionParameter(kThumbnailParameterName::GRAVITY_POINT, $gravityPoint);
		$step->addAction($cropAction);
	}

	/**
	 * @param string $bgColor
	 * @param int $src_h
	 * @param int $src_w
	 * @param entry $entry
	 */
	protected static function prepareInput(&$bgColor, &$src_h, &$src_w, $entry)
	{
		$bgColor = sprintf('%06x', $bgColor);
		if (!$src_w)
		{
			$src_w = $entry->getWidth();
		}

		if (!$src_h)
		{
			$src_h = $entry->getHeight();
		}
	}

	/**
	 * @param int $width
	 * @param int $height
	 * @param int $src_w
	 * @param int $src_h
	 * @param string $bgColor
	 * @param kImageTransformationStep $step
	 */
	protected static function handleResizeWithPadding($width, $height, $src_w, $src_h, $bgColor, $step)
	{
		self::calculatePadding($width, $height,$src_w, $src_h, $borderHeight, $borderWidth);
		$reSizeAction = new kResizeAction();
		$reSizeAction->setActionParameter(kThumbnailParameterName::WIDTH, $width);
		$reSizeAction->setActionParameter(kThumbnailParameterName::HEIGHT, $height);
		$step->addAction($reSizeAction);
		$borderAction = new kBorderImageAction();
		$borderAction->setActionParameter(kThumbnailParameterName::BACKGROUND_COLOR, $bgColor);
		$borderAction->setActionParameter(kThumbnailParameterName::WIDTH, $borderWidth);
		$borderAction->setActionParameter(kThumbnailParameterName::HEIGHT, $borderHeight);
		$step->addAction($borderAction);
	}

	protected static function calculatePadding(&$width, &$height, $src_w, $src_h, &$borderHeight, &$borderWidth)
	{
		$borderWidth = 0;
		$borderHeight = 0;

		if($width * $src_h < $height * $src_w)
		{
			$w = $width;
			$h = ceil($src_h * ($width / $src_w));
			$borderHeight = ceil(($height - $h) / 2);
			if ($borderHeight * 2 + $h > $height)
			{
				$h--;
			}
		}
		else
		{
			$h = $height;
			$w = ceil($src_w * ($height / $src_h));
			$borderWidth = ceil(($width - $w) / 2);
			if ($borderWidth * 2 + $w > $width)
			{
				$w--;
			}
		}

		$width = $w;
		$height = $h;
	}

	/**
	 * @param bool $stripProfiles
	 * @param kImageTransformationStep $step
	 */
	protected static function handleStrip($stripProfiles, $step)
	{
		if($stripProfiles)
		{
			$stripAction = new kStripImageAction();
			$step->addAction($stripAction);
		}
	}

	/**
	 * @param int $width
	 * @param int $height
	 * @param bool $bestFit
	 * @param kImageTransformationStep $step
	 */
	protected static function handleResize($width, $height, $bestFit, $step)
	{
		$action = new kResizeAction();
		$action->setActionParameter(kThumbnailParameterName::WIDTH, $width);
		$action->setActionParameter(kThumbnailParameterName::HEIGHT, $height);
		$action->setActionParameter(kThumbnailParameterName::BEST_FIT, $bestFit);
		$step->addAction($action);
	}

	/**
	 * @param $quality
	 * @param $density
	 * @param $format
	 * @param kImageTransformationStep $step
	 */
	protected static function handleImageOutputAction($quality, $density, $format, $step)
	{
		$outputAction = new kImageOutputAction();
		if($quality != self::UNSET_PARAMETER_ZERO_BASED)
		{
			$outputAction->setActionParameter(kThumbnailParameterName::QUALITY, $quality);
		}

		if($quality != self::UNSET_PARAMETER_ZERO_BASED)
		{
			$outputAction->setActionParameter(kThumbnailParameterName::DENSITY, $density);
		}

		if($format)
		{
			$outputAction->setActionParameter(kThumbnailParameterName::FORMAT, $format);
		}

		$step->addAction($outputAction);
	}

	/**
	 * @param $vid_sec
	 * @param $vid_slice
	 * @param $vid_slices
	 * @param $start_sec
	 * @param $end_sec
	 * @param kImageTransformationStep $step
	 */
	protected static function handleSourceActions($vid_sec, $vid_slice, $vid_slices, $start_sec, $end_sec, $step)
	{
		if($vid_sec != self::UNSET_PARAMETER)
		{
			$sourceAction = new kVidSecAction();
			$sourceAction->setActionParameter(kThumbnailParameterName::SECOND, $vid_sec);
			$step->addAction($sourceAction);
		}
		else if($vid_slices != self::UNSET_PARAMETER)
		{
			if($vid_slice != self::UNSET_PARAMETER)
			{
				$sourceAction = new kVidSliceAction();
				$sourceAction->setActionParameter(kThumbnailParameterName::SLICE_NUMBER, $vid_slice);
				$sourceAction->setActionParameter(kThumbnailParameterName::NUMBER_OF_SLICES, $vid_slices);
				$sourceAction->setActionParameter(kThumbnailParameterName::START_SEC, $start_sec);
				$sourceAction->setActionParameter(kThumbnailParameterName::END_SEC, $end_sec);
				$step->addAction($sourceAction);
			}
			else
			{
				$sourceAction = new kVidStripAction();
				$sourceAction->setActionParameter(kThumbnailParameterName::NUMBER_OF_SLICES, $vid_slices);
				$sourceAction->setActionParameter(kThumbnailParameterName::START_SEC, $start_sec);
				$sourceAction->setActionParameter(kThumbnailParameterName::END_SEC, $end_sec);
				$step->addAction($sourceAction);
			}
		}
	}

	/**
	 * @param entry $entry
	 * @param kImageTransformationStep $step
	 */
	protected static function createEntrySource($entry, $step): void
	{
		$source = new kEntrySource();
		$source->setEntry($entry);
		$step->setSource($source);
	}
}
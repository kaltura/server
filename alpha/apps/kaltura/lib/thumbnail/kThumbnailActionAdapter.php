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
	 * @param $entry
	 * @param $version
	 * @param $width
	 * @param $height
	 * @param $type
	 * @param $bgcolor
	 * @param $src_x
	 * @param $src_y
	 * @param $src_w
	 * @param $src_h
	 * @param $vid_sec
	 * @param $vid_slice
	 * @param $vid_slices
	 * @param $start_sec
	 * @param $end_sec
	 * @param $stripProfiles
	 * @param $thumbParams
	 * @param $quality
	 * @param $format
	 * @param $density
	 * @return kImageTransformation
	 */
	public static function getImageTransformation($entry, $version, $width, $height, $type, $bgcolor, $src_x, $src_y, $src_w, $src_h,
												  $vid_sec, $vid_slice, $vid_slices, $start_sec, $end_sec, $stripProfiles, $thumbParams, $quality, $format, $density)
	{
		$step = new kImageTransformationStep();
		self::createEntrySource($entry, $step);
		switch($type)
		{

		}

		$outputAction = self::getImageOutputAction($quality, $density, $format);
		$step->addAction($outputAction);
		$transformation = new kImageTransformation();
		$transformation->addImageTransformationStep($step);
		return $transformation;
	}

	protected static function getImageOutputAction($quality, $density, $format)
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

		return $outputAction;
	}

	/**
	 * @param $entry
	 * @param $step
	 */
	protected static function createEntrySource($entry, $step): void
	{
		$source = new kEntrySource();
		$source->setEntry($entry);
		$step->addAction($source);
	}
}
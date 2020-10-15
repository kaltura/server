<?php


class KThumbnailCapture
{

	const PLAYLIST_TYPE = 5;
	const TEMP_FILE_POSTFIX = "temp_1.jpg";

	public static function shouldResizeByPackager($params, $type, $dimension)
	{
		//check if all null or 0
		$canBeHandle = (count(array_filter($params)) == 0);
		// check if only one dimension is given or type 5 (stretches to the exact dimensions)
		$positiveDimension = array_filter($dimension, function ($v) {return $v > 0;});
		$validDimension = ($type == 5) || (count($positiveDimension) == 1);
		return ($canBeHandle && $validDimension);
	}


	public static function generateThumbUrlWithOffset($url, $calc_vid_sec, $packagerCaptureUrl, $capturedThumbPath, $width = null, $height = null, $offsetPrefix = '', $postFix = '')
	{
		$offset = floor($calc_vid_sec * 1000);
		if ($width)
			$offset .= "-w$width";
		if ($height)
			$offset .= "-h$height";

		$packagerThumbCapture = str_replace(
			array("{url}", "{offset}"),
			array($url, $offsetPrefix . $offset),
			$packagerCaptureUrl) . $postFix;

		$tempThumbPath = self::getCapturePath($capturedThumbPath);
		return array($packagerThumbCapture, $tempThumbPath);
	}

	public static function getCapturePath($path)
	{
		return $path . self::TEMP_FILE_POSTFIX;
	}
}

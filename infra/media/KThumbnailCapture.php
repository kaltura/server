<?php


class KThumbnailCapture
{

	const PLAYLIST_TYPE = 5;
	const TEMP_FILE_POSTFIX = "temp_1.jpg";

	public static function curlThumbUrlWithOffset($url, $calc_vid_sec, $packagerCaptureUrl, $capturedThumbPath, $width = null, $height = null, $offsetPrefix = '')
	{
		$offset = floor($calc_vid_sec*1000);
		if ($width)
			$offset .= "-w$width";
		if ($height)
			$offset .= "-h$height";

		$packagerThumbCapture = str_replace(
			array ( "{url}", "{offset}" ),
			array ( $url , $offsetPrefix . $offset ) ,
			$packagerCaptureUrl );

		$tempThumbPath = $capturedThumbPath.self::TEMP_FILE_POSTFIX;
		kFile::closeDbConnections();

		$success = KCurlWrapper::getDataFromFile($packagerThumbCapture, $tempThumbPath, null, true);
		return $success;
	}

	public static function captureLocalThumbForBifUsingPackager($srcPath, $capturedThumbPath, $calc_vid_sec, $width = null, $height = null)
	{
		$packagerCaptureUrl = kConf::get('packager_local_thumb_capture_url', 'local', null);
		if (!$packagerCaptureUrl || !$srcPath)
		{
			return false;
		}
		$srcPath = strstr($srcPath, 'content');

		return self::curlThumbUrlWithOffset($srcPath, $calc_vid_sec, $packagerCaptureUrl, $capturedThumbPath, $width, $height);
	}

	public static function shouldResizeByPackager($params, $type, $dimension)
	{
		//check if all null or 0
		$canBeHandle = (count(array_filter($params)) == 0);
		// check if only one dimension is given or type 5 (stretches to the exact dimensions)
		$positiveDimension = array_filter($dimension, function ($v) {return $v > 0;});
		$validDimension = ($type == 5) || (count($positiveDimension) == 1);
		return ($canBeHandle && $validDimension);
	}
}

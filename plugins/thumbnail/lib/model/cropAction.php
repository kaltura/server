<?php
/**
* @package plugins.thunmbnail
* @subpackage model
*/

class cropAction
{

	function validateInput()
	{

	}

	function thumbnail($image, $new_w, $new_h, $focus = kCropGravityPoint::CENTER)
	{
		$image->setImagePage(0, 0, 0, 0);
		$w = $image->getImageWidth();
		$h = $image->getImageHeight();

		if ($w > $h)
		{
			$resize_w = $w * $new_h / $h;
			$resize_h = $new_h;
		}
		else
		{
			$resize_w = $new_w;
			$resize_h = $h * $new_w / $w;
		}

		$image->resizeImage($resize_w, $resize_h, Imagick::FILTER_LANCZOS, 0.9);

		switch ($focus) {
			case kCropGravityPoint::TOP:
				$image->cropImage($new_w, $new_h, 0, 0);
				break;

			case kCropGravityPoint::CENTER:
				$image->cropImage($new_w, $new_h, ($resize_w - $new_w) / 2, ($resize_h - $new_h) / 2);
				break;
		}
	}
}

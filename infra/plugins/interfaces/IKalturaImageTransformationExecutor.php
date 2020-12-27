<?php
/**
 * Enable plugin to execute ImageTransformation
 * @package infra
 * @subpackage Plugins
 */

interface IKalturaImageTransformationExecutor extends IKalturaBase
{
	public function getImageFile($entry, $version, $width, $height, $type, $bgcolor, $quality, $src_x, $src_y, $src_w, $src_h,
							 $vid_sec, $vid_slice, $vid_slices, $orig_image_path, $density, $stripProfiles, $format, $fileSync, $start_sec, $end_sec);
}
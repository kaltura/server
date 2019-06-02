<?php
/**
 * @package plugins.thumbnail
<<<<<<< HEAD
 * @subpackage model
=======
 * @subpackage model.thumbStorage
>>>>>>> bc2267b517dd08ee9a78c282f90b0796fa25ad58
 */

class kThumbStorageFactory
{
	static function getInstance($type=kThumbStorageType::NONE)
	{
		$out = null;
		switch($type)
		{
			case kThumbStorageType::S3:
				$out = new kThumbStorageS3();
			break;
			case kThumbStorageType::LOCAL:
				$out = new kThumbStorageLocal();
			break;
			case kThumbStorageType::NONE:
			default:
				$out = new kThumbStorageNone();
			break;
		}
		return $out;
	}
}
<?php
/**
 * @package core
 * @subpackage thumbnail.thumbStorage
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
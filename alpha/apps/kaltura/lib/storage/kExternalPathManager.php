<?php
class kExternalPathManager extends kPathManager
{
	/**
	 * will return a pair of file_root and file_path
	 * This is the only function that should be extended for building a different path
	 *
	 * @param ISyncableFile $object
	 * @param int $subType
	 * @param $version
	 */
	public function generateFilePathArr(ISyncableFile $object, $subType, $version = null)
	{
		$dateDir = date('Ymd');
		$partnerDir = floor($object->getPartnerId() / 1000);
		$path = "$dateDir/$partnerDir/" . $object->generateFileName($subType, $version);
		$root = '/';
		return array($root, $path);
	}
}